<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddCardPromoIdInInfoAtBuyCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->integer('card_promo_id')->nullable()->default(0);
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
            $table->dropColumn('card_promo_id');
        });
    }
}
