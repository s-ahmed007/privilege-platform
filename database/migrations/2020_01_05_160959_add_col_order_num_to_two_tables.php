<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColOrderNumToTwoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('top_brands', function (Blueprint $table) {
            $table->integer('order_num')->nullable();
        });
        Schema::table('trending_offers', function (Blueprint $table) {
            $table->integer('order_num')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_brands', function (Blueprint $table) {
            $table->dropColumn('order_num');
        });
        Schema::table('trending_offers', function (Blueprint $table) {
            $table->dropColumn('order_num');
        });
    }
}
