<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGcmPushMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gcm_push_mapping', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('gcm_push_id')->unsigned();
            $table->integer('lifestage_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->integer('area_id')->unsigned();
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
        Schema::drop('gcm_push_mapping');
    }
}
