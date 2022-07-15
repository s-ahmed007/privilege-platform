<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_request', function (Blueprint $table) {
            $table->increments('req_id');
            $table->integer('coupon_id');
            $table->string('customer_id', 16);
            $table->tinyInteger('used')->default(0);
            $table->string('request_code', 20);
            $table->date('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_request');
    }
}
