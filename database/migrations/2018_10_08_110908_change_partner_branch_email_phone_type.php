<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePartnerBranchEmailPhoneType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_branch', function ($table) {
            $table->dropUnique(['partner_email']);
            $table->dropUnique(['partner_mobile']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_branch', function ($table) {
            $table->unique(['partner_email']);
            $table->unique(['partner_email']);
        });
    }
}
