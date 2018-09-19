<?php
namespace App\Http\Services;
use App\Models\ChargeLog;
use App\Models\Plan;
use Carbon\Carbon;
use Exception;
use Payjp\Charge;
use Payjp\Customer;
use Payjp\Payjp;
use Log;
use DB;

/*
 * 支払い関連を扱うサービス
 */
class ChargeService
{
    /*
     * 支払いカード登録
     * (PAY.JP顧客登録)
     */
    public static function saveCustomer($data) {

        try {
            Payjp::setApiKey(env('PAYJP_SECRET','sk_test_92acb0603d5cf93821f20c31'));

            //顧客作成
            $customer = Customer::create(array(
                "description" => "新規追加"
            ));

            if (isset($customer["error"])) {
                return false;
            }

            //カード作成
            $cu = Customer::retrieve($customer->id);
            $card = $cu->cards->create(array(
                "name" => $data['name'],
                "number" => $data['number'],
                "exp_month" => $data['exp_month'],
                "exp_year" => $data['exp_year'],
                "cvc" => $data['cvc'],
            ));
            if (isset($card["error"])) {
                return false;
            }

        } catch (\Payjp\Error\Card $e) {
            return false;
        } catch (\Payjp\Error\InvalidRequest $e) {
            return false;
        } catch (\Payjp\Error\Authentication $e) {
            return false;
        } catch (\Payjp\Error\ApiConnection $e) {
            return false;
        } catch (\Payjp\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }

        return [
            'customer_id' => $customer->id,
            'card_id' => $card->id
        ];
    }

    /*
     * 課金作成
     */
    public static function saveCharge($customer_id, $amount, $description=NULL) {

        try {
            Payjp::setApiKey(env('PAYJP_SECRET','sk_test_92acb0603d5cf93821f20c31'));

            //課金作成
            $charge = Charge::create(array(
                "amount" => $amount,
                "currency" => "jpy",
                "customer" => $customer_id,
                "description" => $description,
            ));

            if (isset($charge["error"])) {
                return false;
            }

        } catch (\Payjp\Error\Card $e) {
            return false;
        } catch (\Payjp\Error\InvalidRequest $e) {
            return false;
        } catch (\Payjp\Error\Authentication $e) {
            return false;
        } catch (\Payjp\Error\ApiConnection $e) {
            return false;
        } catch (\Payjp\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }

        return $charge;

    }

    /*
     * 現在日から月末までの料金計算
     */
    public static function getAmountToEndMonth($plan_id, $is_include_today=true) {

        $plan = Plan::where('id', $plan_id)->first();

        $end_date = Carbon::now()->endOfMonth()->format('d');
        $now_d = Carbon::now()->format('d');

        $use_date = $end_date - $now_d;

        //現在日を含める場合
        if ($is_include_today) {
            $use_date += 1;
        }

        $amount = round(($plan->monthly_price / $end_date) * $use_date);

        return $amount;
    }

    /*
     * 月初めから現在日までの料金計算
     */
    public static function getAmountToToday($plan_id) {

        $plan = Plan::where('id', $plan_id)->first();

        $start_date = Carbon::now()->startOfMonth()->format('d');
        $end_date = Carbon::now()->endOfMonth()->format('d'); //月の日数として使用
        $now_d = Carbon::now()->format('d');

        $use_date = $now_d - $start_date + 1;

        $amount = round(($plan->monthly_price / $end_date) * $use_date);

        return $amount;

    }

    /*
     * 支払い情報取得
     */
    public static function getCustomer($customer_id, $card_id) {
        try {

            Payjp::setApiKey(env('PAYJP_SECRET','sk_test_92acb0603d5cf93821f20c31'));

            $customer = Customer::retrieve($customer_id);
            if (isset($customer["error"])) {
                return false;
            }
            $card = $customer->cards->retrieve($card_id);
            if (isset($card["error"])) {
                return false;
            }

        } catch (\Payjp\Error\Card $e) {
            return false;
        } catch (\Payjp\Error\InvalidRequest $e) {
            return false;
        } catch (\Payjp\Error\Authentication $e) {
            return false;
        } catch (\Payjp\Error\ApiConnection $e) {
            return false;
        } catch (\Payjp\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }

        return $card;
    }

    /*
     * 支払い情報更新
     */
    public static function updateCustomer($data) {

        try {
            Payjp::setApiKey(env('PAYJP_SECRET','sk_test_92acb0603d5cf93821f20c31'));

            $customer = Customer::retrieve($data['payment_customer_id']);
            if (isset($customer["error"])) {
                return false;
            }

            $card = $customer->cards->create(array(
                "name" => $data['name'],
                "number" => $data['number'],
                "exp_month" => $data['exp_month'],
                "exp_year" => $data['exp_year'],
                "cvc" => $data['cvc'],
            ));
            if (isset($card["error"])) {
                return false;
            }

        } catch (\Payjp\Error\Card $e) {
            return false;
        } catch (\Payjp\Error\InvalidRequest $e) {
            return false;
        } catch (\Payjp\Error\Authentication $e) {
            return false;
        } catch (\Payjp\Error\ApiConnection $e) {
            return false;
        } catch (\Payjp\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }

        return ['card_id' => $card->id];

    }

    /*
     * timestamp-dbのchargeテーブル登録
     */
    public static function saveChargeTable($workplace, $contract, $amount) {

        try {

            $charge = new \App\Models\Charge();

            $charge->contract_id = $contract->id;
            $charge->workplace_uid = $workplace->uid;
            $charge->workplace_name = $workplace->formal_name;
            $charge->amount = $amount;
            $charge->charge_date = Carbon::now()->format('Y-m-d');
            $charge->save();

        } catch (Exception $e) {
            return false;
        }

        return DB::connection('timestamp-db')->getPdo()->lastInsertId();

    }

    /*
     * timestamp-dbのchargeテーブル更新
     */
    public static function updateStatusChargeTable($id, $status) {

        try {
            $charge = \App\Models\Charge::where('id', $id)->first();
            $charge->status = $status;
            $charge->save();

        } catch (Exception $e) {
            return false;
        }

        return true;

    }

    /*
     * ChargeLog更新
     */
    public static function saveChargeLog($charge_id, $payment_customer_id, $charge) {
        $charge_log = new ChargeLog();
        $charge_log->charge_id = $charge_id;
        $charge_log->payment_charge_id = $charge["id"];
        $charge_log->payment_customer_id = $payment_customer_id;
        $charge_log->amount = $charge["amount"];
        $charge_log->refunded = $charge["refunded"];
        $charge_log->captured = $charge["captured"];
        $charge_log->failure_code = $charge["failure_code"];
        $charge_log->failure_message = $charge["failure_message"];

        $charge_log->save();

    }

}