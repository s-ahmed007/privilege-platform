<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePartnerUsernameNullableInPartnerAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_account', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->string('username')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_account', function (Blueprint $table) {
            $table->string('username', 50)->change()->unique();
        });
    }
}
