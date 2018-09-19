<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Services\ContractService;
use Auth;

/*
 * 新規契約用コントローラー
 */
class ContractController extends Controller
{
    /*
     * 仮申し込み画面表示
     */
    public function getContract() {
        return view('contract.index');
    }

    /*
     * 仮申し込み
     */
    public function postContract(ContractRequest $request) {

        $data = $request->all();
        //contractsテーブルに登録
        $res = ContractService::provisional_save($data);

        if ($res) {
            $message = "お申し込みのメールアドレスに確認メールを送信いたしました。<br>メールに記載のURLから登録を完了してください。";
        } else {
            $message = "申し込み処理に失敗しました。恐れ入りますがお問い合わせフォームよりお問い合わせください。";
        }

        return view('contract.index', ['message'=>$message]);
    }

    /*
     * 本申し込み画面表示
     */
    public function getContractApply(Request $request) {

        $token = $request->get("token");
        $email = $request->get("email");

        //本人確認
        $is_data = Contract::where('confirmation_token', $token)->where('email', $email)->first();
        if (!$is_data) {
            echo "不正なアクセスです。";
            exit;
        }
        //すでに登録完了してる場合
        if (!empty($is_data->confirmation_flg)) {
            return redirect('/contract');
        }

        return view('contract.apply', [
            'email'=>urlencode($email), 'token'=>$token
        ]);
    }

    /*
     * 本申し込み登録
     */
    public function postContractApply(Request $request) {
        $data = $request->all();
        //登録
        $res = ContractService::save($data);

        return redirect($res['redirect'])->withErrors($res['validator'])->withInput();
    }

    /*
     * 契約一覧表示
     */
    public function getContractList() {
        $contracts = Contract::all();

        return view('contract.list', [
            'contracts'=>$contracts
        ]);
    }

}
