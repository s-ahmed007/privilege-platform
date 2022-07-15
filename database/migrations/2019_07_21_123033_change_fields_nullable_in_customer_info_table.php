<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsNullableInCustomerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_info', function (Blueprint $table) {
            $table->string('customer_first_name', 100)->nullable()->change();
            $table->string('customer_last_name', 100)->nullable()->change();
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
            $table->string('customer_first_name', 100)->nullable(false)->change();
            $table->string('customer_last_name', 100)->nullable(false)->change();
        });
    }
}
