<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentReportTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'payment_supplys';

    public function up()
    {
        // 給与明細(支給)テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workplace_uid')->comment('勤務場所uid');
            $table->bigInteger('employee_uid')->comment('従業員uid');
            $table->string('period')->comment('対象年月');
            $table->integer('base_salary')->comment('基本給')->nullable();
            $table->integer('traffic_cost')->comment('通勤手当')->nullable();
            $table->integer('over_cost')->comment('時間外手当')->nullable();
            $table->integer('unemployment_cost')->comment('不就労控除')->nullable();
            $table->string('free_name_1')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_1')->comment('自由入力欄（値）')->nullable();
            $table->string('free_name_2')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_2')->comment('自由入力欄（値）')->nullable();
            $table->string('free_name_3')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_3')->comment('自由入力欄（値）')->nullable();
            $table->string('free_name_4')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_4')->comment('自由入力欄（値）')->nullable();
            $table->string('free_name_5')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_5')->comment('自由入力欄（値）')->nullable();
            $table->integer('total')->comment('総支給額')->nullable();

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
