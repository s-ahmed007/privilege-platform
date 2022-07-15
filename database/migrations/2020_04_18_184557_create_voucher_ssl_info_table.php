<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherSslInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_ssl_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->tinyInteger('status');
            $table->string('tran_id', 100)->unique();
            $table->datetime('tran_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('store_amount', 10, 2)->nullable();
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
            $table->tinyInteger('platform')->nullable();
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
        Schema::dropIfExists('voucher_ssl_info');
    }
}
