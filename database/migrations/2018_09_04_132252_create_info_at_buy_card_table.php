<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoAtBuyCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_at_buy_card', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->string('tran_id', 255);
            $table->string('customer_serial_id', 10);
            $table->string('customer_username', 50);
            $table->string('password', 255);
            $table->integer('moderator_status')->default(0);
            $table->string('customer_first_name', 100);
            $table->string('customer_last_name', 100);
            $table->string('customer_full_name', 100);
            $table->string('customer_email', 100);
            $table->date('customer_dob');
            $table->string('customer_gender', 10);
            $table->string('customer_contact_number', 15);
            $table->text('customer_address')->nullable();
            $table->string('customer_profile_image', 255);
            $table->integer('customer_type');
            $table->integer('month');
            $table->date('expiry_date');
            $table->date('member_since');
            $table->string('referral_number', 45);
            $table->integer('reference_used')->default(0);
            $table->integer('card_active');
            $table->string('card_activation_code', 45);
            $table->string('firebase_token', 255)->nullable()->default(null);
            $table->integer('delivery_status')->default(0);
            $table->integer('review_deleted')->default(0);
            $table->tinyInteger('delivery_type');
            $table->text('shipping_address');
            $table->string('customer_social_id', 45)->nullable()->default(null);
            $table->text('customer_social_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info_at_buy_card');
    }
}
