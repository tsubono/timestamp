<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\IconRequest;
use App\Http\Requests\TimecardRequest;
use App\Http\Services\EmployeeService;
use App\Http\Services\SalaryService;
use App\Http\Services\TimecardService;
use App\Models\Employee;
use App\Models\Icon;
use App\Models\Recorder;
use App\Models\Salary;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Log;
use Illuminate\Http\JsonResponse;

/*
 * 従業員画面コントローラー
 */
class EmployeeController extends Controller
{
    use ExpirationCheckTrait;
    use ApiResponseTrait;

    /*
     * 従業員一覧表示
     */
    public function getIndex()
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $messages = $this->getMessages();
        $params = $this->getParams();

        return view('employee.index', array_merge($messages, $params));
    }

    /*
     * 従業員詳細表示
     */
    public function getDetail(Request $request, $subdomain, $uid) {

        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        //従業員取得
        $employee = Employee::where('uid', $uid)->first();
        if (empty($employee)) {
            return redirect('/employee');
        }

        //対象年月のタイムカード一覧取得
        $year = $request->get('year');
        $month = $request->get('month');
        if (empty($year) && empty($month)) {
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
        }
        $start_date = Carbon::parse($year."-".$month."-"."01");
        $end_date = Carbon::parse($year."-".$month."-"."01")->endOfMonth();
        $timecards = TimecardService::getTimecardsForList($start_date, $end_date, $uid);

        //アイコン一覧取得
 //       $icon_list = [];
//        foreach (range(1, 108) as $no) {
//            $num = sprintf('%03d', $no);
//            $icon_list[$num] = "/profile_icon/{$num}.png";
//        }
        $icon_list = Icon::all();

        $is_clock_out = true;
        $current_status = EmployeeService::getCurrentStatus($uid, Auth::user()->workplace_uid);
        if ($current_status!="待機中") {
            $is_clock_out = false;
        }

        $params = $this->getParams();
        $params = array_merge($params, [
            'employee' => $employee,
            'timecards' => $timecards,
            'year' => $year,
            'month' => $month,
            'date' => $start_date,
            'salaries' => Salary::getSalaries($uid),
            'icon_list' => $icon_list,
            'controls' => Timecard::getControls(),
            'is_clock_out' => $is_clock_out,
        ]);

        $messages = $this->getMessages();

        return view('employee.detail', array_merge($messages, $params));
    }

    /*
     * 従業員新規追加処理
     */
    public function postAddEmployee(EmployeeRequest $request) {

        $data = $request->all();
        //追加処理
        $res = EmployeeService::save($data);

        if ($res) {
            session(['message' => '従業員を追加しました。']);
        } else {
            session(['err_message' => '従業員を追加できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/employee'],
        ];
    }

    /*
     * 従業員情報更新処理
     */
    public function postEditEmployee(EmployeeRequest $request) {

        $data = $request->all();
        //更新処理
        $res = EmployeeService::update($data);

        if ($res) {
            session(['message' => '従業員情報を更新しました。']);
        } else {
            session(['err_message' => '従業員情報を更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/employee/'.$data['uid']],
        ];
    }

    /*
     * 従業員削除
     */
    public function postDeleteEmployee(Request $request) {

        //削除処理
        $res = EmployeeService::delete($request->get('uid'));

        if ($res) {
            session(['message' => '従業員を削除しました。']);
        } else {
            session(['err_message' => '従業員を削除できませんでした。']);
        }

        return redirect('/employee');
    }

    /*
     * アイコン更新
     */
    public function postEditIcon(IconRequest $request) {

        $data = $request->all();

        //ファイルアップロードの場合
        if ($data['icon_type']=="icon_file") {

            $file_name = $data['uid'].'.png';

            if(!file_exists(public_path().'/storage/profile_icon/')){
                mkdir(public_path().'/storage/profile_icon/');
            }

            //いまはstorageに
            // Original (無加工)
            if(!file_exists(public_path().'/storage/profile_icon/original/')){
                mkdir(public_path().'/storage/profile_icon/original/');
            }
            Image::make($request->file('icon_file'))->save(public_path().'/storage/profile_icon/original/'.$file_name);

            // レコーダーの一覧用 (86px * 103px)
            if (!file_exists(public_path().'/storage/profile_icon/recorder/')) {
                mkdir(public_path().'/storage/profile_icon/recorder/');
            }
            Image::make($request->file('icon_file'))->fit(86, 103)->save(public_path().'/storage/profile_icon/recorder/'.$file_name);

            // 従業員詳細のアイコン用 (100px * 120px)
            if (!file_exists(public_path().'/storage/profile_icon/thumbnail/')) {
                mkdir(public_path().'/storage/profile_icon/thumbnail/');
            }
            Image::make($request->file('icon_file'))->fit(100, 120)->save(public_path().'/storage/profile_icon/thumbnail/'.$file_name);

            // (200 * 240)
            if (!file_exists(public_path().'/storage/profile_icon/original_mini/')) {
                mkdir(public_path().'/storage/profile_icon/original_mini/');
            }
            Image::make($request->file('icon_file'))->fit(200, 240)->save(public_path().'/storage/profile_icon/original_mini/'.$file_name);


            $res = EmployeeService::updateIcon($data['uid'], $data['icon_type'], $file_name);
        //既存アイコンから選択の場合
        } else {
            $res = EmployeeService::updateIcon($data['uid'], $data['icon_type'], $data['icon']);
        }

        if ($res) {
            session(['message' => 'アイコンを更新しました。']);
        } else {
            session(['err_message' => 'アイコンを更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/employee/'.$data['uid']],
        ];
    }

    /*
     * 給与設定
     */
    public function postAddSalary(Request $request) {

        $data = $request->all();
        $data['records'] = [];

        //データ整形
        foreach ($data['start_time'] as $idx => $item) {
            $data['records'][$idx]['start_time'] = $item;
            $data['records'][$idx]['hourly_pay'] = $data['hourly_pay'][$idx];

            if (count($data['start_time'])==2 && $idx==1) {
                if (empty($data['records'][$idx]['start_time']) &&
                        empty($data['records'][$idx]['hourly_pay'])) {
                    unset($data['records'][$idx]);
                }
            }
        }
        //バリデーション
        $res = SalaryService::validate($data['records']);
        if (!$res['result']) {
            return new JsonResponse([
                "errors" => $res['message']
            ],400);

        } else {
            //更新処理
            $res = SalaryService::save($data);

            if ($res) {
                session(['message' => '給与設定を更新しました。']);
            } else {
                session(['err_message' => '給与設定を更新できませんでした。']);
            }
            return [
                'status_code' => 200,
                'payloads' => ['location' => '/employee/'.$data['uid']],
            ];
        }
    }

    /*
     * 給与削除
     */
    public function postDeleteSalary(Request $request, $subdomain) {

        $data = $request->all();

        //削除処理
        $res = SalaryService::delete($data);
        if ($res) {
            session(['message' => '給与設定を削除しました。']);
        } else {
            session(['err_message' => '給与設定を削除できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/employee/'.$data['uid']],
        ];
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
            'payloads' => ['location' => '/employee/'.$data['employee_uid'].'?year='.$year.'&month='.$month],
        ];
    }

    /*
     * タイムカード詳細
     */
    public function getDetailTimecard($subdomain, $employee_uid, $id) {

        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $employee = Employee::where('uid', $employee_uid)->first();

        $timecard = Timecard::where('id', $id)->first();
        if (empty($timecard)) {
            return redirect('/employee/'.$employee_uid);
        }

        //一覧に表示用のタイムカード一覧
        $all_record = TimecardDetail::ofTimecard($timecard->id)->orderBy('id', 'asc')->get();
        //編集モーダルに表示用のタイムカード一覧
        $details = TimecardService::getRecordsForEdit($timecard->id);
        //タイムカード情報を集計したもの
        $timeCardInfo = TimecardService::getTimecardInfoDaily($timecard->date, $timecard->employee_uid);

        $params = $this->getParams();
        $params = array_merge($params, [
            'timecard' => $timecard,
            'employee' => $employee,
            'all_record' => $all_record,
            'recordsJson' => $this->getRecordsJson($employee, $timecard),
            'details' => $details,
            'employee_flg' => true,
            'time_card_info' => $timeCardInfo,
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'is_clocking_out' => TimecardDetail::isClockingOut($timecard->id),
            'controls' => Timecard::getControls(),
        ]);

        $messages = $this->getMessages();

        return view('/timecard.detail', array_merge($messages, $params));

    }

    /*
     * タイムカード詳細(angulaJS)に渡す用のデータ作成
     */
    private function getRecordsJson($employee, $timecard)
    {
        return json_encode([
            "id" => $timecard->id,
            "employee_uid" => $employee->uid,
            "details" => TimecardService::getRecordsForEdit($timecard->id),
            'employee_flg' => true,
        ]);
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
     * VIEWに渡す共通変数をまとめて返す
     */
    private function getParams() {
        $employeeData = EmployeeService::getEmployees();

        return [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'employees' => $employeeData['employees'],
            'resigned_employees' => $employeeData['resigned_employees'],
        ];


    }
}
