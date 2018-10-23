<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateGameLeaderboard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leaderboards', function($table) {
            $table->increments('id');
            $table->integer('game_id');
            $table->integer('user_id');
            $table->integer('spins_count');
            $table->bigInteger('score');
            $table->date('score_date');
            $table->string('game_slot');
            $table->timestamps();

            $table->index('game_id');
            $table->index('user_id');
            $table->index('score');
            $table->index('game_slot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('game_leaderboards');
    }
}
