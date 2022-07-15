<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePartnerPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('partner_post', 'post');

        Schema::table('post', function (Blueprint $table) {
            $table->renameColumn('partner_account_id', 'poster_id');
        });

        Schema::table('post', function (Blueprint $table) {
            $table->tinyInteger('poster_type');
        });

        Schema::table('post', function (Blueprint $table) {
            $table->string('post_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_post', function (Blueprint $table) {
            //
        });
    }
}
