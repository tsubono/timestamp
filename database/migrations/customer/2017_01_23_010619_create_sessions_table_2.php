<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable2 extends Migration
{
    protected $connection = 'customer-db';

    private $table = 'sessions';

    public function up()
    {
        // セッションテーブル
        Schema::create(
            $this->table,
            function (Blueprint $table) {
                $table->engine = 'INNODB ROW_FORMAT=DYNAMIC';

                $table->string('id');
                $table->text('payload');
                $table->integer('last_activity');

                $table->unique('id');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
