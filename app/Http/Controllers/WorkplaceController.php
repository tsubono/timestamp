<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkplaceRequest;
use App\Http\Services\EmployeeService;
use App\Http\Services\WorkplaceService;
use App\Models\Workplace;
use Auth;
use DB;
use Log;

/*
 * 勤務場所コントローラー
 */
class WorkplaceController extends Controller
{
    use ExpirationCheckTrait;

    /*
     * 勤務場所情報表示
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
        ];

        return view('workplace.index', array_merge($messages, $params));
    }


    /*
     * 勤務場所基本情報更新
     */
    public function postEditWorkplace(WorkplaceRequest $request) {

        $data = $request->all();

        //更新処理
        $res = WorkplaceService::update($data);

        if ($res) {
            session(['message' => '勤務場所情報を更新しました。']);
        } else {
            session(['err_message' => '勤務場所情報を更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/workplace'],
        ];
    }

    /*
     * 勤務場所時間情報更新
     */
    public function postEditTime(WorkplaceRequest $request) {

        $data = $request->all();

        //出勤中の従業員がいる場合は日付変更設定を変更できないように
        if (EmployeeService::isClockingEmployees(Auth::user()->workplace_uid)) {
            unset($data['timing_of_tomorrow']);
            //更新処理
            $res = WorkplaceService::updateTime($data);

            if ($res) {
                session(['err_message' => '出勤中の従業員が存在するため、日付変更設定が更新できませんでした。']);
            } else {
                session(['err_message' => '勤務場所の時間情報を更新できませんでした。']);
            }

        } else {
            //更新処理
            $res = WorkplaceService::updateTime($data);

            if ($res) {
                session(['message' => '勤務場所の時間情報を更新しました。']);
            } else {
                session(['err_message' => '勤務場所の時間情報を更新できませんでした。']);
            }
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/workplace'],
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
