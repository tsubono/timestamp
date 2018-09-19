<?php
namespace App\Http\Services;
use App\Helpers\Excel\TimeCardsExcel;
use App\Helpers\ZipArchive;
use App\Models\Employee;
use App\Models\Timecard;
use App\Models\Workplace;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;
use Exception;
use Log;

/*
 * 出勤簿関連を扱うサービス
 */
class WorkingReportService
{
    /*
     * 出勤簿エクセル取得
     */
    public static function createReportExcel($data)
    {
        try {
            // Excelのフォーマット
            $excelHelper = new TimeCardsExcel();
            // Excel格納先Zip
            $zip = new ZipArchive();

            $timecardList = [];
            $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();

            //従業員指定なしの場合
            if (empty($data['employee_uid'])) {
                $employees = EmployeeService::getEmployees();

                if (count($employees) == 0) {
                    return ['error' => '従業員が存在しません。'];
                }
                foreach ($employees['employees'] as $idx => $employee) {
                    $timecardList[$idx]['employee_uid'] = $employee->uid;
                    $timecardList[$idx]['timecards'] = self::getTimecardsForWorkingReport($data, $employee->uid);
                }
            //従業員指定ありの場合
            } else {
                $timecardList[0]['employee_uid'] = $data['employee_uid'];
                $timecardList[0]['timecards'] = self::getTimecardsForWorkingReport($data, $data['employee_uid']);
            }

            $getFlg = false;
            //出勤簿作成
            foreach ($timecardList as $timecards) {
                $employee = Employee::ofEmployee($timecards['employee_uid'])->first();
                    foreach ($timecards['timecards'] as $record) {
                        $getFlg  = true;
                        //excelを作成
                        $excel = $excelHelper->getExcelFile($employee, $workplace, $record);

                        $date = Carbon::parse($record->date);
                        $fileName = "出勤簿{$date->format("Y")}年{$date->format("m")}月";

                        $zip->addExcel($excel, $fileName . $employee->name_kana . $employee->uid . ".xls");
                    }

            }

        } catch (Exception $e) {
            return ['error' => '出勤簿を作成できませんでした。'];
        }

        if (!$getFlg) {
            return ['error' => '指定条件のタイムカードが存在しません。'];
        }

        return $zip;
    }

    /*
     * 出勤簿用タイムカード一覧取得
     */
    public static function getTimecardsForWorkingReport($data, $employee_uid) {
        //タイムカード取得
        $query = Timecard::query();
        if (!empty($data['start_y'])) {
            if (!empty($data['start_m'])) {
                $date = $data['start_y'].'-'.$data['start_m'].'-01';
            } else {
                $date = $data['start_y'].'-'.'01-01';
            }
            $query->where('date', '>=', $date);
        }
        if (!empty($data['end_y'])) {
            if (!empty($data['end_m'])) {
                $date = $data['end_y'].'-'.$data['end_m'].'-31';
            } else {
                $date = $data['end_y'].'-'.'12-31';
            }
            $query->where('date', '<=', $date);
        }
        $query->where('employee_uid', $employee_uid);
        $query->where('workplace_uid', Auth::user()->workplace_uid);

        $query->orderBy('date', 'desc');
        $timecards = $query->get();

        return $timecards;

    }
}