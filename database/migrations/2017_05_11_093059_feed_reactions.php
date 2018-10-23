<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FeedReactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('reactions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('entity_type');
            $table->integer('entity_id')->default(0);
            $table->string('reaction');
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
