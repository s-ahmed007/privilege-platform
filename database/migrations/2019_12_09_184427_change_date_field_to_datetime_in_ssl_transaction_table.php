<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateFieldToDatetimeInSslTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ssl_transaction_table', function (Blueprint $table) {
            $table->string('tran_date')->change();
            $table->dateTime('tran_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ssl_transaction_table', function (Blueprint $table) {
            $table->string('tran_date')->change();
            $table->date('tran_date')->change();
        });
    }
}
