<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->decimal('credit', 10, 2);
            $table->decimal('credit_used', 10, 2);
            $table->decimal('debit', 10, 2);
            $table->decimal('debit_used', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_payments');
    }
}
