<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModEmployeesTable0127 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function(Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('joined_date');
            $table->dropColumn('resigned_date');
        });

        Schema::table('employees', function(Blueprint $table) {
            $table->date('birthday')->nullable()->default(NULL)->after('gender');
            $table->date('resigned_date')->nullable()->default(NULL)->after('traffic_cost');
            $table->date('joined_date')->nullable()->default(NULL)->after('traffic_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function(Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('joined_date');
            $table->dropColumn('resigned_date');
        });

        Schema::table('employees', function(Blueprint $table) {
            $table->date('birthday')->nullable()->default(NULL)->after('gender');
            $table->date('resigned_date')->nullable()->default(NULL)->after('traffic_cost');
            $table->date('joined_date')->nullable()->default(NULL)->after('traffic_cost');
        });
    }
}
