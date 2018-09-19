<?php
namespace App\Http\Services;
use Carbon\Carbon;
use App\Models\Contract;
use App\Models\Workplace;
use App\User;
use Illuminate\Support\Facades\Mail;
use Config;
use Validator;
use DB;
use Artisan;
use Exception;
use Log;
use App\Mail\OrderShipped;

/*
 * 申し込み関連を扱うサービス
 */
class ContractService
{
    /*
     * 申し込み仮登録
     */
    public static function provisional_save($data)
    {
        try {
            DB::connection('timestamp-db')->beginTransaction();

            $contract = new Contract();
            $contract->email = $data["email"];
            $contract->domain_name = $data["domain_name"];
            $contract->confirmation_token = $data["_token"];

            $contract->save();


            $url = env('APP_URL_SCHEME', 'http://') . env('APP_URL_DOMAIN', 't-stamp.test') . '/contract/apply?token=' . $data["_token"] . '&email=' . urlencode($data["email"]);

            $options = [
                'from' => 'tsubono@ga-design.jp',
                'from_jp' => 'ほげほげ',
                'to' => $data["email"],
                'subject' => 'TIMESTAMP | 本人確認メール',
                'template' => 'emails.contract', // resources/views/emails/contract.blade.php
            ];

            $data = [
                'url' => $url,
            ];
            Mail::to($options['to'])->send(new OrderShipped($options, $data));

            DB::connection('timestamp-db')->commit();

        } catch (Exception $e) {
            DB::connection('timestamp-db')->rollBack();
            log::info($e);
            echo $e;
            return false;
        }

        return true;

    }

    /*
     * 申し込み本登録
     */
    public static function save($data) {

        $redirect = '/login';

        //契約情報バリデート
        $validator = Contract::validate_contract($data['contract']);
        if ($validator->fails()) {
            $redirect = '/contract/apply?token='.$data['token'].'&email='.$data['email'].'#page1';
            return [
                'redirect' => $redirect,
                'validator' => $validator
            ];
        }
        //店舗情報バリデート
        $validator = Workplace::validate_workplace($data['workplace']);
        if ($validator->fails()) {
            $redirect = '/contract/apply?token='.$data['token'].'&email='.$data['email'].'#page2';
            return [
                'redirect' => $redirect,
                'validator' => $validator
            ];
        }
        //ログイン情報バリデート
        $validator = User::validate_user($data['user']);
        if ($validator->fails()) {
            $redirect = '/contract/apply?token='.$data['token'].'&email='.$data['email'].'#page3';
            return [
                'redirect' => $redirect,
                'validator' => $validator
            ];
        }

        //DB作成
        $contract = Contract::where('email', urldecode($data['email']))->where('confirmation_token', $data['token'])->first();
        $command = 'mysql -u '.env('DB_USERNAME','homestead').' -p'.env('DB_PASSWORD','secret').' -e "create database "'.$contract->domain_name.';';
        $return = exec($command);

        if ($return != 0) {
            echo "DB作成に失敗しました";
            exit;
        }
        //テーブル作成
        $return = \Artisan::call('customer:migrate', [
            'db_name' => $contract->domain_name,
            "--force" => "true"
        ]);
        if ($return != 0) {
            echo "テーブルの作成に失敗しました";
            exit;
        }

        //DB設定更新
        Config::set("database.connections.customer-db.database", $contract->domain_name);

        //データ登録
        DB::connection('timestamp-db')->beginTransaction();
        DB::connection('customer-db')->beginTransaction();

        try {
            //契約情報
            $data['contract']['zipcode'] = $data['contract']['zip_1'].'-'.$data['contract']['zip_2'];
            $contract->fill($data['contract']);
            $contract->save();

            //店舗情報
            $workplace = new Workplace();
            $data['workplace']['uid'] = $workplace->create_uid;
            $data['workplace']['zip_code'] = $data['workplace']['zip_1'].'-'.$data['workplace']['zip_2'];
            $data['workplace']['next_charge_date'] = Carbon::now()->startOfDay()->subDays('1')->addMonths('1');
            $data['workplace']['expiration_date'] = Carbon::now()->startOfDay()->subDays('1')->addMonths('1');
            $data['workplace']['plan_id'] = "3";
            $workplace->fill($data['workplace']);
            $workplace->save();

            //ユーザー情報
            $user = new User();
            $data['user']['password'] = \Hash::make($data['user']['password']);
            $user->fill($data['user']);
            $user->save();

            DB::connection('timestamp-db')->commit();
            DB::connection('customer-db')->commit();

        } catch (Exception $e) {
            DB::connection('timestamp-db')->rollBack();
            DB::connection('customer-db')->rollBack();
        }

        $redirect = env('APP_URL_SCHEME','http://').$contract->domain_name.'.'.env('APP_URL_DOMAIN','t-stamp.loc').'/login';

        return [
            'redirect' => $redirect,
            'validator' => $validator
        ];
    }

    public static function update($data) {

        try {
            $contract = Contract::where('id', $data["id"])->first();
            if (!empty($data['zip_1']) && !empty($data['zip_2'])) {
                $data['zipcode'] = $data['zip_1'] . "-" . $data['zip_2'];
            }
            $contract->fill($data);
            $contract->save();
        } catch (Exception $e) {
            log::info($e);
            return false;
        }
        return true;
    }
}