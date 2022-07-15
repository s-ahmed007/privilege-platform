<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->json('date_duration');
            $table->json('weekdays');
            $table->json('time_duration');
            $table->integer('point');
            $table->tinyInteger('active');
            $table->string('offer_description', 255);
            $table->integer('price');
            $table->integer('counter_limit')->nullable();
            $table->integer('scan_limit')->nullable();
            $table->integer('point_customize_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_offers');
    }
}
