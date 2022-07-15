<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_post', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_account_id');
            $table->string('image_url', 255);
            $table->longText('caption');
            $table->tinyInteger('moderate_status')->default(0);
            $table->timestamp('posted_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_post');
    }
}
