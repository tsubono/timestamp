<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkplacesTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'workplaces';

    public function up()
    {
        // 勤務場所テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('uid', false, true)->comment('勤務場所uid');
            $table->string('name')->comment('略称');
            $table->string('formal_name')->comment('正式名称');
            $table->string('zip_code')->comment('郵便番号');
            $table->string('pref')->comment('都道府県');
            $table->string('address')->comment('市区町村・番地');
            $table->string('building')->comment('建物名');
            $table->string('tel')->comment('電話番号');
            $table->time('timing_of_tomorrow')->comment('日付変更タイミング');
            $table->enum('round_minute_attendance', ['1', '10', '15', '30'])->default('1')->comment('出退勤時間の丸め分数');
            $table->enum('round_minute_break', ['1', '10', '15', '30'])->default('1')->comment('休憩時間の丸め分数');
            $table->integer('plan_id')->comment('プランid');
            $table->string('payment_method')->comment('支払い方法')->default('クレジットカード')->nullable();
            $table->string('payment_customer_id')->comment('決済用顧客id')->nullable();
            $table->dateTime('next_charge_date')->comment('次回決済日');
            $table->dateTime('expiration_date')->comment('有効期限日');
            $table->boolean('suspend_flg')->comment('利用停止フラグ(true:利用停止中 false:利用中)');

            $table->timestamps();
            $table->softDeletes();

            $table->unique('uid');
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
