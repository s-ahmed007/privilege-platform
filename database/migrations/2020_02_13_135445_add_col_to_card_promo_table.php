<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToCardPromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_promo', function (Blueprint $table) {
            $table->tinyInteger('membership_type')->nullable();
            $table->integer('month')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_promo', function (Blueprint $table) {
            $table->dropColumn('membership_type');
            $table->dropColumn('month');
        });
    }
}
