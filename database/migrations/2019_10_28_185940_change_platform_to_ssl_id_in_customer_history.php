<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePlatformToSslIdInCustomerHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_history', function (Blueprint $table) {
            $table->renameColumn('platform', 'ssl_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_history', function (Blueprint $table) {
            $table->renameColumn('ssl_id', 'platform');
        });
    }
}
