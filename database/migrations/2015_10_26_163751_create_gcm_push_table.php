<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGcmPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gcm_push', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sitem_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('content');
            $table->string('image');
            $table->string('deeplink');
            $table->string('notification_type');
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
        Schema::drop('gcm_push');
    }
}
