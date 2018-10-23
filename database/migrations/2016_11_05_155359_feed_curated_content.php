<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FeedCuratedContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_curated_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('element_id');
            $table->string('element_type')->default('');
            $table->integer('lifestage_id');
            $table->integer('age'); // no of weeks/months
            $table->string('age_unit'); // week/month
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
        Schema::drop('feed_curated_content');
    }
}
