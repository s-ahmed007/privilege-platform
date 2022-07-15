<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToBranchOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_offers', function (Blueprint $table) {
            $table->text('offer_full_description')->nullable();
            $table->integer('priority')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_offers', function (Blueprint $table) {
            $table->dropColumn('offer_full_description');
            $table->dropColumn('priority');
        });
    }
}
