<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangeTimecardsTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'change_timecards';

    public function up()
    {
        // タイムカード変更申請テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timecard_id')->comment('タイムカードid');
            $table->bigInteger('employee_uid', false, true)->comment('従業員uid');
            $table->bigInteger('workplace_uid', false, true)->comment('勤務場所uid');
            $table->date('date')->comment('日付');
            $table->smallInteger('status')->comment('承認状況(1:承認 2:否認)')->nullable();
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
