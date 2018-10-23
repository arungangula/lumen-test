<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_user_metrics', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('metric_id');
            $table->string('metric_value');
            $table->string('metric_achieved');
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
        Schema::drop('bc_user_mertics');
    }
}
