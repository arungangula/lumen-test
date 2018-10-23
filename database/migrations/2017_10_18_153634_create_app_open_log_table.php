<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppOpenLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_open_logs', function($table) {
            $table->increments('id');
            $table->string('channel');
            $table->integer('user_id');
            $table->string('app_version');
            $table->string('device_id');
            $table->string('os_version');
            $table->timestamps();

            $table->index('channel');
            $table->index('app_version');
            $table->index('os_version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_open_logs');
    }
}
