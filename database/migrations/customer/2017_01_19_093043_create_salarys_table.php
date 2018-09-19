<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalarysTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'salaries';

    public function up()
    {
        // 従業員別給与テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('employee_uid', false, true)->comment('従業員uid');
            $table->dateTime('apply_date')->comment('適用開始日時');
            $table->time('start_time')->comment('開始時間');
            $table->time('end_time')->comment('終了時間');
            $table->integer('hourly_pay')->comment('時給');
            $table->timestamps();
            $table->softDeletes();

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
