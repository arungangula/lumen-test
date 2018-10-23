<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostSpam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedpost_spam', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('element_id');
            $table->string('element_type')->default('');
            $table->integer('user_id');
            $table->string('action');
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
        Schema::drop('feedpost_spam');
    }
}
