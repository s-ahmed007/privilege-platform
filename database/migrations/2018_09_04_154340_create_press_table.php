<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('press', function (Blueprint $table) {
            $table->increments('id');
            $table->text('press_name');
            $table->text('sub_title');
            $table->longText('press_details');
            $table->string('press_image', 255);
            $table->string('press_link', 255);
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('press');
    }
}
