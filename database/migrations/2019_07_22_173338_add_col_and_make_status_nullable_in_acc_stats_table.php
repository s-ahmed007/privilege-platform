<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColAndMakeStatusNullableInAccStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_kit_stats', function (Blueprint $table) {
            $table->string('final_phone_number', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_kit_stats', function (Blueprint $table) {
            $table->dropColumn('final_phone_number');
        });
    }
}
