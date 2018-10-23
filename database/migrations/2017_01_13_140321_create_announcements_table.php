<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->text('text');
            $table->string('image_url');
            $table->string('deeplink');
            $table->string('cta_text');
            $table->string('cta_color');
            $table->string('position');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
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
        Schema::drop('annoucements');
    }
}
