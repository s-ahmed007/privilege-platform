<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIconColumnToAllCategoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon', 255)->nullable();
        });
        Schema::table('sub_cat_1', function (Blueprint $table) {
            $table->string('icon', 255)->nullable();
        });
        Schema::table('sub_cat_2', function (Blueprint $table) {
            $table->string('icon', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
        Schema::table('sub_cat_1', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
        Schema::table('sub_cat_2', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
}
