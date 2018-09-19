<?php

namespace App\Http\Controllers;

use App\Http\Services\EmployeeService;
use App\Http\Services\WorkingReportService;
use App\Models\Workplace;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;


/*
 * 出勤簿コントローラー
 */
class WorkingReportController extends Controller
{
    use ApiResponseTrait;
    use ExpirationCheckTrait;

    /*
     * 出勤簿出力画面表示
     */
    public function getIndex()
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

        return view('working_report.index', array_merge($messages, $params));
    }

    /*
     * 出勤簿出力
     */
    public function postExport(Request $request) {

        $data = $request->all();
        $request->flash();

        //zipオブシェクト作成処理
        $zip = WorkingReportService::createReportExcel($data);

        if (is_array($zip) && isset($zip['error'])) {
            session(['err_message' => $zip['error']]);
            return back();

        }

        $fileName = "出勤簿";
        $header = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename*=UTF-8''" . rawurlencode("$fileName.zip"),
        ];

        return \Response::make(file_get_contents($zip->getPath()), 200, $header);
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
