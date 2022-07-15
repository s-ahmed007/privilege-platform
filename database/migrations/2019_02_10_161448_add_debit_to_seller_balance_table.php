<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDebitToSellerBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seller_balance', function (Blueprint $table) {
            $table->integer('debit')->default(0);
            $table->integer('debit_used')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seller_balance', function (Blueprint $table) {
            $table->dropColumn('debit');
            $table->dropColumn('debit_used');
        });
    }
}
