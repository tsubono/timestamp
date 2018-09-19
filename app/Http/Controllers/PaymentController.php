<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Services\ChargeService;
use App\Http\Services\WorkplaceService;
use App\Models\Contract;
use App\Models\Plan;
use App\Models\Workplace;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Support\Facades\Artisan;
use Log;

/*
 * 支払い情報コントローラー
 */

class PaymentController extends Controller
{

    /*
     * 支払い情報表示
     */
    public function getIndex()
    {
        $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();
        $payment_customer_id = $workplace->payment_customer_id;
        $payment_card_id = $workplace->payment_card_id;

        //支払い情報が既に登録されていればカードオブジェクトを取得
        if (!empty($payment_customer_id) && !empty($payment_card_id)) {
            $card = ChargeService::getCustomer($payment_customer_id, $payment_card_id);
        } else {
            $card = false;
        }

        $messages = $this->getMessages();
        $params = [
            'workplace' => $workplace,
            'card' => $card,
        ];

        return view('payment.index', array_merge($messages, $params));
    }

    /*
     * 支払い情報更新処理
     */
    public function postEditPayment($subdomain, PaymentRequest $request)
    {

        $data = $request->all();

        //支払い情報未登録の場合
        if (empty($data['payment_customer_id']) || empty($data['payment_card_id'])) {
            //支払い顧客新規登録
            $res = ChargeService::saveCustomer($data);
            if (!$res) {
                return $request->response(['error' => 'カード情報が不正です。']);
            }
            //店舗情報更新
            $data['payment_customer_id'] = $res['customer_id'];
            $data['payment_card_id'] = $res['card_id'];

            $res = WorkplaceService::update($data);

            //支払い情報更新
        } else {
            $res = ChargeService::updateCustomer($data);
            if (!$res) {
                return $request->response(['error' => 'カード情報が不正もしくは既に登録されています。']);
            }
            //店舗情報更新
            $data['payment_card_id'] = $res['card_id'];

            $res = WorkplaceService::update($data);
        }

        //支払い情報登録時に決済Cronをまわしとく(期限切れ対策)
        // $exitCode = Artisan::call('daily:charge');
        if ($res) {
            $res = $this->charge($subdomain, Auth::user()->workplace_uid);

            if ($res) {
                session(['message' => '支払い情報を更新しました。']);
            } else {
                session(['message' => '支払い情報を更新できませんでした。']);
            }
        } else {
            session(['message' => '支払い情報を更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/payment'],
        ];
    }

    /*
     * 支払い情報が登録された勤務場所に対して決済処理
     */
    private function charge($subdomain, $workplace_uid)
    {
        $workplace = Workplace::where('uid', $workplace_uid)->first();
        $contract = Contract::where('domain_name', $subdomain)->first();

        try {
            DB::connection('timestamp-db')->beginTransaction();
            DB::connection('customer-db')->beginTransaction();

            //決済金額(=プランの金額)取得
            if (!empty($workplace->next_plan_id)) {
                //next_plan_idがNULLでない場合(=ダウングレード待ち)はそちらのプランの金額で
                //$amount = Plan::monthly_amount($workplace->next_plan_id);
                //決済日(現在)から月末までの分
                $amount = ChargeService::getAmountToEndMonth($workplace->next_plan_id, false);
                //翌月分
                $amount += Plan::monthly_amount($workplace->next_plan_id);
            } else {
                //$amount = Plan::monthly_amount($workplace->plan_id);
                //決済日(現在)から月末までの分
                $amount = ChargeService::getAmountToEndMonth($workplace->plan_id, false);
                //翌月分
                $amount += Plan::monthly_amount($workplace->plan_id);

            }
            //chargesテーブルに決済レコード作成
            $charge_id = ChargeService::saveChargeTable($workplace, $contract, $amount);
            //決済レコード作成失敗時エラー
            if (!$charge_id) {
                DB::connection('timestamp-db')->rollBack();
                DB::connection('customer-db')->rollBack();
                return false;
            }

            $payment_customer_id = $workplace->payment_customer_id;
            //支払い情報が未登録時エラー
            if (empty($payment_customer_id)) {
                DB::connection('timestamp-db')->rollBack();
                DB::connection('customer-db')->rollBack();
                return false;
            }
            $now = Carbon::now();
            //決済処理
            $description = $now->format('Y') . "年" . $now->format('m') . "月分の残日数と" .
                $now->copy()->startOfMonth()->addMonths("1")->format('Y') . "年" . $now->copy()->startOfMonth()->addMonths("1")->format('m') . "月分の請求(自動決済)です。";

            $charge = ChargeService::saveCharge($payment_customer_id, $amount, $description);

            //決済処理失敗時エラー
            if (!$charge) {
                DB::connection('timestamp-db')->rollBack();
                DB::connection('customer-db')->rollBack();
                //chargesテーブルステータス更新
                ChargeService::updateStatusChargeTable($charge_id, "課金失敗");
                return false;
                //課金処理成功時
            } else {
                //chargesテーブルステータス更新
                ChargeService::updateStatusChargeTable($charge_id, "課金成功");
                //戻り値のChargeオブジェクトでchargeLog更新
                ChargeService::saveChargeLog($charge_id, $payment_customer_id, $charge);
            }

            //workplace更新
            if (!empty($workplace->next_plan_id)) {
                //プラン更新
                $workplace->plan_id = $workplace->next_plan_id;
                $workplace->next_plan_id = NULL;
            }
            //次回決済日と有効期限日を更新
            $workplace->next_charge_date = Carbon::now()->startOfMonth()->addMonths('1')->endOfMonth();
            $workplace->expiration_date = Carbon::now()->startOfMonth()->addMonths('1')->endOfMonth();

            $workplace->charged_flg = NULL;
            $workplace->save();

            DB::connection('timestamp-db')->commit();
            DB::connection('customer-db')->commit();

            return true;


        } catch (Exception $e) {
            log::info($e);
            DB::connection('timestamp-db')->rollBack();
            DB::connection('customer-db')->rollBack();
            return false;
        }

    }

    /*
     * セッションメッセージを取得
     */
    private function getMessages()
    {
        $message = session('message');
        $err_message = session('err_message');
        session()->forget('message');
        session()->forget('err_message');

        return compact("message", "err_message");
    }

}
