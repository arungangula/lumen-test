<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameExpertCategoryToQuestionCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::rename('experts_category', 'question_category');
        Schema::rename('user_experts_category_mapping', 'user_question_category_mapping');
        Schema::rename('question_experts_category_mapping', 'question_category_mapping');
        Schema::rename('experts_category_lifestage_mapping', 'question_category_lifestage_mapping');
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
