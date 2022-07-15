<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateTimeInCouponPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rbd_coupon_payment', function (Blueprint $table) {
            $table->string('updated_at')->change();
        });
        Schema::table('rbd_coupon_payment', function (Blueprint $table) {
            $table->dateTime('updated_at')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
