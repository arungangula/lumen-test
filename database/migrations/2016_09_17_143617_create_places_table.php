<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('place_id');
            $table->string('name');
            $table->integer('location_id');
            $table->integer('city_id');
            $table->integer('area_id');
            $table->double('lat', 15, 8);
            $table->double('lon', 15, 8);
            $table->string('city_name');
            $table->string('area_name');
            $table->timestamps();
            $table->index('place_id');
            $table->index('lat');
            $table->index('lon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('places');
    }
}
