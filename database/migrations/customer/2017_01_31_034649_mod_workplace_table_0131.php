<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWorkplaceTable0131 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->integer('next_plan_id')->after('plan_id')->nullable()->comment('次月プランID');
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
            $table->dropColumn('next_plan_id');
        });
    }
}
