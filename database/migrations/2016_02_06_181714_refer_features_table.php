<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReferFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refer_event', function (Blueprint $table) {
            
            $table->string('event_code');
            $table->primary('event_code');
            $table->integer('referrer_points')->unsigned();
            $table->integer('friend_points')->unsigned(); //person clicks on link
            $table->integer('frequency')->unsigned(); //number of times this link is applicable 
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
        Schema::drop('refer_features');
    }
}
