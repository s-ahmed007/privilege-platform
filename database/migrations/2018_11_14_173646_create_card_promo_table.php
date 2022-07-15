<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardPromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_promo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 255);
            $table->integer('active');
            $table->string('text', 255);
            $table->integer('flat_rate')->nullable();
            $table->integer('percentage')->nullable();
            $table->integer('type');
            $table->date('expiry_date');
            $table->string('usage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_promo');
    }
}
