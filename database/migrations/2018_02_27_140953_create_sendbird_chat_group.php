<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSendbirdChatGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_groups', function($table) {
            $table->increments('id');
            $table->string('group_name');
            $table->string('group_type');
            $table->string('group_url');
            $table->timestamps();

            $table->index('group_name');
            $table->index('group_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chat_groups');
    }
}
