<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    protected $connection = 'timestamp-db';

    /** @type string テーブル名 */
    private $table = 'charges';

    public function up()
    {
        // 課金情報テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->comment('契約id');
            $table->string('workplace_uid')->comment('勤務場所uid');
            $table->string('workplace_name')->comment('勤務場所名');
            $table->integer('amount')->comment('決済金額');
            $table->string('status')->comment('状態')->nullable();
            $table->dateTime('charge_date')->comment('決済予定日');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
