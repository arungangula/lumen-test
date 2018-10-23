<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandStoryUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_story_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_story_id');
            $table->integer('user_id');
            $table->timestamp('viewed_on');
            $table->timestamps();

            $table->index(['brand_story_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand_story_users');
    }
}
