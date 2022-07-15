<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTimestampAttributeToDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('all_coupons', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('all_coupons', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });

        Schema::table('customer_notification', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('customer_notification', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });

        Schema::table('partner_notification', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('partner_notification', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });

        Schema::table('review', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('review', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });

        Schema::table('review_comment', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('review_comment', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timestamp_to_datetime');
    }
}
