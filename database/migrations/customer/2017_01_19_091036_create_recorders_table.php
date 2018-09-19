<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordersTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'recorders';

    public function up()
    {
        // レコーダーテーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('uid', false, true)->comment('レコーダーuid');
            $table->bigInteger('workplace_uid', false, true)->comment('勤務場所uid');
            $table->enum('type', ['web', 'app'])->default('web')->comment('レコーダー種類');
            $table->string('name')->comment('レコーダー名');
            $table->integer('pass_code')->comment('パスコード');
            $table->string('token')->comment('トークン');
            $table->timestamps();

            $table->unique('uid');
            $table->unique(['workplace_uid', 'token']);
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
