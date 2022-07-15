<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignedCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigned_card', function (Blueprint $table) {
            $table->increments('id');
            $table->string('card_number', 16);
            $table->integer('status');
            $table->integer('card_type');
            $table->integer('seller_account_id');
            $table->dateTime('assigned_on');
            $table->dateTime('sold_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assigned_card');
    }
}
