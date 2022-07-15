<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTncForPartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tnc_for_partner', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_account_id');
            $table->longText('terms&condition');
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
        Schema::dropIfExists('tnc_for_partner');
    }
}
