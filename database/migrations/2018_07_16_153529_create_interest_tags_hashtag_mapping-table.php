<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestTagsHashtagMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_tags_hashtag_mapping', function($table) {
            $table->increments('id');
            $table->integer('tag_id');
            $table->integer('hashtag_id');
            $table->timestamps();

            $table->index('tag_id');
            $table->index('hashtag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('interest_tags_hashtag_mapping');
    }
}
