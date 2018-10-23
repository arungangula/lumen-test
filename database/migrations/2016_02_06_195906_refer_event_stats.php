<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReferEventStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refer_event_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('refer_link');
            $table->string('refer_link_id');
            $table->string('event_code');
            $table->integer('tuser_id')->unsigned();
            $table->integer('ref_points')->unsigned();
            $table->integer('frnd_points')->unsigned();
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
        Schema::drop('refer_event_stats');
    }
}
