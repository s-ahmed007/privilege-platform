<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchUserNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_user_notification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_user_id');
            $table->string('customer_id');
            $table->string('notification_text');
            $table->tinyInteger('notification_type');
            $table->integer('source_id');
            $table->tinyInteger('seen');
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
        Schema::dropIfExists('branch_user_notification');
    }
}
