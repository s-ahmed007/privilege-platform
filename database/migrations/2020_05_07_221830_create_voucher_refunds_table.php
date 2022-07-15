<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->integer('purchase_id');
            $table->integer('ssl_id');
            $table->tinyInteger('refund_status')->default(0);
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->tinyInteger('refund_type')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('voucher_refunds');
    }
}
