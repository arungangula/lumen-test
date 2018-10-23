<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_scores', function($table) {
            $table->increments('id');
            $table->integer('game_id');
            $table->integer('user_id');
            $table->integer('score');
            $table->string('prize');
            $table->string('game_slot');
            $table->timestamps();

            $table->index('game_id');
            $table->index('user_id');
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
        Schema::drop('game_scores');
    }
}
