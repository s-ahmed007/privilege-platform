<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderDateTypeToDatetimeToTempInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->string('order_date')->change();
        });
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->dateTime('order_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            Schema::table('info_at_buy_card', function (Blueprint $table) {
                $table->string('order_date')->change();
            });
            Schema::table('info_at_buy_card', function (Blueprint $table) {
                $table->date('order_date')->change();
            });
        });
    }
}
