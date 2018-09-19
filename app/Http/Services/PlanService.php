<?php
namespace App\Http\Services;
use App\Models\Contract;
use App\Models\Plan;
use App\Models\Workplace;
use Carbon\Carbon;
use DB;
use Log;
use Auth;
use Exception;

/*
 * プラン設定関連を扱うサービス
 */
class PlanService
{
    /*
     * プラン設定変更
     */
    public static function update($subdomain, $plan_id) {

        try {
            DB::connection('customer-db')->beginTransaction();
            DB::connection('timestamp-db')->beginTransaction();

            $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();

            //変更前プラン
            $old_plan_id = $workplace->plan_id;
            $old_plan = Plan::where('id', $old_plan_id)->first();
            //変更後プラン
            $plan = Plan::where('id', $plan_id)->first();


            //アップグレードの場合はその場で決済
            if ($old_plan_id < $plan_id) {

                $amount = self::getAmount($plan_id, $old_plan_id, $workplace->charged_flg);

                $contract = Contract::where('domain_name', $subdomain)->first();
                //chargesテーブルに決済レコード作成
                $charge_id = ChargeService::saveChargeTable($workplace, $contract, $amount);

                $description = "※プランアップグレード(".$old_plan->name."→".$plan->name.")："
                    .Carbon::now()->format('m')."月と"
                    .Carbon::now()->startOfMonth()->addMonth("1")->format('m')."月分の請求です。";
                //決済実行
                $res = ChargeService::saveCharge($workplace->payment_customer_id, $amount, $description);


                if (!$res) {
                    //chargesテーブルステータス更新
                    ChargeService::updateStatusChargeTable($charge_id, "課金失敗");
                    DB::connection('timestamp-db')->commit();

                    return false;
                }

                //chargesテーブルステータス更新
                ChargeService::updateStatusChargeTable($charge_id, "課金成功");
                //戻り値のChargeオブジェクトでchargeLog更新
                ChargeService::saveChargeLog($charge_id, $workplace->payment_customer_id, $res);

                //プランID更新
                $workplace->plan_id = $plan_id;
                $workplace->next_plan_id = NULL;
                //日程更新
                $workplace->next_charge_date = Carbon::now()->startOfMonth()->addMonths("1")->endOfMonth()->format('Y-m-d');
                $workplace->expiration_date = Carbon::now()->startOfMonth()->addMonths("1")->endOfMonth()->format('Y-m-d');
                $workplace->charged_flg = 1;
                $workplace->save();

            //ダウングレードの場合は次月から適用 = 決済cronの時にプランidをnext_plan_idに更新する
            } else {
                $workplace->next_plan_id = $plan_id;
                $workplace->save();
            }

            DB::connection('customer-db')->commit();
            DB::connection('timestamp-db')->commit();

        } catch (Exception $e) {
            log::info($e);

            DB::connection('customer-db')->rollBack();
            DB::connection('timestamp-db')->rollBack();
            return false;
        }

        return true;
    }

    /*
     * プランの変更時
     * 決済金額を取得
     */
    public static function getAmount($plan_id, $old_plan_id, $charged_flg=false) {

        $plan = Plan::where('id', $plan_id)->first();
        $old_plan = Plan::where('id', $old_plan_id)->first();

        //今月未利用分取得(=払い戻し対象)
        $old_amount = ChargeService::getAmountToEndMonth($old_plan_id);
        //アップグレード後料の月末までの料金取得(=新規請求対象)
        $new_amount = ChargeService::getAmountToEndMonth($plan_id);

        //差額を請求する
        $amount = $new_amount - $old_amount;
        //次月分もこの時に決済
        //既に次月分決済してる場合は古いプランの分を払い戻しする
        if ($charged_flg) {
            $amount += ($plan->monthly_price - $old_plan->monthly_price);
        } else {
            $amount += $plan->monthly_price;
        }

        return $amount;
    }


}