<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyTipNotifcationLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_tip_notification_logs', function($table) {
            $table->increments('id');
            $table->integer('element_id');
            $table->string('element_type');
            $table->bigInteger('people_count');
            $table->bigInteger('success');
            $table->bigInteger('failure');
            $table->text('result_dump');
            $table->bigInteger('converted');
            $table->bigInteger('remaining');
            $table->timestamps();

            $table->index('element_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('daily_tip_notification_logs');
    }
}
