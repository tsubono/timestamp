<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Recorder;
use App\Models\Workplace;
use App\User;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;
use Exception;

/*
 * 認証Apiコントローラー
 */
class AuthApiController extends Controller
{
    use IsRecorderTrait;

    protected $connection = 'customer-db';

    /*
     * ログイン処理
     */
    public function postLogin(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            $domain = $request->get('domain_name');

            $contract = Contract::where('domain_name', $domain)->first();
            if (empty($contract)) {
                $response['error'] = [];
                $response['error']['message'] = "ドメイン名が不正です。";
                return response()->json($response);
            }


            // 接続設定にデータベース名を設定
            Config::set("database.connections.{$this->connection}.database", $domain);

            DB::reconnect($this->connection);
            DB::setDefaultConnection($this->connection);

            //ユーザー存在確認
            $user = User::where('login_id', $request->get('login_id'))
                        ->where('enable_flg', '1')->first();

            if (empty($user)) {
                $response['error'] = [];
                $response['error']['message'] = "ユーザーIDもしくはパスワードが不正です。";
            } else {
                if (!\Hash::check($request->get('password'), $user->password)) {
                    $response['error'] = [];
                    $response['error']['message'] = "ユーザーIDもしくはパスワードが不正です。";

                } else {
                    //店舗一覧取得
                    $response['workplace_list'] = [];

                    //オーナーの場合 = 全店舗
                    if ($user->owner_flg == "1") {
                        $workplaces = Workplace::where('suspend_flg', '<>', '1')->get();
                        foreach ($workplaces as $idx => $workplace) {
                            $response['workplace_list'][$idx]['uid'] = $workplace->uid;
                            $response['workplace_list'][$idx]['name'] = $workplace->formal_name;
                        }
                    //ユーザーの場合 = 1店舗だけ
                    } else {
                        $workplace = Workplace::where('uid', $user->workplace_uid)->first();
                        if (empty($workplace)) {
                            $response['error'] = [];
                            $response['error']['message'] = "ユーザーの所属する店舗が存在しません。";
                        } else {
                            $response['workplace_list'][0]['uid'] = $workplace->uid;
                            $response['workplace_list'][0]['name'] = $workplace->formal_name;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "認証処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }

    /*
     * 端末登録処理
     */
    public function postSaveTerminal(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //ユーザー存在確認
            $user = User::where('login_id', $request->get('login_id'))
                ->where('enable_flg', '1')->first();

            if (empty($user)) {
                $response['error'] = [];
                $response['error']['message'] = "ユーザーIDもしくはパスワードが不正です。";

            } else {
                //ログイントークン発行
                $token = \Hash::make($user->password. $user->login_id. Carbon::now()->format('His'));

                //レコーダー登録
                $recorder = new Recorder();

                $data = $request->all();
                $data['uid'] = $recorder->create_uid;
                $data['type'] = 'app';
                $data['token'] = $token;
                $recorder->fill($data);
                $recorder->save();

                $response['token'] = $token;
            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "登録処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }

    /*
     * 初期化処理
     */
    public function postInitialize(Request $request) {
        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";

            } else {
                //初期化処理 = レコーダー削除
                $recorder->delete();

                $response['success'] = 'OK';
            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "初期化処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }
}
