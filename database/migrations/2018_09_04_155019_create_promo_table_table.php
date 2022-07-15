<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_name', 20);
            $table->text('image_link');
            $table->string('category', 45);
            $table->integer('discount_percentage');
            $table->string('partner_website', 255);
            $table->string('promo_code', 15);
            $table->text('term&condition');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_table');
    }
}
