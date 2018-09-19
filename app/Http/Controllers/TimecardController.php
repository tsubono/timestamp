<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimecardRequest;
use App\Http\Services\EmployeeService;
use App\Http\Services\TimecardService;
use App\Models\Employee;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;
use Illuminate\Http\JsonResponse;


/*
 * タイムカードコントローラー
 */
class TimecardController extends Controller
{
    use ApiResponseTrait;
    use ExpirationCheckTrait;

    /*
     * タイムカード一覧表示
     */
    public function getIndex(Request $request)
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        //対象期間のタイムカード一覧取得
        $year = $request->get('year');
        $month = $request->get('month');
        //対象期間の指定がない場合は現在を設定
        if (empty($year) && empty($month)) {
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
        }
        $start_date = Carbon::parse($year."-".$month."-"."01");
        $end_date = Carbon::parse($year."-".$month."-"."01")->endOfMonth();
        $timecards = TimecardService::getTimecardsForList($start_date, $end_date);

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'timecards' => $timecards,
            'year' => $year,
            'month' => $month,
            'date' => $start_date,
            'employees' => EmployeeService::getEmployees()['employees'],
            'controls' => Timecard::getControls(),
        ];

        return view('timecard.index', array_merge($messages, $params));
    }

    /*
     * タイムカード詳細
     */
    public function getDetail($subdomain, $id) {

        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        //対象のタイムカード取得
        $timecard = Timecard::where('id', $id)->first();
        if (empty($timecard)) {
            return redirect('/timecard');
        }

        //タイムカードに紐づく従業員
        $employee = Employee::ofEmployee($timecard->employee_uid)->first();
        //一覧に表示用のタイムカード一覧
        $all_record = TimecardDetail::ofTimecard($timecard->id)->orderBy('id', 'asc')->get();
        //編集モーダルに表示用のタイムカード一覧
        $details = TimecardService::getRecordsForEdit($timecard->id);
        //タイムカード情報を集計したもの
        $timeCardInfo = TimecardService::getTimecardInfoDaily($timecard->date, $timecard->employee_uid);

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'timecard' => $timecard,
            'employee' => $employee,
            'all_record' => $all_record,
            //'recordsJson' => $this->getRecordsJson($employee, $timecard),
            'details' => $details,
            'is_clocking_out' => TimecardDetail::isClockingOut($timecard->id),
            'controls' => Timecard::getControls(),
            'time_card_info' => $timeCardInfo,
            'employee_flg' => false,
        ];

        return view('/timecard.detail', array_merge($messages, $params));
    }

    /*
     * タイムカード新規追加
     */
    public function postAddTimecard(TimecardRequest $request) {

        $data = $request->all();

        //バリデート
        $error = TimecardService::validateForCreate($data['timecard_id'], $data['time'], $data['control_id']);
        if (!empty($error)) {
            return $request->response(['error' => $error]);
        }

        //登録処理
        $res = TimecardService::save($data['timecard_id'], $data['employee_uid'], $data['time'], $data['control_id']);

        if ($res) {
            session(['message' => 'タイムカードを更新しました。']);
        } else {
            session(['err_message' => 'タイムカードを更新できませんでした。']);
        }

        $year = Carbon::parse($data['time'])->format('Y');
        $month = Carbon::parse($data['time'])->format('m');

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/timecard/?year='.$year.'&month='.$month],
        ];

    }

    /*
     * タイムカード編集
     */
    public function postEditTimecard(Request $request)
    {
        $data = $request->all();

        if ($data['ajaxFlg']??false) {
            //データ整形
            $data['details'] = $this->formatTimecardEditData($data['details']);
        }

        //バリデート
        $error = TimecardService::validateForEdit($data['id'], $data['employee_uid'], $data['details']);
        if (!empty($error)) {
            return $this->responseBad($error);
        }

        //更新処理
        $res = TimecardService::update($data['id'], $data['employee_uid'], $data['details']);
        if ($res) {
            session(['message' => 'タイムカードを更新しました。']);
            return $this->responseOk("",
                [
                    "id" => $res,
                    "employee_flg" => $data["employee_flg"],
                    "employee_uid" => $data["employee_uid"],
                ]
            );
        } else {
            return $this->responseBad("タイムカードを更新できませんでした");
        }
    }


    private function formatTimecardEditData($details) {

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

    /*
     * angularJSに渡すデータを作成
     */
    private function getRecordsJson($employee, $timecard)
    {
        return json_encode([
            "id" => $timecard->id??"",
            "employee_uid" => $employee->uid??"",
            "details" => TimecardService::getRecordsForEdit($timecard->id??0)??[],
            'employee_flg' => false,
        ]);
    }

    /*
     * タイムカード削除
     */
    public function postDeleteTimecard(Request $request) {

        $data = $request->all();

        //削除処理
        $res = TimecardService::delete($data['id']);

        if ($res) {
            session(['message' => 'タイムカードを削除しました。']);
        } else {
            session(['err_message' => 'タイムカードを削除できませんでした。']);
        }

        //パラメータによって戻る画面を分岐
        if (!$data['employee_flg']) {
            $location = '/timecard/?year=' . $data['year'] . '&month=' . $data['month'];
        } else {
            $location = '/employee/'.$data['employee_uid'];
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => $location],
        ];
    }

    /*
     * 操作可能なコントロール(出勤・休憩入り・休憩戻り・退勤)を取得
     */
    public function ajaxGetEnableControl(Request $request) {

        $data = $request->all();

        $res = [];
        if (!empty($data['time']) && !empty($data['employee_uid'])) {
            $res = TimecardService::getEnableControl($data['time'], $data['employee_uid'], $data['employee_flg']??false);
        }
        echo json_encode($res);

    }

    /*
     * 新規打刻可能な従業員を取得
     */
    public function ajaxGetEnableEmployee(Request $request) {

        $data = $request->all();

        $res = [];
        if (!empty($data['time'])) {
            $res = TimecardService::getEnableEmployee($data['time']);
        }
        echo json_encode($res);
    }

    /*
     * セッションメッセージを取得
     */
    private function getMessages() {
        $message = session('message');
        $err_message = session('err_message');
        session()->forget('message');
        session()->forget('err_message');

        return compact("message","err_message");
    }

    /*
     * タイムカード詳細情報を取得(ajax)
     */
    public function ajaxGetTimecardDetails(Request $request) {

        $data = $request->all();
        $employee = Employee::where('uid', $data['employee_uid'])->first();
        $timecard = Timecard::where('id', $data['timecard_id'])->first();

        echo $this->getRecordsJson($employee, $timecard);


    }

}
