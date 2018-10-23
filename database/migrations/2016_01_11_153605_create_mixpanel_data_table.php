<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixpanelDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('mixpanel_data', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->string('event');
            $table->integer('time');
            $table->string('distinct_id');
            $table->string('city');
            $table->string('userId');
            $table->string('initial_referrer');
            $table->string('referrer');
            $table->string('url');
            $table->string('lifestage');
            $table->string('page_type');
            $table->string('screen_type');
            $table->string('platform');
            $table->string('properties',2000)->default('{}');
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
