<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardSellerAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_seller_account', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 45);
            $table->string('password', 255);
            $table->string('phone', 16);
            $table->integer('role');
            $table->tinyInteger('active');
            $table->string('f_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_seller_account');
    }
}
