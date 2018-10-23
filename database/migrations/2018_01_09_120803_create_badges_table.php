<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badges', function($table) {
            $table->increments('id');
            $table->string('badge_name');
            $table->string('badge_status');
            $table->string('badge_image');
            $table->string('badge_feed_image');
            $table->integer('badge_he_criteria');
            $table->integer('badge_le_criteria');
            $table->string('badge_criteria_decider');
            $table->string('badge_misc_criteria');
            $table->datetime('badge_valid_from');
            $table->datetime('badge_valid_to');
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
        Schema::drop('badges');
    }
}
