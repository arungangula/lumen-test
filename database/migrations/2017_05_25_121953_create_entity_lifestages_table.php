<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityLifestagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_lifestages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id');
            $table->string('entity_type');
            $table->string('lifestage_period');
            $table->integer('from_day');
            $table->integer('to_day');
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
        Schema::drop('entity_lifestages');
    }
}
