<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsToPartnerMenuImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_menu_images', function (Blueprint $table) {
            $table->text('image_caption')->nullable();
            $table->text('pinned')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_menu_images', function (Blueprint $table) {
            $table->dropColumn('image_caption');
            $table->dropColumn('pinned');
        });
    }
}
