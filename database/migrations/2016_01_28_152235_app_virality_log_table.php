<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AppViralityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_virality_event_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('eventname');
            $table->string('success');
            $table->integer('campaignid')->unsigned();
            $table->string('campaignname');
            $table->integer('friend_id')->unsigned();
            $table->integer('referrer_id')->unsigned();
            $table->string('friend_email');
            $table->string('referrer_email');
            $table->timestamps();
            $table->text('dump');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_virality_event_log');
    }
}
