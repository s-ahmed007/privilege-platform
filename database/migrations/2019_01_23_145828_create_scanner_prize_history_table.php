<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScannerPrizeHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scanner_prize_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->integer('point');
            $table->integer('scanner_id');
            $table->integer('status');
            $table->dateTime('posted_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scanner_prize_history');
    }
}
