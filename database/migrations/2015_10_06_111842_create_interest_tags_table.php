<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('interest_tags_article_mapping', function (Blueprint $table) {
            $table->integer('tag_id');
            $table->integer('article_id');
            $table->string('display_message');
            $table->timestamps();
            $table->primary(['tag_id', 'article_id']);
        });

        Schema::create('interest_tags_collection_mapping', function (Blueprint $table) {
            $table->integer('tag_id');
            $table->integer('collection_id');
            $table->string('display_message');
            $table->timestamps();
            $table->primary(['tag_id', 'collection_id']);
        });

        Schema::create('interest_tags_subcategory_mapping', function (Blueprint $table) {
            $table->integer('tag_id');
            $table->integer('sub_category_id');
            $table->string('display_message');
            $table->timestamps();
            $table->primary(['tag_id', 'sub_category_id']);
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
