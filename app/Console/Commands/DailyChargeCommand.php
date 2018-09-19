<?php

namespace App\Console\Commands;

use App\Http\Services\ChargeService;
use App\Models\Charge;
use App\Models\Contract;
use App\Models\Plan;
use App\Models\Workplace;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Config;
use Exception;
use Log;
use DB;

class DailyChargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '決済コマンド（毎日実行）';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo Carbon::now();
        echo "Daily自動決済開始\n";

        //確認が済んでいる全契約取得
        $contracts = Contract::where('confirmation_flg', '1')->get();

        foreach ($contracts as $contract) {

            // 契約別DB接続設定の接続先データベースを設定
            Config::set("database.connections.customer-db.database", $contract->domain_name);

            DB::connection('customer-db')->reconnect();

            echo "------------------------------------\n";
            echo "ContractID:".$contract->id."\n";
            echo "ContractDomainName:".$contract->domain_name."\n";


            //workplacesの「次回決済日」が当日および過去のものが対象
            $workplaces = Workplace::where('next_charge_date', '<=', Carbon::now()->format('Y-m-d'))->get();
            if (count($workplaces)==0) {
                echo "本日決済対象なし\n";
            }
            foreach ($workplaces as $workplace) {

                try {

                    echo "WorkplaceUID:".$workplace->uid."\n";
                    echo "WorkplaceName:".$workplace->name."\n";

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
                        echo "Error:決済レコード作成失敗\n";

                        DB::connection('timestamp-db')->rollBack();
                        DB::connection('customer-db')->rollBack();

                        continue;
                    }


                    $payment_customer_id = $workplace->payment_customer_id;
                    //支払い情報が未登録時エラー
                    if (empty($payment_customer_id)) {
                        echo "Error:支払い情報が未登録\n";

                        DB::connection('timestamp-db')->rollBack();
                        DB::connection('customer-db')->rollBack();

                        continue;
                    }
                    $now = Carbon::now();
                    //決済処理
                    $description = $now->format('Y')."年".$now->format('m') ."月分の残日数と".
                        $now->copy()->startOfMonth()->addMonths("1")->format('Y') . "年" . $now->copy()->startOfMonth()->addMonths("1")->format('m') . "月分の請求(自動決済)です。";
                    $charge = ChargeService::saveCharge($payment_customer_id, $amount, $description);
                    //決済処理失敗時エラー
                    if (!$charge) {
                        echo "Error:決済処理失敗\n";

                        DB::connection('timestamp-db')->rollBack();
                        DB::connection('customer-db')->rollBack();
                        //chargesテーブルステータス更新
                        ChargeService::updateStatusChargeTable($charge_id, "課金失敗");

                        continue;
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
                    //if (Carbon::now()->endOfMonth()->isSameDay(Carbon::now())) {
                        $workplace->next_charge_date = Carbon::now()->startOfMonth()->addMonths('1')->endOfMonth();
                        $workplace->expiration_date = Carbon::now()->startOfMonth()->addMonths('1')->endOfMonth();
                    //} else {
                    //    $workplace->next_charge_date = Carbon::now()->startOfDay()->addMonths('1');
                    //    $workplace->expiration_date = Carbon::now()->startOfDay()->addMonths('1');
                    //}

                    $workplace->charged_flg = NULL;
                    $workplace->save();

                    DB::connection('timestamp-db')->commit();
                    DB::connection('customer-db')->commit();

                    echo "決済処理完了\n";

                } catch (Exception $e) {
                    log::info($e);
                    echo "Error:システムエラー\n";

                    DB::connection('timestamp-db')->rollBack();
                    DB::connection('customer-db')->rollBack();
                }
            }

        }

    }
}
