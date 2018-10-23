<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchLogArchivalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_logs_archive', function($table) {
            $table->increments('id');
            $table->string('source');
            $table->integer('user_id');
            $table->string('query');
            $table->string('type');
            $table->integer('total');
            $table->integer('offset');
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
        Schema::drop('search_logs_archive');
    }
}
