<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    protected $connection = 'customer-db';

    /** @type string テーブル名 */
    private $table = 'users';

    public function up()
    {
        // ユーザーテーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('ユーザー名');
            $table->string('login_id')->unique()->comment('ログインID');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->string('password')->comment('パスワード');
            $table->boolean('enable_flg')->comment('有効フラグ(true:有効 false:無効)');
            $table->boolean('owner_flg')->comment('オーナーフラグ(true:オーナーアカウント false:勤務場所アカウント)')->nullable();
            $table->bigInteger('workplace_uid', false, true)->comment('勤務場所uid:勤務場所アカウントの場合、対象の勤務場所の管理画面にのみログインできる')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
