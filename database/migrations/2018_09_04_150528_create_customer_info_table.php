<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16);
            $table->string('customer_first_name', 100);
            $table->string('customer_last_name', 100);
            $table->string('customer_full_name', 100);
            $table->string('customer_email', 100)->unique();
            $table->date('customer_dob');
            $table->string('customer_gender', 10);
            $table->string('customer_contact_number', 15)->unique();
            $table->text('customer_address')->nullable();
            $table->string('customer_profile_image', 255);
            $table->integer('customer_type');
            $table->integer('month');
            $table->date('expiry_date');
            $table->date('member_since');
            $table->string('referral_number', 45)->unique();
            $table->integer('reference_used')->default(0);
            $table->integer('card_active');
            $table->string('card_activation_code', 45);
            $table->string('firebase_token', 255)->nullable()->default(null);
            $table->integer('delivery_status')->default(0);
            $table->integer('review_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_info');
    }
}
