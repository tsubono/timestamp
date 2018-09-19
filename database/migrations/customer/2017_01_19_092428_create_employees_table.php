<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'employees';

    public function up()
    {
        // 従業員テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('uid', false, true)->comment('従業員uid');
            $table->string('lname')->comment('姓');
            $table->string('fname')->comment('名');
            $table->string('lname_kana')->comment('姓（カナ）');
            $table->string('fname_kana')->comment('名（カナ）');
            $table->enum('gender', ['male', 'female', 'other'])->comment('性別');
            $table->date('birthday')->comment('生年月日');
            $table->string('icon')->comment('画像（アイコンor写真）');
            $table->integer('traffic_cost')->comment('交通費');
            $table->date('joined_date')->comment('入社日');
            $table->date('resigned_date')->comment('退職日')->nullale();

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
