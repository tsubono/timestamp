<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModRecordersTable0417 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recorders', function(Blueprint $table) {
            $table->string('terminal_id')->nullable()->after('token')->comment('端末ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recorders', function(Blueprint $table) {
            $table->dropColumn('terminal_id');
        });
    }
}
