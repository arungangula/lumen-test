<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExpertCategoryAllDbChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experts_category', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('user_experts_category_mapping', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('experts_category_id');
            $table->timestamps();
        });

        Schema::create('question_experts_category_mapping', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('question_id');
            $table->integer('experts_category_id');
            $table->timestamps();
        });

        Schema::create('experts_category_lifestage_mapping', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('lifestage_id');
            $table->integer('experts_category_id');
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
        Schema::drop('experts_category');
        Schema::drop('user_experts_category_mapping');
        Schema::drop('question_experts_category_mapping');
        Schema::drop('experts_category_lifestage_mapping');
    }
}
