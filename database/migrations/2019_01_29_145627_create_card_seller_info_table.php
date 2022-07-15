<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardSellerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_seller_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 45);
            $table->string('last_name', 45);
            $table->integer('seller_account_id');
            $table->integer('pin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_seller_info');
    }
}
