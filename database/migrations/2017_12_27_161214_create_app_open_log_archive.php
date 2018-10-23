<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppOpenLogArchive extends Migration
{
    public function up()
    {
        Schema::create('app_open_logs_archive', function($table) {
            $table->increments('id');
            $table->string('channel');
            $table->integer('user_id');
            $table->string('app_version');
            $table->string('device_id');
            $table->string('os_version');
            $table->string('action');
            $table->integer('build_version');
            $table->string('gcm_token');
            $table->string('android_id');
            $table->timestamps();
            $table->index('channel');
            $table->index('app_version');
            $table->index('os_version');
        });
    }

    
    public function down()
    {
        Schema::drop('app_open_logs_archive');
    }

}
