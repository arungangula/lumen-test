<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeeplinkAccessLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deeplinks_access_log', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('referred_by_user_id');
            $table->integer('element_id')->nullable();
            $table->string('element_type')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('pid');
            $table->string('channel');
            $table->string('access_path');
            $table->json('extra_params');
            $table->timestamps();
            $table->index('user_id');
            $table->index('campaign_name');
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
