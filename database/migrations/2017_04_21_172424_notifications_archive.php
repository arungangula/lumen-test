<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotificationsArchive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_archive', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('entity_id');
            $table->string('entity_type', 50);
            $table->text('notification_text');
            $table->text('notification_image');
            $table->string('notification_type', 50);
            $table->string('notification_state', 50);
            $table->integer('trigger_user_id');
            $table->timestamp('notification_expiry');
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
        //
    }
}
