<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointCustomizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_customize', function (Blueprint $table) {
            $table->increments('id');
            $table->string('point_type', 100);
            $table->json('date_duration');
            $table->json('weekdays');
            $table->json('time_duration');
            $table->tinyInteger('point_multiplier')->default(0);
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_customize');
    }
}
