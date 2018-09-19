<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModAffiliationTable0309 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliations', function(Blueprint $table) {
            $table->dateTime('current_clock_in')->nullable()->default(NULL)->after('employee_uid')->comment('最新の打刻日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliations', function(Blueprint $table) {
            $table->dropColumn('current_clock_in');
        });
    }
}
