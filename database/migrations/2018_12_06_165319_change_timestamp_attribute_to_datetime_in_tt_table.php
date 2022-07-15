<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTimestampAttributeToDatetimeInTtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `transaction_table` CHANGE `posted_on` `posted_on` DATETIME NOT NULL');

//        Schema::table('transaction_table', function (Blueprint $table) {
//            $table->string('posted_on')->change();
//        });
//        Schema::table('transaction_table', function (Blueprint $table) {
//            $table->dateTime('posted_on')->change();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
