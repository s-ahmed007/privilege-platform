<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTimestatmpToDatetimeInTranReqAndBranchUserNotiTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_user_notification', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('branch_user_notification', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });
        Schema::table('customer_transaction_request', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('customer_transaction_request', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
