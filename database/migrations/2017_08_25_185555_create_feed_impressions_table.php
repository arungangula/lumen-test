<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedImpressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_impressions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->integer('lifestage_id');
            $table->integer('impressions_count');
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
        Schema::drop('feed_impressions');
    }
}
