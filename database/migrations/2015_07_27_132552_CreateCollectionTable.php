<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_content_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description',1000);
            $table->string('cover_image',500);
            $table->string('seo_url')->unique();
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('status')->default('inactive');
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
        Schema::drop('bc_content_collections');
    }
}
