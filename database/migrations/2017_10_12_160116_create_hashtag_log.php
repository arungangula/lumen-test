<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHashtagLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hashtag_logs', function($table) {
            $table->increments('id');
            $table->integer('hashtag_id');
            $table->string('hashtag_name');
            $table->integer('user_id');
            $table->string('hashtag_event');
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
        Schema::drop('hashtag_logs');
    }
}
