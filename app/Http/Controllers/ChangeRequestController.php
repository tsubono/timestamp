<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimecardEditRequest;
use App\Http\Services\TimecardService;
use App\Models\ChangeTimecard;
use App\Models\Timecard;
use App\Models\Workplace;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;


/*
 * 変更申請コントローラー
 */
class ChangeRequestController extends Controller
{
    use ApiResponseTrait;
    use ExpirationCheckTrait;

    /*
     * 変更申請一覧表示
     */
    public function getIndex()
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        //statusがNULLの変更申請を取得
        $change_requests = ChangeTimecard::ofWorkplace(Auth::user()->workplace_uid)->whereNull('status')->get();
        //変更申請履歴も取得
        $change_request_histories = ChangeTimecard::ofWorkplace(Auth::user()->workplace_uid)->whereNotNull('status')->get();

        //変更申請に紐づくタイムカード詳細を取得
        foreach ($change_requests as $change_request) {
            $details = TimecardService::getRecordsForEdit($change_request->id, "ChangeTimecardDetail");
            $change_request->details = $details;
        }
        //変更申請履歴に紐づくタイムカード詳細を取得
        foreach ($change_request_histories as $change_request_history) {
            $details = TimecardService::getRecordsForEdit($change_request_history->id, "ChangeTimecardDetail");
            $change_request_history->details = $details;
        }

        $messages = $this->getMessages();

        return view('change_request.index', [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'change_requests' => $change_requests,
            'change_request_histories' => $change_request_histories,
            'message' => $messages['message'],
            'err_message' => $messages['err_message'],
            'controls' => Timecard::getControls(),
        ]);
    }

    /*
     * 変更申請ステータス更新
     */
    public function postUpdate(Request $request) {

        $data = $request->all();

        //否認の場合 = 否認(2)にするだけ
        if ($data["status"] == "2") {
            ChangeTimecard::updateStatus($data["id"], "2");
            session(['message' => '変更依頼を否認しました。']);

        //承認の場合
        } else {
            //タイムカード情報を更新
            $res = TimecardService::update($data['timecard_id'], $data['employee_uid'], $data['details'], true);
            if ($res) {
                //変更申請一覧のステータスを更新
                ChangeTimecard::updateStatus($data["id"], "1");
                session(['message' => '変更依頼を承認しました。']);
            } else {
                session(['err_message' => '変更依頼を承認できませんでした。']);
            }
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/change_request'],
        ];

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
