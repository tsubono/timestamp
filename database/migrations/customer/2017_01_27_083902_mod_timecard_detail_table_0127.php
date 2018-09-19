<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModTimecardDetailTable0127 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timecard_details', function(Blueprint $table) {
            $table->dropColumn('end_time');
        });

        Schema::table('timecard_details', function(Blueprint $table) {
            $table->date('end_time')->nullable()->default(NULL)->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timecard_details', function(Blueprint $table) {
            $table->dropColumn('end_time');
        });

        Schema::table('timecard_details', function(Blueprint $table) {
            $table->date('end_time')->nullable()->default(NULL)->after('start_time');
        });
    }
}
