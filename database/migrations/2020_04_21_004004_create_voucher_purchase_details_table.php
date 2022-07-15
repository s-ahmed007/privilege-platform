<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherPurchaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_purchase_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voucher_id');
            $table->integer('ssl_id');
            $table->tinyInteger('redeemed')->default(0);
            $table->integer('review_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_purchase_details');
    }
}
