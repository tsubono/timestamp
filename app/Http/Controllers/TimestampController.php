<?php
namespace App\Http\Controllers;


use App\Http\Services\EmployeeService;
use App\Http\Services\TimecardService;
use App\Models\Employee;
use App\Models\Icon;
use App\Models\Recorder;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Config;


/**
 * タイムスタンプ（webアプリ用)コントローラー
 */
class TimestampController extends Controller
{
    /*
     * ロック画面表示
     */
    public function locked($subdomain, $uid) {
        //レコーダーが存在するかどうか
        $recorder = Recorder::where('uid', $uid)->first();
        if (empty($recorder)) {
            echo '不正なアクセスです。';
            exit;
        }
        //勤務場所が存在するかどうか
        $workplace = Workplace::where('uid', $recorder->workplace_uid)->first();
        if (empty($workplace)) {
            echo '不正なアクセスです。';
            exit;
        }

        return view('timestamp.locked', [
            'name' => $workplace->name,
            'uid' => $uid
        ]);
    }

    /*
     * ロック解除処理
     */
    public function unlock(Request $request, $subdomain, $uid) {

        $pass_code = $request->get('passcode');

        //レコーダーが存在するかどうか
        $recorder = Recorder::where('uid', $uid)->first();
        if (empty($recorder)) {
            echo '不正なアクセスです。';
            exit;
        }

        //パスコードが一致するかどうか
        if (!hash_equals($recorder->pass_code, $pass_code)) {
            return redirect()
                ->back()
                ->withErrors(['passcode' => 'パスコードが一致しません。']);
        }

        //クッキー設定
        $cookie = \Cookie::make('recorder_token', $recorder->token, 60 * 24 * 30);

        return redirect('/timestamp/'.$uid)->withCookie($cookie);
    }

    /*
     * Web打刻画面をロック
     */
    public function lock($subdomain, $uid) {
        //クッキー消去
        $cookie = \Cookie::forget('recorder_token');

        return redirect('/timestamp/'.$uid.'/locked')->withCookie($cookie);
    }


    /*
     * Webレコーダー画面表示
     */
    public function index($subdomain, $uid) {

        $recorder = Recorder::where('uid', $uid)->first();

        //従業員
        $employees = EmployeeService::getEmployees($recorder->workplace_uid);

        return view('timestamp.index', [
            'uid' => $uid,
            'employees' => $employees["employees"],
            'recorder' => $recorder
        ]);
    }

}
