<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_notification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 16);
            $table->string('image_link', 255);
            $table->text('notification_text');
            $table->integer('notification_type');
            $table->integer('source_id');
            $table->tinyInteger('seen')->default(0);
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
        Schema::dropIfExists('customer_notification');
    }
}
