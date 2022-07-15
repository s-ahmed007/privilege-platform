<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('phone', 20);
            $table->tinyInteger('status');
            $table->string('tran_id', 100)->unique();
            $table->datetime('tran_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('store_amount', 10, 2)->nullable();
            $table->integer('donation_type');
            $table->text('val_id')->nullable();
            $table->text('card_type')->nullable();
            $table->text('card_no')->nullable();
            $table->text('currency')->nullable();
            $table->text('bank_tran_id')->nullable();
            $table->text('card_issuer')->nullable();
            $table->text('card_brand')->nullable();
            $table->text('card_issuer_country')->nullable();
            $table->text('card_issuer_country_code')->nullable();
            $table->decimal('currency_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
