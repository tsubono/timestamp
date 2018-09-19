<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractInfoRequest;
use App\Http\Requests\ContractMailRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\WorkplaceRequest;
use App\Http\Services\ChargeService;
use App\Http\Services\ContractService;
use App\Http\Services\UserService;
use App\Http\Services\WorkplaceService;
use App\Models\Contract;
use App\Models\Plan;
use App\Models\Workplace;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Config;
use DB;
use Log;
use Illuminate\Http\JsonResponse;

/*
 * オーナー専用コントローラー
 */
class OwnerController extends Controller
{
    /**
     * 店舗選択画面表示
     */
    public function getIndex()
    {
        //オーナーじゃない場合は表示させないよに
        if (Auth::user()->owner_flg != "1") {
            return redirect('/');
        }

        $user = User::where('id', Auth::user()->id)->first();

        $messages = $this->getMessages();
        $params = [
            'workplaces' => Workplace::all(),
            'account_flg' => true,
            'contract_flg' => true,
            'plans' => Plan::all(),
            'user' => $user,
        ];

        return view('owner.index', array_merge($messages, $params));
    }

    /*
     * 店舗選択処理
     */
    public function postSelectWorkplace(Request $request) {

        //ログインユーザーに店舗情報を設定
        $user = User::where('id', Auth::user()->id)->first();
        $user->workplace_uid = $request->get('workplace_uid');
        $user->save();

        //ホーム画面へ
        return redirect('/');
    }

    /*
     * 店舗新規追加処理
     */
    public function postAddWorkplace($subdomain, WorkplaceRequest $request) {

        $data = $request->all();

        if (empty($data["plan_id"])) {
            return $request->response(['error' => 'プランを選択してください。']);
        }
        //追加処理
        $res = WorkplaceService::addWorkplace($subdomain, $data);

        if (isset($res['error'])) {
            return $request->response(['error' => $res['error']]);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/owner'],
        ];
    }

    /*
     * オーナーアカウント編集
     */
    public function postEditAccount(UserRequest $request) {

        $data = $request->all();

        //重複確認
        $isData = User::isEmail($data['email'], Auth::user()->id);
        if (!empty($isData)) {
            return $request->response(['error' => 'メールアドレスが既に登録されています。']);
        }
        $isData = User::isLoginId($data['login_id'], Auth::user()->id);
        if (!empty($isData)) {
            return $request->response(['error' => 'ログインIDが既に登録されています。']);
        }

        //更新処理
        $data['id'] = Auth::user()->id;
        $res = UserService::update($data);
        if ($res) {
            session(['message' => 'アカウント情報を更新しました。']);
        } else {
            session(['err_message' => 'アカウント情報を更新できませんでした。']);
        }

        //パラメータによって戻る画面を分岐
        if ($request->get('contract_flg')) {
            $location = '/owner/contract';
        } else {
            $location = '/owner';
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => $location],
        ];
    }

    /*
     * 契約情報表示
     */
    public function getContract(Request $request) {
        //オーナーじゃない場合は表示させないよに
        if (Auth::user()->owner_flg != "1") {
            return redirect('/');
        }

        //ドメインチェック
        $domain_name = $request->route('subdomain');
        $contract = Contract::where('domain_name', $domain_name)->first();
        if (empty($contract)) {
            echo '不正なアクセスです。';
            exit;
        }

        $user = User::where('id', Auth::user()->id)->first();

        $messages = $this->getMessages();
        $params = [
            'account_flg' => true,
            'contract_flg' => true,
            'contract' => $contract,
            'workplace_flg' => true,
            'user' => $user,
        ];

        return view('owner.contract', array_merge($messages, $params));
    }

    /*
     * 契約情報更新
     */
    public static function postEditContract(ContractInfoRequest $request) {

        $data = $request->all();

        //更新処理
        $res = ContractService::update($data);

        if ($res) {
            session(['message' => '基本情報を更新しました。']);
        } else {
            session(['err_message' => '基本情報を更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/owner/contract'],
        ];

    }

    /*
     * 契約情報（メール情報）更新
     */
    public static function postEditContractMail(ContractMailRequest $request) {

        $data = $request->all();

        //重複確認
        if (Contract::isEmail($data['email'], $data['id'])) {
            $response = [
                'status_code' => '422',
                'errors' => implode('<br>', array_flatten(['mail' => 'メールアドレスが既に登録されています。'])),
            ];
            return new JsonResponse($response, '422');
        }

        //更新処理
        $res = ContractService::update($data);

        if ($res) {
            session(['message' => 'メール情報を更新しました。']);
        } else {
            session(['err_message' => 'メール情報を更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/owner/contract'],
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
