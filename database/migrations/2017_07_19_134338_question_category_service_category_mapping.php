<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QuestionCategoryServiceCategoryMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_category_service_category_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_category_id');
            $table->integer('lifestage_id');
            $table->integer('service_category_id');
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
