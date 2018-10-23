<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotedFeeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('promoted_feeds', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('entity_id');
            $table->string('entity_type', 50);
            $table->text('user_ids');
            $table->tinyInteger('is_promoted')->default(0);
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
