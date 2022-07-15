<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResetUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reset_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 50);
            $table->string('token');
            $table->tinyInteger('used')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reset_user', function (Blueprint $table) {
            //
        });
    }
}
