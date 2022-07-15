<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoyaltyLogEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('royalty_log_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event', 50)->nullable();
            $table->string('event_value', 50)->nullable();
            $table->string('customer_id', 40)->nullable();
            $table->dateTime('posted_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('royalty_log_events');
    }
}
