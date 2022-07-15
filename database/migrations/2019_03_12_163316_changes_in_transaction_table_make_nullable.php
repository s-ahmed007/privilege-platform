<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesInTransactionTableMakeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_table', function (Blueprint $table) {
            $table->decimal('amount_spent', 10, 2)->change()->nullable();
            $table->decimal('discount_amount', 10, 2)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_table', function (Blueprint $table) {
            //
        });
    }
}
