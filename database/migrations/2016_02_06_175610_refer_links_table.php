<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReferLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refer_links', function (Blueprint $table) {
            $table->increments('id');

            $table->string('short_url')->nullable()->unique();
            $table->string('lng_url')->nullable();
            $table->string('feature')->nullable();
            $table->string('channel')->nullable();
            $table->string('device')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();

            $table->integer('user_id')->unsigned();
            $table->json('params');
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
        Schema::drop('refer_links');
    }
}
