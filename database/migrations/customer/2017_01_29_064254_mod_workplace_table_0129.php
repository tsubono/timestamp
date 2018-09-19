<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModWorkplaceTable0129 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workplaces', function(Blueprint $table) {
            $table->dropColumn('timing_of_tomorrow');
            $table->dropColumn('next_charge_date');
            $table->dropColumn('expiration_date');
        });
        Schema::table('workplaces', function(Blueprint $table) {
            $table->string('timing_of_tomorrow')->after('tel')->comment('日付変更タイミング');
            $table->date('expiration_date')->after('payment_customer_id')->comment('有効期限日');
            $table->date('next_charge_date')->after('payment_customer_id')->comment('次回決済日');
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
            $table->dropColumn('timing_of_tomorrow');
            $table->dropColumn('next_charge_date');
            $table->dropColumn('expiration_date');
        });
        Schema::table('workplaces', function(Blueprint $table) {
            $table->string('timing_of_tomorrow')->after('tel')->comment('日付変更タイミング');
            $table->date('expiration_date')->after('payment_customer_id')->comment('有効期限日');
            $table->date('next_charge_date')->after('payment_customer_id')->comment('次回決済日');
        });
    }
}
