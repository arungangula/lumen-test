<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('post_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('post');
            $table->string('image');
            $table->integer('published')->default(1);
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
        Schema::drop('post_content');
    }
}
