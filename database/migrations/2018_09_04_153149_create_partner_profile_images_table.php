<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerProfileImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_profile_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_account_id');
            $table->string('partner_profile_image', 255);
            $table->string('partner_thumb_image', 255)->nullable();
            $table->string('partner_cover_photo', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_profile_images');
    }
}
