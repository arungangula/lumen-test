<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_stories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('service_id');
            $table->string('image');
            $table->longText('description');
            $table->timestamp('show_on');
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
        Schema::drop('brand_stories');
    }
}
