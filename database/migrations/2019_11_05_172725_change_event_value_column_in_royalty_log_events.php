<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEventValueColumnInRoyaltyLogEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('royalty_log_events', function (Blueprint $table) {
            $table->longText('event_value')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('royalty_log_events', function (Blueprint $table) {
            $table->string('event_value', 50)->change();
        });
    }
}
