<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_account_id');
            $table->decimal('1_star', 10, 2)->default(0.00);
            $table->decimal('2_star', 10, 2)->default(0.00);
            $table->decimal('3_star', 10, 2)->default(0.00);
            $table->decimal('4_star', 10, 2)->default(0.00);
            $table->decimal('5_star', 10, 2)->default(0.00);
            $table->decimal('average_rating', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rating');
    }
}
