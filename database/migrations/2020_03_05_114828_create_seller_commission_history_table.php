<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerCommissionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_commission_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id');
            $table->integer('ssl_id');
            $table->integer('commission');
            $table->tinyInteger('type');
            $table->tinyInteger('paid')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_commission_history');
    }
}
