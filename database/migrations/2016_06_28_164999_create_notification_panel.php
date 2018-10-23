<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationPanel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_panel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('notification_type');
            $table->string('element_id');
            $table->integer('action_user_id');
            $table->integer('manager_id');
            $table->integer('service_id');
            $table->integer('unread')->default(0);
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
