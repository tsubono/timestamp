<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModRecorderTable0129 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recorders', function(Blueprint $table) {
            $table->dropColumn('pass_code');
        });
        Schema::table('recorders', function(Blueprint $table) {
            $table->string('pass_code')->after('name')->comment('パスコード');
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
            $table->dropColumn('pass_code');
        });
        Schema::table('recorders', function(Blueprint $table) {
            $table->integer('pass_code')->after('name')->comment('パスコード');
        });
    }
}
