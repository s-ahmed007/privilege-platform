<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_terms', function (Blueprint $table) {
            $table->id();
            $table->integer('partner_id');
            $table->string('name');
            $table->text('area');
            $table->longText('images');
            $table->decimal('rating', 4, 2);
            $table->longText('offer');
            $table->longText('branches');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_terms');
    }
}
