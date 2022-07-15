<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSslTransactionTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssl_transaction_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->integer('status')->nullable()->default(null);
            $table->date('tran_date')->nullable()->default(null);
            $table->string('tran_id', 255)->unique();
            $table->text('val_id')->nullable()->default(null);
            $table->decimal('amount', 10, 2);
            $table->decimal('store_amount', 10, 2)->nullable()->default(null);
            $table->text('card_type')->nullable()->default(null);
            $table->text('card_no')->nullable()->default(null);
            $table->text('currency')->nullable()->default(null);
            $table->text('bank_tran_id')->nullable()->default(null);
            $table->text('card_issuer')->nullable()->default(null);
            $table->text('card_brand')->nullable()->default(null);
            $table->text('card_issuer_country')->nullable()->default(null);
            $table->text('card_issuer_country_code')->nullable()->default(null);
            $table->decimal('currency_amount', 10, 2)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ssl_transaction_table');
    }
}
