<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailAndTimestampOnResetUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reset_user', function (Blueprint $table) {
            $table->string('email', 45)->nullable();
            $table->timestamps();
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
            $table->dropColumn('email', 45);
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
