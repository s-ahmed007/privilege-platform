<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsToWishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wish', function (Blueprint $table) {
            $table->tinyInteger('partner_request_type')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wish', function (Blueprint $table) {
            $table->dropColumn('partner_request_type');
            $table->dropColumn('deleted_at');
        });
    }
}
