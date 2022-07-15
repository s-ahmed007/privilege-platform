<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->string('sat', 45);
            $table->string('sun', 45);
            $table->string('mon', 45);
            $table->string('tue', 45);
            $table->string('wed', 45);
            $table->string('thurs', 45);
            $table->string('fri', 45);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opening_hours');
    }
}
