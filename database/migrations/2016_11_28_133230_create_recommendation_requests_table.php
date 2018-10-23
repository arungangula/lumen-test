<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecommendationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendation_requests', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('uuid');
            $table->integer('user_id');
            $table->string('requested_entity_type');
            $table->string('entity_type');
            $table->string('entity_id');
            $table->timestamps();
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recommendation_requests');
    }
}
