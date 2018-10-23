<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserBadgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_badges', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('badge_id');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('badge_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_badges');
    }
}
