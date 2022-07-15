<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResetOtpToResetUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reset_user', function (Blueprint $table) {
            $table->string('reset_otp', 10)->nullable()->after('sent_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reset_user', function (Blueprint $table) {
            $table->dropColumn('reset_otp');
        });
    }
}
