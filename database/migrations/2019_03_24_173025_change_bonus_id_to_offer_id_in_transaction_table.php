<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBonusIdToOfferIdInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_table', function (Blueprint $table) {
            $table->renameColumn('bonus_id', 'offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_table', function (Blueprint $table) {
            $table->renameColumn('offer_id', 'bonus_id');
        });
    }
}
