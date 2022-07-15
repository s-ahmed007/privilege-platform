<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAmountToInfoAtBuyCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
}
