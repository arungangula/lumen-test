<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNotificationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification_logs', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('entity_id');
            $table->string('entity_type');
            $table->timestamps();

            $table->index('user_id');
            $table->index('entity_id');
            $table->index('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_notification_logs');
    }
}
