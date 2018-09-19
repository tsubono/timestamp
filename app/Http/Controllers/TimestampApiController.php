<?php
namespace App\Http\Controllers;

use App\Http\Services\ChangeRequestService;
use App\Http\Services\EmployeeService;
use App\Http\Services\TimecardService;
use App\Models\ChangeTimecard;
use App\Models\Employee;
use App\Models\Icon;
use App\Models\Recorder;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use Config;

/**
 * タイムスタンプ（webアプリ用)のAPIコントローラー
 */
class TimestampApiController extends Controller
{
    use ApiResponseTrait;

    /**
     * ステータス更新処理
     */
    public function postUpdateStatus($subdomain, Request $request, $uid)
    {

        $data = $request->all();
        $recorder = Recorder::where('uid', $uid)->first();
        if (empty($recorder)) {
            return $this->responseBad('レコーダーが不正です。');
        }
        $nowDateTime = Carbon::now()->format('Y-m-d H:i');

        $timecard_id = $request->get('timecard_id');
        $control_id = $request->get('control_id');

        //バリデート
        $error = TimecardService::validateForCreate($timecard_id, $nowDateTime, $control_id, $recorder->workplace_uid);
        if (!empty($error)) {
            return redirect('/timestamp/'.$uid)->withErrors(['error' => $error]);
        }

        //登録処理
        $res = TimecardService::save($timecard_id, $data['employee_uid'], $nowDateTime, $control_id, $recorder->workplace_uid);

        if (!$res) {
            return redirect('/timestamp/'.$uid)->withErrors(['error' => '更新できませんでした。']);
        }

        return redirect('/timestamp/'.$uid);
    }


    /*
     * タイムカード削除依頼
     */
//    public function deleteRequest($subdomain, Request $request, $uid, $timecard_id) {
//        $data = $request->all();
//        $recorder = Recorder::where('uid', $uid)->first();
//
//        //変更依頼テーブルに登録
//        $status = 3; //3は削除として扱う
//        $res = ChangeRequestService::update($timecard_id, [], $recorder->workplace_uid, $status);
//        if ($res) {
//            return redirect('/timestamp/'.$uid);
//        } else {
//            return $this->responseBad('削除依頼できませんでした。');
//        }
//
//    }


    /*
    * 従業員詳細情報を返す(ajax)
    */
    public function ajaxGetEmployeeDetails($subdomain, Request $request, $uid) {

        $data = $request->all();
        $res = [];

        $recorder = Recorder::where('uid', $data['recorder_uid'])->first();
        if (empty($recorder)) {
            return $this->responseBad('レコーダーが不正です。');

        }
        $workplace_uid = $recorder->workplace_uid;

        $employee = Employee::where('uid', $data['employee_uid'])->first();

        //アイコン取得
        $icon = "";
        if (!empty($employee->icon))
            if ($employee->icon_type=="icon") {
                $icon = Icon::getPath($employee->icon,'original_mini');
            } elseif ($employee->icon_type=="icon_file") {
                $icon = asset('storage'.Icon::getPath($employee->icon,'original_mini'));
            }

        $res['employee'] = [
            'name' => $employee->name,
            'icon' => $icon,
            'status' => EmployeeService::getCurrentStatus($employee->uid, $workplace_uid),
            'uid' => $employee->uid
        ];

        //初期化
        $res['control'] =  [
            'clocking_in' => false,
            'break_in' => false,
            'break_out' => false,
            'clocking_out' => false,
        ];

        //従業員の操作可能コントロール(出勤・休憩入り・休憩戻り・退勤)取得
        $controls = TimecardService::getEnableControl(Carbon::now()->format('Y-m-d H:i:s'), $employee->uid, false, $workplace_uid, true);
        foreach ($controls as $control) {
            foreach (Config::get('const.control_ids') as $key => $control_id) {
                if ($control_id == $control) {
                    $res['control'][$key] = true;
                }
            }
        }

        //現在日のタイムカード取得
        $timecard = Timecard::where('workplace_uid', $workplace_uid)->ofEmployee($employee->uid)
            ->where('date', Carbon::now()->format('Y-m-d'))->first();
        //最新のタイムカード取得
        $current = Timecard::getCurrentRecord($employee->uid, $workplace_uid);

        if (!empty($current)) {
            //出勤中の場合
            if (!TimecardDetail::isClockingOut($current->id)) {
                $res['timecard_id'] = $current->id;
            } else {
                if (!empty($timecard)) {
                    $res['timecard_id'] = $timecard->id;
                } else {
                    $res['timecard_id'] = 0;
                }
            }
        } else {
            if (!empty($timecard)) {
                $res['timecard_id'] = $timecard->id;
            } else {
                $res['timecard_id'] = 0;
            }
        }

        $res['token'] = csrf_token();


        return json_encode($res);

    }

    /*
     * タイムカード一覧情報を返す(ajax)
     */
    public function ajaxGetTimecardLists($subdomain, Request $request, $uid) {

        $data = $request->all();
        $year = $request->get("year") ?: Carbon::now()->format("Y");
        $month =$request->get("month") ?: Carbon::now()->format("m");

        //対象期間（一ヶ月単位）
        $start = $year.'-'.$month.'-01';
        $carbon_start_time = Carbon::parse($start);
        $end = Carbon::parse($start)->lastOfMonth()->format('Y-m-d');
        $carbon_end_time = Carbon::parse($end);

        $date_list = [];
        //その月の分の日リストを作成
        for ($s = $carbon_start_time;
             $s->format('Y-m-d') <= $carbon_end_time;
             $s = $s->modify('+1 day')) {

            $date_list[$s->format('Y-m-d')] = [];

            //追加依頼があるかどうか
            $date_list[$s->format('Y-m-d')]['add_request_flg']  = false;
            $change_timecard = ChangeTimecard::where('timecard_id', 0)->whereNull('status')
                            ->where('date', $s->format('Y-m-d'))->where('employee_uid', $data['employee_uid'])->first();
            if (!empty($change_timecard)) {
                $date_list[$s->format('Y-m-d')]['add_request_flg']  = true;
            }
        }

        $recorder = Recorder::where('uid', $uid)->first();
        $timecards = TimecardService::getTimecardsForList($start, $end, $data['employee_uid'], $recorder->workplace_uid);

        //既存のタイムカードがある場合は$date_listを更新
        foreach ($timecards as $timecard) {
            $date_list[$timecard->date]['clock_in'] = $timecard["first_time"];
            $date_list[$timecard->date]['clock_out'] = $timecard["last_time"];
            $date_list[$timecard->date]['timecardId'] = $timecard["timecard_id"];
            $date_list[$timecard->date]['records'] = TimecardService::getRecordsForEdit($timecard["timecard_id"]);
            $date_list[$timecard->date]['change_request_flg']  = false;

            //退勤済みの場合
            if (TimecardDetail::isClockingOut($timecard["timecard_id"])) {
                $date_list[$timecard->date]['clock_out_flg'] = true;

                $change_timecard = ChangeTimecard::where('timecard_id', $timecard["timecard_id"])->whereNull('status')->first();
                //変更申請を既にしている場合はフラグを立てる
                if (!empty($change_timecard)) {
                    $date_list[$timecard->date]['change_request_flg'] = true;
                }
            //未退勤の場合
            } else {
                $date_list[$timecard->date]['clock_out_flg'] = false;
            }
        }
        return json_encode([
            'dateList' => $date_list,
        ]);

    }

    /*
     * タイムカード詳細情報を返す(ajax)
     */
    public function ajaxGetTimecardDetails($subdomain, Request $request, $uid) {

        $data = $request->all();
        $recorder = Recorder::where('uid', $uid)->first();
        $employee = Employee::where('uid', $data['employee_uid'])->first();
        $timecard = Timecard::where('id', $data['timecard_id'])->first();

        if (!empty($timecard)) {
            $default_date = $timecard->date . " " . Workplace::getTimingOfTomorrow($recorder->workplace_uid);
        } else {
            $default_date = $data['date']. " ". Workplace::getTimingOfTomorrow($recorder->workplace_uid);
        }

        return json_encode([
            "id" => $timecard->id??"",
            "employee_uid" => $employee->uid??"",
            "details" => TimecardService::getRecordsForEdit($timecard->id??0)??[],
            'employee_flg' => false,
            'default_date_time' => $default_date
        ]);

    }

    /*
     * 変更・追加依頼実行
     */
    public function postChangeRequest($subdomain, Request $request, $uid) {

        $data = $request->all();

        $recorder = Recorder::where('uid', $uid)->first();

        //データ整形
        $data['records'] = $this->formatTimecardEditData($data['details']);

        if (empty($data['timecard_id'])) {
            $timecard_id = 0;
            //バリデート
            $error = ChangeRequestService::validateForEdit($timecard_id, $data, $recorder->workplace_uid, true);
        } else {
            $timecard_id = $data['timecard_id'];
            //バリデート
            $error = ChangeRequestService::validateForEdit($timecard_id, $data, $recorder->workplace_uid);
        }
        if (!empty($error)) {
            return $this->responseBad($error);
        }


        //変更依頼テーブルに登録
        $res = ChangeRequestService::update($timecard_id, $data, $recorder->workplace_uid);
        if ($res) {
            return redirect('/timestamp/'.$uid);
        } else {
            return $this->responseBad('変更依頼できませんでした。');
        }
    }

    private function formatTimecardEditData($details)
    {

        $res = [];
        $count = 0;
        foreach ($details as $detail) {

            if (!empty($detail['startTime'])) {
                $res[$count]['type'] = '0';
                $res[$count]['time'] = $detail['startTime'];
                $count++;
            }
            foreach ($detail['rests'] as $rest) {
                if (!empty($rest['startTime'])) {
                    $res[$count]['type'] = '1';
                    $res[$count]['time'] = $rest['startTime'];
                    $count++;
                }
                if (!empty($rest['endTime'])) {
                    $res[$count]['type'] = '2';
                    $res[$count]['time'] = $rest['endTime'];
                    $count++;
                }
            }
            if (!empty($detail['endTime'])) {
                $res[$count]['type'] = '3';
                $res[$count]['time'] = $detail['endTime'];
                $count++;
            }
        }
        return $res;
    }
}
