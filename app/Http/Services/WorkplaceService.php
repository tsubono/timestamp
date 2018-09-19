<?php
namespace App\Http\Services;
use App\Models\Contract;
use App\Models\Plan;
use App\Models\Workplace;
use Carbon\Carbon;
use Validator;
use DB;
use Auth;
use Exception;
use Log;

/*
 * 店舗関連を扱うサービス
 */
class WorkplaceService
{
    /*
     * 店舗登録
     */
    public static function save($data) {

        try {
            //店舗情報
            $workplace = new Workplace();
            $data['uid'] = $workplace->create_uid;
            $data['zip_code'] = $data['zip_1'] . '-' . $data['zip_2'];
            $data['next_charge_date'] = Carbon::now()->addMonths('1')->endOfMonth();
            $data['expiration_date'] = Carbon::now()->addMonths('1')->endOfMonth();
            $workplace->fill($data);
            $workplace->save();


        } catch (Exception $e) {
            return false;
        }

        return $workplace;
    }

    /*
     * 店舗更新
     */
    public static function update($data) {
        try {
            $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();
            if (!empty($workplace)) {
                if (isset($data['zip_1']) && isset($data['zip_2'])) {
                    $data['zip_code'] = $data['zip_1'] . "-" . $data['zip_2'];
                }
                $workplace->fill($data);
                $workplace->save();
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * 店舗更新(時間設定)
     */
    public static function updateTime($data) {
        try {
            $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();
            if (!empty($workplace)) {
                $workplace->fill($data);
                $workplace->save();
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * 店舗追加
     */
    public static function addWorkplace($subdomain, $data) {

        try {

            $now = Carbon::now();

            DB::connection('customer-db')->beginTransaction();
            DB::connection('timestamp-db')->beginTransaction();

            //支払い情報作成
            $res = ChargeService::saveCustomer($data['card']);
            if (!$res) {
                DB::connection('customer-db')->rollBack();
                return ['error' => 'カード情報が不正です。'];
            }

            $data['payment_customer_id'] = $res['customer_id'];
            $data['payment_card_id'] = $res['card_id'];

            //店舗登録
            $data['charged_flg'] = 1;
            $workplace = WorkplaceService::save($data);
            if (!$workplace) {
                DB::connection('customer-db')->rollBack();
                return ['error' => '店舗を登録できませんでした。'];
            }

            //ここから課金処理
            //当月分の日割り金額取得
            $amount = ChargeService::getAmountToEndMonth($data['plan_id']);
            //来月分の金額も合算して課金
            $amount += Plan::monthly_amount($data['plan_id']);

            $contract = Contract::where('domain_name', $subdomain)->first();
            //chargesテーブルに決済レコード作成
            $charge_id = ChargeService::saveChargeTable($workplace, $contract, $amount);


            $description = $now->format('m') . '月分と' . $now->startOfMonth()->addMonths('1')->format('m') . '月分の請求です。';

            $res = ChargeService::saveCharge($data['payment_customer_id'], $amount, $description);
            if (!$res) {
                //chargesテーブルステータス更新
                ChargeService::updateStatusChargeTable($charge_id, "課金失敗");
                DB::connection('timestamp-db')->commit();
                DB::connection('customer-db')->rollBack();
                return ['error' => 'カード情報が不正です。'];
            }

            //chargesテーブルステータス更新
            ChargeService::updateStatusChargeTable($charge_id, "課金成功");
            //戻り値のChargeオブジェクトでchargeLog更新
            ChargeService::saveChargeLog($charge_id, $data['payment_customer_id'], $res);

            DB::connection('customer-db')->commit();
            DB::connection('timestamp-db')->commit();

        } catch (Exception $e) {
            log::info($e);

            DB::connection('customer-db')->rollBack();
            DB::connection('timestamp-db')->rollBack();
            return ['error' => '店舗を登録できませんでした。'];
        }

        return true;

    }
}