<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWorkplacesTable0419 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->boolean('charged_flg')->nullable()->after('payroll_role')->comment('月の途中で決済したフラグ');
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
            $table->dropColumn('charged_flg');
        });
    }
}
