<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateFieldToDatetimeInCustomerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_info', function (Blueprint $table) {
            $table->string('member_since')->change();
            $table->dateTime('member_since')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_info', function (Blueprint $table) {
            $table->string('member_since')->change();
            $table->date('member_since')->change();
        });
    }
}
