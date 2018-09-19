<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeLogsTable extends Migration
{
    protected $connection = 'timestamp-db';

    /** @type string テーブル名 */
    private $table = 'change_logs';

    public function up()
    {
        // 課金ログテーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('charge_id')->comment('課金情報id');
            $table->string('payment_charge_id')->comment('決済時に作成される課金id');
            $table->string('payment_customer_id')->comment('決済用顧客id');
            $table->integer('amount')->comment('決済金額');
            $table->boolean('refunded')->comment('返金状況(true:払い戻し済み false:返金なし)');
            $table->boolean('captured')->comment('確定状況(true:確定 false:未確定)');
            $table->string('failure_code')->comment('課金失敗時のエラーコード')->nullable();
            $table->string('failure_message')->comment('課金失敗時の説明')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
