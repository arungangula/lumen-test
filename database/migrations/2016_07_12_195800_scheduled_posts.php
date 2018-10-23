<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScheduledPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('post_content_scheduled', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('post_by_user_id');
            $table->text('post')->default(null);
            $table->string('image')->default(null);
            $table->integer('published')->default(1);
            $table->dateTime('scheduled_at');
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
        Schema::drop('post_content_scheduled');
    }
}