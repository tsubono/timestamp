<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModTimestampDetailTable0201 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timecard_details', function(Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });

        Schema::table('timecard_details', function(Blueprint $table) {
            $table->dateTime('end_time')->nullable()->default(NULL)->after('timecard_id');
            $table->dateTime('start_time')->nullable()->default(NULL)->after('timecard_id');
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
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });

        Schema::table('timecard_details', function(Blueprint $table) {
            $table->time('end_time')->nullable()->default(NULL)->after('timecard_id');
            $table->time('start_time')->nullable()->default(NULL)->after('timecard_id');
        });
    }
}
