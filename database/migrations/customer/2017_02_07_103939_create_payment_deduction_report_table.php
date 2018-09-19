<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDeductionReportTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'payment_deductions';

    public function up()
    {
        // 給与明細(控除)テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workplace_uid')->comment('勤務場所uid');
            $table->bigInteger('employee_uid')->comment('従業員uid');
            $table->string('period')->comment('対象年月');
            $table->integer('health_insurance')->comment('健康保険')->nullable();
            $table->integer('care_insurance')->comment('介護保険')->nullable();
            $table->integer('welfare_pension')->comment('厚生年金')->nullable();
            $table->integer('employment_insurance')->comment('雇用保険')->nullable();
            $table->integer('social_insurance')->comment('社会保険計')->nullable();
            $table->integer('income_tax')->comment('所得税')->nullable();
            $table->integer('inhabitant_tax')->comment('住民税')->nullable();
            $table->string('free_name_1')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_1')->comment('自由入力欄（値）')->nullable();
            $table->string('free_name_2')->comment('自由入力欄（名前）')->nullable();
            $table->integer('free_value_2')->comment('自由入力欄（値）')->nullable();
            $table->integer('total')->comment('控除計')->nullable();
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
