<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLikePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('likes_post', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'liker_id');
        });

        Schema::table('likes_post', function (Blueprint $table) {
            $table->tinyInteger('liker_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('likes_post', function (Blueprint $table) {
            //
        });
    }
}
