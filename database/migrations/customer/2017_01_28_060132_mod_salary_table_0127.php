<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModSalaryTable0127 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salaries', function(Blueprint $table) {
            $table->dropColumn('end_time');
            $table->dropColumn('apply_date');
        });
        Schema::table('salaries', function(Blueprint $table) {
            $table->date('apply_date')->after('employee_uid')->comment('適用開始日');
            $table->boolean('default_flg')->nullable()->after('hourly_pay')->comment('デフォルトフラグ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salaries', function(Blueprint $table) {
            $table->date('end_time')->nullable()->default(NULL)->after('start_time');
        });
    }
}
