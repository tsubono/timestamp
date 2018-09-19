<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    protected $connection = 'timestamp-db';

    /** @type string テーブル名 */
    private $table = 'contracts';

    public function up()
    {
        // 契約テーブル
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain_name')->comment('サブドメイン名');
            $table->string('company_name')->comment('会社名');
            $table->string('company_name_kana')->comment('会社名カナ');
            $table->string('person_name')->comment('担当者名');
            $table->string('person_name_kana')->comment('担当者名カナ');
            $table->string('email')->comment('メールアドレス');
            $table->string('tel')->comment('電話番号');
            $table->string('zipcode')->comment('郵便番号');
            $table->string('pref')->comment('都道府県');
            $table->string('address')->comment('市区町村・番地');
            $table->string('building')->comment('建物名');
            $table->boolean('receive_mail_flg')->comment('メール受信可否(true:メール受信可能 false:メール受信拒否)');
            $table->string('confirmation_token')->comment('本人確認用トークン');
            $table->dateTime('free_end_date')->comment('フリー期間終了日時')->nullable();
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
