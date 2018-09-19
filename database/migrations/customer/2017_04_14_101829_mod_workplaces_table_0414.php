<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWorkplacesTable0414 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->dropColumn('round_minute_attendance');
            $table->dropColumn('round_minute_break');
        });
        Schema::table('workplaces', function(Blueprint $table) {
            $table->integer('round_minute_break')->default('1')->after('timing_of_tomorrow')->comment('休憩時間の丸め分数');
            $table->integer('round_minute_attendance')->default('1')->after('timing_of_tomorrow')->comment('日付変更タイミング');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
