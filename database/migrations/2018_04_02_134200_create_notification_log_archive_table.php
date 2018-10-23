<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationLogArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notification_log_archive', function($table) {
            $table->integer('id');
            $table->integer('element_id');
            $table->string('element_type');
            $table->bigInteger('people_count');
            $table->bigInteger('success');
            $table->bigInteger('failure');
            $table->text('result_dump');
            $table->integer('converted')->nullable();
            $table->integer('remaining')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('push_notification_log_archive');
    }
}
