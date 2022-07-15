<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfluencerRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('influencer_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name', 100);
            $table->string('blog_name', 100);
            $table->string('blog_category', 100);
            $table->string('email', 100);
            $table->string('facebook_link', 255)->nullable()->default('#');
            $table->string('website_link', 255)->nullable()->default('#');
            $table->string('youtube_link', 255)->nullable()->default('#');
            $table->string('instagram_link', 255)->nullable()->default('#');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('influencer_request');
    }
}
