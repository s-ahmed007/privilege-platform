<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToReviewCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_comment', function (Blueprint $table) {
            $table->tinyInteger('moderation_status')->default(0);
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
        Schema::table('review_comment', function (Blueprint $table) {
            $table->dropColumn('moderation_status');
            $table->dropColumn('deleted_at');
        });
    }
}
