<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->tinyInteger('coupon_type');
            $table->text('reward_text');
            $table->text('coupon_details');
            $table->text('coupon_tnc');
            $table->string('stock', 45)->default(0);
            $table->timestamp('posted_on');
            $table->date('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_coupons');
    }
}
