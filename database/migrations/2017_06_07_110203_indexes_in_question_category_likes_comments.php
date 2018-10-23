<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexesInQuestionCategoryLikesComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_category_mapping', function (Blueprint $table)
        {
            $table->index('question_id');
        });

        Schema::table('post_likes', function (Blueprint $table)
        {
            $table->index('user_id');
            $table->index('element_id');
            $table->index('element_type');
        });

        Schema::table('bc_commentable', function (Blueprint $table)
        {
            $table->index('user_id');
            $table->index('commentable_id');
            $table->index('commentable_type');
        });
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
