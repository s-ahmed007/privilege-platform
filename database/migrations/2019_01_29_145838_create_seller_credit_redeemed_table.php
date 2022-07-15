<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerCreditRedeemedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_credit_redeemed', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('credit');
            $table->integer('seller_account_id');
            $table->integer('status');
            $table->dateTime('posted_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_credit_redeemed');
    }
}
