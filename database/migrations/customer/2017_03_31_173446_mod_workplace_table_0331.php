<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWorkplaceTable0331 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->integer('payroll_role')->after('suspend_flg')->default('1')->comment('給与計算規則[1:切り捨て 2:四捨五入]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->dropColumn('payroll_role');
        });
    }
}
