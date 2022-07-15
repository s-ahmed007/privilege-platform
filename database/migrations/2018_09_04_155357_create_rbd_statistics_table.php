<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRbdStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rbd_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 16)->nullable()->default(null);
            $table->integer('partner_id')->nullable()->default(null);
            $table->string('ip_address', 50)->nullable()->default(null);
            $table->text('browser_data')->nullable()->default(null);
            $table->timestamp('visited_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rbd_statistics');
    }
}
