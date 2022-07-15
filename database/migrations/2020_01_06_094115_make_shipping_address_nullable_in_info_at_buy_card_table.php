<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeShippingAddressNullableInInfoAtBuyCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->string('shipping_address')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->string('shipping_address')->change();
        });
    }
}
