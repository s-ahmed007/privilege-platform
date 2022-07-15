<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChagnePostedOnToDatetimeInPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('post', function (Blueprint $table) {
            $table->dateTime('posted_on')->change();
        });

        Schema::table('likes_post', function (Blueprint $table) {
            $table->string('posted_on')->change();
        });
        Schema::table('likes_post', function (Blueprint $table) {
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
        Schema::table('post', function (Blueprint $table) {
            //
        });
    }
}
