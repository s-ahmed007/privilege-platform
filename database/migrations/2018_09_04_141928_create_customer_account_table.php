<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_account', function (Blueprint $table) {
            $table->string('customer_id', 16)->primary();
            $table->string('customer_serial_id', 10);
            $table->string('customer_username', 50)->unique();
            $table->string('password', 255);
            $table->integer('moderator_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_account');
    }
}
