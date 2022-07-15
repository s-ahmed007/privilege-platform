<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_branch', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 45)->unique();
            $table->string('password', 255);
            $table->integer('partner_account_id');
            $table->string('partner_email', 100)->unique();
            $table->string('partner_mobile', 15)->unique();
            $table->text('partner_address');
            $table->text('partner_location');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->string('zip_code', 10);
            $table->string('partner_area', 45);
            $table->string('partner_division', 45);
            $table->integer('main_branch')->default(0);
            $table->tinyInteger('active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_branch');
    }
}
