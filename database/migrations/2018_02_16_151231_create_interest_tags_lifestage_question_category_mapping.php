<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestTagsLifestageQuestionCategoryMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_tags_lifestage_question_category_mapping', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id');
            $table->integer('lifestage_id');
            $table->integer('question_category_id');
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
        //
    }
}
