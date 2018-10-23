<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecommendationResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendation_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id');
            $table->string('uuid');
            $table->string('item_uuid');
            $table->timestamps();
            $table->index('uuid');
            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recommendation_responses');
    }
}
