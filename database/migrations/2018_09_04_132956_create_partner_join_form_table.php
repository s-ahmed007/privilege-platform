<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerJoinFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_join_form', function (Blueprint $table) {
            $table->increments('id');
            $table->string('business_name', 50);
            $table->string('business_number', 16);
            $table->string('business_email', 100);
            $table->integer('business_zip_code');
            $table->string('business_address', 100);
            $table->string('full_name', 50);
            $table->string('partner_division', 45);
            $table->string('business_area', 100);
            $table->string('business_category', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_join_form');
    }
}
