<?php
namespace App\Http\Services;
use App\Models\Affiliation;
use App\Models\Employee;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use Carbon\Carbon;
use DB;
use Log;
use Auth;
use Exception;

/*
 * 従業員関連を扱うサービス
 */
class EmployeeService
{
    /*
     * 従業員登録
     */
    public static function save($data) {

        if (empty($data['birthday'])) {
            $data['birthday'] = NULL;
        }
        if (empty($data['joined_date'])) {
            $data['joined_date'] = NULL;
        }

        //データ登録
        DB::connection('customer-db')->transaction(function () use ($data){
            try {
                //従業員
                $employee = new Employee();
                $data['uid'] = $employee->create_uid;
                $employee->fill($data);
                $employee->save();
                //所属
                $affiliation = new Affiliation();
                $affiliation->workplace_uid = Auth::user()->workplace_uid;
                $affiliation->employee_uid = $data['uid'];
                $affiliation->save();

            } catch (Exception $e) {
                return false;
            }
        });
        return true;
    }

    /*
     * 従業員更新
     */
    public static function update($data) {

        if (empty($data['birthday'])) {
            $data['birthday'] = NULL;
        }
        if (empty($data['joined_date'])) {
            $data['joined_date'] = NULL;
        }
        if (empty($data['resigned_date'])) {
            $data['resigned_date'] = NULL;
        }

        try {
            $employee = Employee::where('uid', $data['uid'])->first();
            $employee->fill($data);
            $employee->save();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /*
     * アイコン更新
     */
    public static function updateIcon($employee_uid, $icon_type, $path) {

        try {
            $employee = Employee::where('uid', $employee_uid)->first();
            $employee->icon = $path;
            $employee->icon_type = $icon_type;
            $employee->save();

        } catch (Exception $e) {
            return false;
        }

        return true;

    }

    /*
     * 従業員削除
     */
    public static function delete($uid) {

        try {
            $employee = Employee::where('uid', $uid)->first();
            if (!empty($employee)) {
                $employee->delete();
            }

        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /*
     * 勤務場所に紐付いた従業員一覧取得
     */
    public static function getEmployees($workplace_uid=null) {

        //所属
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        $affiliations = Affiliation::ofWorkplace($workplace_uid)->orderBy('current_clock_in','desc')->get();


        $employees = [];
        $resigned_employees = [];

        $now = TimecardService::getDateByTiming(Carbon::now()->format('Y-m-d H:i'), $workplace_uid);
        $date = Carbon::parse($now)->format('Y-m-d');

        foreach ($affiliations as $affiliation) {
            $employee = $affiliation->employee;
            if (!empty($employee)) {
                //直近の出勤日
                $employee->current_date = self::getCurrentDate($employee->uid, $workplace_uid);
                //現在の出勤状態
                $employee->current_status = self::getCurrentStatus($employee->uid, $workplace_uid);

                //退職日がNULL = 就任中従業員
                if (empty($employee->resigned_date)) {
                    //従業員全体
                    $employees[] = $employee;

                //辞職済み従業員
                } else {
                    $resigned_employees[] = $employee;
                }
            }
        }
        return [
            'employees' => $employees,
            'resigned_employees' => $resigned_employees,
        ];
    }

    /*
     * 従業員数取得
     */
    public static function getEmployeeCount($workplace_uid=null) {

        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        //所属
        $affiliations = Affiliation::ofWorkplace($workplace_uid)->get();

        $count = 0;

        foreach ($affiliations as $affiliation) {
            $employee = $affiliation->employee;
            if (!empty($employee)) {
                $count++;
            }
        }
        return $count;
    }

    /*
     * 従業員の直近の出勤日取得
     */
    private static function getCurrentDate($uid, $workplace_uid=null) {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        $timecard = Timecard::ofEmployee($uid)->ofWorkplace($workplace_uid)->orderBy('date','desc')->first();

        if (!empty($timecard)) {
            return $timecard->date;
        } else {
            return NULL;
        }
    }

    /*
     * 従業員の現在の出勤状態取得
     */
    public static function getCurrentStatus($uid, $workplace_uid=null) {

        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $clock_flg = false;
        $status = "";

        $timecards = Timecard::ofEmployee($uid)->ofWorkplace($workplace_uid)->get();
        foreach ($timecards as $timecard) {
            $timecard_detail_work = TimecardDetail::ofTimecard($timecard->id)
                    ->whereNull('end_time')->where('type', '1')->first();
            $timecard_detail_rest = TimecardDetail::ofTimecard($timecard->id)
                    ->whereNull('end_time')->where('type', '2')->first();

            if (empty($timecard_detail_work) && empty($timecard_detail_rest)) {
                $status = '待機中';
            } elseif (!empty($timecard_detail_work) && empty($timecard_detail_rest)) {
                $status = '出勤中';
            } elseif (empty($timecard_detail_work) && !empty($timecard_detail_rest)) {
                $status = '休憩中';
            } elseif (!empty($timecard_detail_work) && !empty($timecard_detail_rest)) {
                $status = '休憩中';
            }
            if ($status != '待機中') {
                $clock_flg = true;
                break;
            }
        }

        if ($clock_flg) {
            return $status;
        } else {
            return '待機中';
        }
    }

    /*
     * 勤務場所に出勤中の従業員がいるかどうか
     */
    public static function isClockingEmployees($workplace_uid) {
        $res = false;

        $employees = self::getEmployees($workplace_uid);

        foreach ($employees['employees'] as $employee) {
            if ($employee->current_status != '待機中') {
                return true;
            }
        }
        foreach ($employees['resigned_employees'] as $employee) {
            if ($employee->current_status != '待機中') {
                return true;
            }
        }

        return $res;
    }

}