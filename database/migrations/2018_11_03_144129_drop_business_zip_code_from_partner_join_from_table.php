<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBusinessZipCodeFromPartnerJoinFromTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_join_form', function (Blueprint $table) {
            $table->dropColumn('business_zip_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_join_form', function (Blueprint $table) {
            $table->integer('business_zip_code');
        });
    }
}
