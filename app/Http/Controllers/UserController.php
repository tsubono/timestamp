<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use App\Models\Workplace;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Log;

/*
 * ユーザー（ログインアカウント）コントローラー
 */
class UserController extends Controller
{
    use ExpirationCheckTrait;

    /*
     * 店舗ユーザー一覧表示
     */
    public function getIndex() {

        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'users' => User::ofWorkplace(Auth::user()->workplace_uid)->notOwner()->get(),
        ];

        return view('user.index', array_merge($messages, $params));
    }

    /*
     * ユーザー情報追加
     */
    public function postAddUser(UserRequest $request) {

        $data = $request->all();

        //重複確認
        $res = $this->isData($data);
        if (isset($res['error'])) {
            return $request->response($res);
        }

        //追加処理
        $res = UserService::save($data);
        if ($res) {
            session(['message' => 'ユーザーを追加しました。']);
        } else {
            session(['err_message' => 'ユーザーを追加できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/user'],
        ];
    }

    /*
     * ユーザー情報更新処理
     */
    public function postEditUser(UserRequest $request) {

        $data = $request->all();

        //重複確認
        $res = $this->isData($data);
        if (isset($res['error'])) {
            return $request->response($res);
        }

        //更新処理
        $res = UserService::update($data);
        if ($res) {
            session(['message' => 'ユーザー情報を更新しました。']);
        } else {
            session(['err_message' => 'ユーザー情報を更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/user'],
        ];
    }

    /*
     * ユーザー削除
     */
    public function postDeleteUser(Request $request) {

        //削除処理
        $res = UserService::delete($request->get('id'));

        if ($res) {
            session(['message' => 'ユーザーを削除しました。']);
        } else {
            session(['err_message' => 'ユーザーを削除できませんでした。']);
        }

        return redirect('/user');
    }

    /*
     * データの重複確認を行う
     */
    private function isData($data) {

        if (!empty($data['email'])) {
            $isData = User::isEmail($data['email'], $data['id']);
            if (!empty($isData)) {
                return ['error' => 'メールアドレスが既に登録されています。'];
            }
        }

        $isData = User::isLoginId($data['login_id'], $data['id']);
        if (!empty($isData)) {
            return ['error' => 'ユーザーIDが既に登録されています。'];
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
