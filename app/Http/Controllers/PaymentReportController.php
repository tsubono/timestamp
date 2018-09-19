<?php

namespace App\Http\Controllers;

use App\Http\Services\EmployeeService;
use App\Http\Services\PaymentReportService;
use App\Models\Employee;
use App\Models\PaymentDeduction;
use App\Models\PaymentSupply;
use App\Models\Salary;
use App\Models\Workplace;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;


/*
 * 給与明細コントローラー
 */
class PaymentReportController extends Controller
{
    use ApiResponseTrait;
    use ExpirationCheckTrait;

    /*
     * 給与明細出力画面表示
     */
    public function getIndex(Request $request)
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'employees' => EmployeeService::getEmployees()['employees'],
        ];

        return view('payment_report.index', array_merge($messages, $params));
    }

    /*
     * 給与明細計算結果出力
     */
    public function postCalcDetail(Request $request) {

        $data = $request->all();
        $request->flash();

        //データが入ってるかチェック
        $res = $this->isData($data);
        if (!$res) {
            return redirect('/payment_report');
        }

        return redirect('/payment_report/detail?employee_uid='.$data['employee_uid'].'&year='.$data['year'].'&month='.$data['month']);
    }

    /*
     * 給与明細計算結果画面表示
     */
    public function getCalcDetail(Request $request) {

        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $data = $request->all();
        $request->flash();

        //データが入ってるかチェック
        $res = $this->isData($data);
        if (!$res) {
            return redirect('/payment_report');
        }

        //給与情報が設定されているかチェック
        $res = $this->isSalary($data['employee_uid']);
        if (!$res) {
            return redirect('/payment_report');
        }

        //計算処理
        $res = PaymentReportService::calcPayment($data);
        if (!$res) {
            session(['err_message'=>'給与詳細を作成できませんでした。出勤記録や時給設定をご確認ください。']);
            return redirect('/payment_report');
        }

        if (count($data['month'])==1) {
            $data['month'] = '0'.$data['month'];
        }

        //明細データ（支給）
        $payment_supply = PaymentSupply::ofWorkplace(Auth::user()->workplace_uid)->ofEmployee($data['employee_uid'])
                            ->ofPeriod($data['year'].$data['month'])->first();
        if (empty($payment_supply)) {
            $payment_supply = new PaymentSupply();
        }

        //明細データ（控除）
        $payment_deduction = PaymentDeduction::ofWorkplace(Auth::user()->workplace_uid)->ofEmployee($data['employee_uid'])
                                ->ofPeriod($data['year'].$data['month'])->first();
        if (empty($payment_deduction)) {
            $payment_deduction = new PaymentDeduction();
        }

        $messages = $this->getMessages();
        $params = [
            "payment_supply" => $payment_supply,
            "payment_deduction" => $payment_deduction,
            "year" => $data['year'],
            "month" => $data['month'],
            "employee" => Employee::ofEmployee($data['employee_uid'])->first(),
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
        ];

        return view('payment_report.detail', array_merge($messages, $params));
    }

    /*
     * 給与明細出力
     */
    public function postExport(Request $request) {

        $data = $request->all();
        $request->flash();

        //テーブル更新
        $paymentData = PaymentReportService::updatePayments($data);
        if (!$paymentData) {
            session(['err_message'=>'給与明細を作成できませんでした。']);
            return back();
        }

        //ファイル作成
        $path = PaymentReportService::createReportExcel($data, $paymentData);
        if (!$path) {
            session(['err_message'=>'給与明細を作成できませんでした。']);
            return back();
        }

        $employee = Employee::ofEmployee($data['employee_uid'])->first();

        $fileName = $data['year']."年".$data['month']."月度給与明細_".$employee->name;
        // IEでのファイル名文字化け対応 (RFC 6266 http://www.rfc-editor.org/rfc/rfc6266.txt)
        $header = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename*=UTF-8''" . rawurlencode($fileName),
        ];

        return \Response::make(file_get_contents($path), 200, $header);

    }

    /*
     * データが入ってるか確認
     */
    private function isData($data) {
        if (empty($data['employee_uid'])) {
            session(['err_message' => "従業員を指定してください。"]);
            return false;
        }
        if (empty($data['year']) || empty($data['month'])) {
            session(['err_message' => "対象年月を指定してください。"]);
            return false;
        }

        return true;
    }

    /*
     * 給与設定が登録されているか確認
     */
    private function isSalary($employee_uid) {
        $salary = Salary::ofEmployee($employee_uid)
                    ->get();
        if (count($salary)==0) {
            session(['err_message' => "対象の従業員に有効な給与情報が設定されていません。"]);
            return false;
        }

        return true;

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

}
