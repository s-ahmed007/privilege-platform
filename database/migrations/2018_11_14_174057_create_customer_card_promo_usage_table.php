<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCardPromoUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_card_promo_usage', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->integer('promo_id');
            $table->integer('ssl_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_card_promo_usage');
    }
}
