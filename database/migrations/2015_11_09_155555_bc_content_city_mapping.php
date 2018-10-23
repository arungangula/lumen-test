<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BcContentCityMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('bc_content_city_mapping', function (Blueprint $table) {
            $table->integer('article_id');
            $table->integer('location_id');
            $table->integer('city_id');
            $table->integer('area_id');
            $table->timestamps();
        });

        //event lifestage mapping
        Schema::create('events_lifestage_mapping', function (Blueprint $table) {
            $table->integer('event_id');
            $table->integer('lifestage_id');
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
        //
    }
}
