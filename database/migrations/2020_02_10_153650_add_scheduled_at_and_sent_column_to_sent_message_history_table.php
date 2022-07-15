<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduledAtAndSentColumnToSentMessageHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sent_message_history', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->nullable();
            $table->integer('sent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sent_message_history', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
            $table->dropColumn('sent');
        });
    }
}
