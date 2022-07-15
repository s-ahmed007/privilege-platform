<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedeemDurationColInBranchVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_vouchers', function (Blueprint $table) {
            $table->integer('redeem_duration')->nullable()->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_vouchers', function (Blueprint $table) {
            $table->dropColumn('redeem_duration');
        });
    }
}
