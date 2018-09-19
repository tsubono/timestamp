<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use Exception;
use Log;

/*
 * パスワードリセットコントローラー
 */
class PasswordResetController extends Controller
{
    protected $redirectPath = '/login';

    /**
     * パスワード再設定用メール送信
     */
    public function postSendEmail(Request $request)
    {
        $request->flash();

        $domain_name = $request->route('subdomain');
        $data = $request->all();
        //ユーザー存在確認
        $isUser = User::where($data['type'], $data[$data['type']])->first();
        //ユーザーがいる場合はメール送信
        if ($isUser && $isUser->enable_flg==1) {
            $this->send($data, $domain_name, $isUser);
        } else {
            //return redirect()->back()->withErrors(['reset_error' => '登録されていないユーザーです。']);
        }

        return redirect()->back()->with('type', $data['type'])->with('sent_mail', 'パスワード再設定のメールを送信しました。');
    }

    /**
     * メール送信処理
     */
    protected function send($data, $domain_name, $user)
    {
        try {
            //契約情報
            $contract = Contract::where('domain_name', $domain_name)->first();

            //トークン作成
            $token = hash_hmac(
                'sha256',
                str_random(40) . $contract->email,
                env('APP_KEY')
            );

            $from = env('MAIL_FROM', 'noreply@t-stamp.net');
            //$to = $contract->email;
            if ($data['type'] == "email") {
                $to = $data[$data['type']];
            } elseif (!empty($user->email)) {
                $to = $user->email;
            } else {
                $to = $contract->email;
            }


            if ($data['type'] == "email") {
                $val = urlencode($data[$data['type']]);
            } else {
                $val = $data[$data['type']];
            }
            $url = env('APP_URL_SCHEME', 'http://') . $contract->domain_name . '.'
                . env('APP_URL_DOMAIN', 't-stamp.loc') . '/reset_password/'
                . '?token=' . $token . '&type=' . $data['type'] . '&val=' . $val;

            $messsage =
                <<<CONTRACT
パスワードをリセットするためにリンクをクリックしてください。
{$url}
CONTRACT;

            $subject = "TIMESTAMP | パスワード再設定";

            Mail::raw($messsage, function ($mail) use ($from, $to, $subject) {
                $mail->from($from);
                $mail->to($to)->subject($subject);
            });

            //passwort_resetsテーブルに登録
            PasswordReset::insert([
                $data['type'] => $data[$data['type']],
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * パスワード再設定画面表示
     */
    public function showResetForm(Request $request)
    {
        $type = $request->get('type');
        $val = $request->get('val');
        $token = $request->get('token');

        //password_resetsテーブルにあるデータか確認
        $isData = PasswordReset::where($type, $val)->where("token", $token)->orderBy('created_at','desc')->first();

        if (empty($isData)) {
            //ログイン画面にリダイレクトされる
            return redirect('/login');
        }
        $user = User::where($type, $val)->first();
        if (empty($user)) {
            //ログイン画面にリダイレクトされる
            return redirect('/login');
        }

        return view('auth.reset',[
            'token' => $token,
            'type' => $type,
            'val' => $val
        ]);
    }

    /**
     * パスワード再設定
     */
    public function postResetPassword(Request $request)
    {
        $this->validate($request, $this->getResetValidationRules(), $this->messages());

        $data = $request->all();

        User::where($data['type'], $data[$data['type']])->update([
            $data['type'] => $data[$data['type']],
            'password' => bcrypt($data["password"]),
        ]);

        //パスワード変更完了後、password_resetsテーブルのデータを削除
        $password_reset = PasswordReset::where($data['type'], $data[$data['type']])->where('token', $data['token'])->first();
        if (!empty($password_reset)) {
            $password_reset->delete();
        }

        return redirect($this->redirectPath);
    }

    /**
     * パスワード再設定時バリデーション
     */
    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'password' => 'required|confirmed|between:8,32',
        ];
    }

    /**
     * バリデーションメッセージ
     */
    public function messages()
    {
        return [
            'token.required' => 'tokenが必要です。',
            'password.required' => 'パスワードが必要です。',
            'password.confirmed' => 'パスワードとパスワード(確認用)が一致しません。',
            'password.between' => 'パスワードは8～32文字で入力してください。',
        ];
    }

}
