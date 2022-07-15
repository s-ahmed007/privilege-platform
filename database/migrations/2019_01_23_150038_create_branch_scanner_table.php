<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchScannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_scanner', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 45);
            $table->string('last_name', 45);
            $table->integer('ip_authorized');
            $table->string('designation', 45);
            $table->integer('branch_id');
            $table->integer('branch_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_scanner');
    }
}
