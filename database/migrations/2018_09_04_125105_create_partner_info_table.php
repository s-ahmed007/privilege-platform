<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_account_id');
            $table->string('partner_name', 45);
            $table->string('owner_name', 45)->nullable()->default(null);
            $table->string('owner_contact', 15)->nullable()->default(null);
            $table->integer('partner_category');
            $table->string('partner_type', 45);
            $table->string('facebook_link', 255)->default('#');
            $table->string('website_link', 100)->default('#');
            $table->string('instagram_link', 100)->default('#');
            $table->longText('about');
            $table->date('expiry_date');
            $table->string('firebase_token', 255)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_info');
    }
}
