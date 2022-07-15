<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->string('customer_id', 16);
            $table->decimal('amount_spent', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->timestamp('posted_on');
            $table->integer('req_id')->nullable();
            $table->integer('rbd_payment_status')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_table');
    }
}
