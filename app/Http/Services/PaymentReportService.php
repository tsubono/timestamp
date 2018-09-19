<?php
namespace App\Http\Services;
use App\Helpers\Excel\PaymentExcel;
use App\Helpers\Excel\TimeCardsExcel;
use App\Helpers\ZipArchive;
use App\Models\Employee;
use App\Models\PaymentDeduction;
use App\Models\PaymentSupply;
use App\Models\Salary;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;
use Exception;
use Log;

/*
 * 給与明細関連を扱うサービス
 */
class PaymentReportService
{
    /*
     * 給与計算
     */
    public static function calcPayment($data)
    {
        try {
            $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();

            // 1ヶ月分の交通費を取得
            $data['traffic_cost'] = TimecardService::getTrafficCostMonthly($data['year'], $data['month'], $data['employee_uid']);
            // 1ヶ月分の時給別給与情報を取得
            $salaries = TimecardService::getTimecardInfoBySalary($data['year'], $data['month'], $data['employee_uid']);

            // 基本給計算
            $data['base_salary'] = 0;
            if (!$salaries) {
                return false;

            } else {
                foreach ($salaries as $salary) {
                    $hour = floor($salary['time'] / 60);
                    $minute = $salary['time'] % 60;
                    $salary["price"] = $hour * $salary['hourly_pay'];
                    $salary["price"] += $minute * number_format(($salary['hourly_pay'] / 60), 5, null, '');

                    //切り捨て
                    if ($workplace->payroll_role == "1") {
                        $salary["price"] = floor($salary["price"]);
                    //四捨五入
                    } elseif ($workplace->payroll_role == "2") {
                        $salary["price"] = round($salary["price"]);
                    }

                    $data['base_salary'] += $salary["price"];
                }
            }

            if (count($data['month'])==1) {
                $data['month'] = '0'.$data['month'];
            }
            $data['period'] = $data['year'].$data['month'];
            $data['workplace_uid'] = Auth::user()->workplace_uid;

            //明細（支給）テーブル更新
            $payment_supply = PaymentSupply::ofWorkplace(Auth::user()->workplace_uid)->where('employee_uid', $data['employee_uid'])
                                ->where('period', $data['year'].$data['month'])->first();
            if (empty($payment_supply)) {
                $payment_supply = new PaymentSupply();
            }
            $payment_supply->fill($data);
            $payment_supply->total = PaymentSupply::getTotal($payment_supply);
            $payment_supply->save();

        } catch (Exception $e) {
            log::info($e);
            return false;
        }
        return true;
    }

    /*
     * 給与明細出力
     */
    public static function createReportExcel($data, $paymentData) {

        $employee = Employee::where('uid', $data['employee_uid'])->first();
        $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();
        $date = $data['year'].'年'.$data['month'].'月';

        $salaries = TimecardService::getTimecardInfoBySalary($data['year'], $data['month'], $data['employee_uid']);


        foreach ($salaries as &$salary) {
            $hour = floor($salary['time'] / 60);
            $minute = $salary['time'] % 60;
            $salary["price"] = $hour * $salary['hourly_pay'];
            $salary["price"] += $minute * ($salary['hourly_pay'] / 60);

            //切り捨て
            if ($workplace->payroll_role == "1") {
                $salary["price"] = floor($salary["price"]);
                //四捨五入
            } elseif ($workplace->payroll_role == "2") {
                $salary["price"] = round($salary["price"]);
            }
        }

        $excelHelper = new PaymentExcel($employee,$workplace,$date);
        //Excelオブジェクト作成
        $excel = $excelHelper->getExcelFile($data, $salaries, $paymentData);

        if (!file_exists(storage_path('payment_reports'))) {
            mkdir(storage_path('payment_reports'));
        }
        $path = storage_path('payment_reports/').Carbon::now()->format('YmdHis');
        // tmpリソースにexcelを書き出し
        $writer = \PHPExcel_IOFactory::createWriter($excel,'Excel5');
        $writer->save($path);

        return $path;
    }

    /*
     * 給与明細テーブル更新
     */
    public static function updatePayments($data) {

        $res = DB::connection('customer-db')->transaction(function () use ($data) {
            try {
                if (count($data['month'])==1) {
                    $data['month'] = '0'.$data['month'];
                }
                //明細（支給）
                $payment_supply = PaymentSupply::ofWorkplace(Auth::user()->workplace_uid)->where('employee_uid', $data['employee_uid'])->where('period', $data['year'] . $data['month'])->first();
                if (empty($payment_supply)) {
                    $payment_supply = new PaymentSupply();
                    $payment_supply->workplace_uid = Auth::user()->workplace_uid;
                    $payment_supply->employee_uid = $data['employee_uid'];
                    $payment_supply->period = $data['year'] . $data['month'];
                }
                $payment_supply->fill($data['supply']);
                $payment_supply->save();

                //明細（控除）
                $payment_deduction = PaymentDeduction::ofWorkplace(Auth::user()->workplace_uid)->where('employee_uid', $data['employee_uid'])->where('period', $data['year'] . $data['month'])->first();
                if (empty($payment_deduction)) {
                    $payment_deduction = new PaymentDeduction();
                    $payment_deduction->workplace_uid = Auth::user()->workplace_uid;
                    $payment_deduction->employee_uid = $data['employee_uid'];
                    $payment_deduction->period = $data['year'] . $data['month'];
                }
                $payment_deduction->fill($data['deduction']);
                $payment_deduction->save();

            } catch (Exception $e) {
                return false;
            }

            return [
                'supply' => $payment_supply,
                'deduction' => $payment_deduction,
            ];
        });

        return $res;

    }


}