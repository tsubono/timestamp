<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModContractsTable0126 extends Migration
{
    protected $connection = 'timestamp-db';

    private $table = 'contracts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->boolean('confirmation_flg')->comment('確認済みフラグ')->after('confirmation_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('confirmation_flg');
        });
    }
}
