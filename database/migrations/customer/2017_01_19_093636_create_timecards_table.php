<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimecardsTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'timecards';

    public function up()
    {
        // タイムカードテーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('employee_uid', false, true)->comment('従業員uid');
            $table->bigInteger('workplace_uid', false, true)->comment('勤務場所uid');
            $table->date('date')->comment('日付');
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
