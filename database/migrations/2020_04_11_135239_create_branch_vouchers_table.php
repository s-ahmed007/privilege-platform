<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->json('date_duration')->nullable();
            $table->json('weekdays')->nullable();
            $table->json('time_duration')->nullable();
            $table->integer('point')->nullable();
            $table->tinyInteger('active');
            $table->string('heading', 255);
            $table->decimal('actual_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->integer('counter_limit')->nullable();
            $table->integer('scan_limit')->nullable();
            $table->text('tnc')->nullable();
            $table->text('description')->nullable();
            $table->string('valid_for')->nullable();
            $table->integer('priority')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('commission_type')->nullable();
            $table->decimal('commission', 10, 2)->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('branch_vouchers');
    }
}
