<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    protected $connection = 'timestamp-db';

    /** @type string テーブル名 */
    private $table = 'plans';

    public function up()
    {
        // プランテーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('プラン名');
            $table->integer('monthly_price')->comment('月額料金');
            $table->integer('employee_limit')->comment('従業員数上限');
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
