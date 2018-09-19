<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecorderRequest;
use App\Http\Services\RecorderService;
use App\Models\Recorder;
use App\Models\Workplace;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;

/*
 * レコーダーコントローラー
 */
class RecorderController extends Controller
{
    use ExpirationCheckTrait;

    /*
     * レコーダー情報表示
     */
    public function getIndex($subdomain)
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'recorders' => Recorder::ofWorkplace(Auth::user()->workplace_uid)->where('type','<>','app')->get(),
            'subdomain' => $subdomain
        ];

        return view('recorder.index', array_merge($messages, $params));
    }

    /**
     * レコーダー詳細情報表示
     */
    public function getDetail($subdomain, $uid)
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'recorder' => Recorder::where('uid', $uid)->first(),
            'subdomain' => $subdomain
        ];

        return view('recorder.detail', array_merge($messages, $params));
    }

    /*
     * レコーダー追加
     */
    public function postAddRecorder(RecorderRequest $request) {

        $data = $request->all();

        //追加処理
        $res = RecorderService::save($data);

        if ($res) {
            session(['message' => 'レコーダーを追加しました。']);
        } else {
            session(['err_message' => 'レコーダーを追加できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/recorder'],
        ];
    }

    /*
     * レコーダー更新
     */
    public function postEditRecorder(RecorderRequest $request) {

        $data = $request->all();

        //更新処理
        $res = RecorderService::update($data);

        if ($res) {
            session(['message' => 'レコーダーを更新しました。']);
        } else {
            session(['err_message' => 'レコーダーを更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/recorder'],
        ];
    }

    /*
     * レコーダー削除
     */
    public static function postDeleteRecord(Request $request) {

        //削除処理
        $res = RecorderService::delete($request->get('uid'));

        if ($res) {
            session(['message' => 'レコーダーを削除しました。']);
        } else {
            session(['err_message' => 'レコーダーを削除できませんでした。']);
        }

        return redirect('/recorder');
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
