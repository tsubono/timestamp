<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModChangeTimecardDetails0330 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_timecard_details', function(Blueprint $table) {
            $table->integer('operating_time_round60')->after('operating_time_round30');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_timecard_details', function(Blueprint $table) {
            $table->dropColumn('operating_time_round60');
        });
    }
}
