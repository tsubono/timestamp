<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimecardDetailsTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'timecard_details';

    public function up()
    {
        // タイムカード（詳細）テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timecard_id')->comment('タイムカードid');
            $table->time('start_time')->comment('打刻時間(出勤,休憩入)');
            $table->time('end_time')->comment('打刻時間(退勤,休憩戻)');
            $table->smallInteger('type')->comment('打刻タイプ（1:出退勤 2:休憩）');
            $table->integer('operating_time_round1')->comment('稼働時間(丸めなし）');
            $table->integer('operating_time_round10')->comment('稼働時間(10分丸め)');
            $table->integer('operating_time_round15')->comment('稼働時間(15分丸め)');
            $table->integer('operating_time_round30')->comment('稼働時間(30分丸め）');
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
