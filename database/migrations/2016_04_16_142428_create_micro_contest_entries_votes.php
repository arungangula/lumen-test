<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMicroContestEntriesVotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('micro_contest_entry_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contest_entry_id')->unsigned();
            $table->string('user_id')->nullable();
            $table->string('user_ip')->nullable();
            $table->boolean('published');
            $table->timestamp('created_date');
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
