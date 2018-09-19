<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWorkplaceTable0130 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->string('payment_card_id')->after('payment_customer_id')->nullable()->comment('決済用カードid');
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
            $table->dropColumn('payment_card_id');
        });
    }
}
