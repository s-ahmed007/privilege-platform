<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerRewardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_reward', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->integer('customer_reward');
            $table->integer('coupon');
            $table->integer('refer_bonus');
            $table->integer('bonus_counter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_reward');
    }
}
