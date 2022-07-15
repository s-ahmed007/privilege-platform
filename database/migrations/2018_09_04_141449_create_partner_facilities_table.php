<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_facilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->integer('card_payment')->default(0);
            $table->integer('kids_area')->default(0);
            $table->integer('outdoor_seating')->default(0);
            $table->integer('smoking_area')->default(0);
            $table->integer('reservation')->default(0);
            $table->integer('wifi')->default(0);
            $table->integer('concierge')->default(0);
            $table->integer('online_booking')->default(0);
            $table->integer('seating_area')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_facilities');
    }
}
