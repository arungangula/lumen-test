<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleDescriptionImageVoteEnabledColumnsToContestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_master', function (Blueprint $table) {

            $table->tinyInteger('title_enabled')->default(1);
            $table->tinyInteger('description_enabled')->default(1);
            $table->tinyInteger('vote_enabled')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest_master', function (Blueprint $table) {
            //
        });
    }
}
