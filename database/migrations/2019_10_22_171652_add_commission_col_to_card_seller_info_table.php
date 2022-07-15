<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionColToCardSellerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_seller_info', function (Blueprint $table) {
            $table->integer('commission')->nullable();
            $table->integer('trial_commission')->nullable()->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_seller_info', function (Blueprint $table) {
            $table->dropColumn('commission');
            $table->dropColumn('trial_commission');
        });
    }
}
