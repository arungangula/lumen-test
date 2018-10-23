<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserSourceTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_user_source', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('source_url');
            $table->string('uses');
            $table->string('type');
            $table->string('utm_source');
            $table->string('utm_medium');
            $table->string('utm_campaign');
            $table->string('utm_keywords');
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
        Schema::drop('notification_panel');
    }
}
