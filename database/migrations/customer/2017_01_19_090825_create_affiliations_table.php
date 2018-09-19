<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliationsTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'affiliations';

    public function up()
    {
        // 従業員と勤務場所の紐付け用テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workplace_uid', false, true)->comment('勤務場所uid');
            $table->bigInteger('employee_uid', false, true)->comment('従業員uid');
            $table->timestamps();

            $table->unique(['workplace_uid', 'employee_uid']);
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
