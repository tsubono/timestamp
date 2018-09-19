<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Config;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    protected $guard = 'users';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth, Request $request)
    {
        //$this->middleware('guest', ['except' => 'logout']);
        $this->auth = $auth;
    }

    public function username()
    {
        return 'login_id';
    }

    /*
     * ログイン画面表示
     */
    public function getLogin() {
        $message = session('message');
        session()->forget('message');
        return view('auth.login',['message'=>$message]);
    }

    /*
     * ログイン処理
     */
    public function postLogin(Request $request)
    {

        $request->flash();

        $user = User::where('login_id', $request->get('login_id'))->first();

        if (empty($user) || !\Hash::check($request->get('password'), $user->password) || $user->enable_flg!=1) {
            session(['message'=>'認証できませんでした。<br>入力を確認してください。']);
            return redirect('/login');
        }

//        if ($user->is_active !== true) {
//            return $this->loginError('このユーザーのログインは無効にされています。');
//        }

        $this->auth->login($user, $request->has('remember'));

        if ($user->owner_flg=="1") {
            return redirect('/owner');
        }

        return redirect('/');
    }

    /*
     * ログアウト
     */
    public function getLogout(Request $request) {
        $user = User::where('id', $this->auth->user()->id)->first();
        if (!empty($user)) {
            $domain_name = $request->route('subdomain');
            $redirect = env('APP_URL_SCHEME','http://').$domain_name.'.'.env('APP_URL_DOMAIN','t-stamp.loc').'/login';
            $this->auth->logout();
            return redirect($redirect);
        }
    }

    /**
     * ログイン後の処理
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {

    }

}
