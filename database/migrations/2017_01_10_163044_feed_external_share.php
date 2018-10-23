<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FeedExternalShare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_share', function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('entity_id');
            $table->string('entity_type', 50);
            $table->string('shared_on', 50)->default('unknown');
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
        Schema::drop('feed_share');
    }
}
