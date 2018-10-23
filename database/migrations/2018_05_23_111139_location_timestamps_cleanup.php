<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocationTimestampsCleanup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // update timestamps for location_master
        Schema::table('location_master', function($table) {
            $table->dateTime('created_at')->change();
            $table->dateTime('updated_at')->change();
            $table->string('place_id');
        });


        // update timestamps for city_master
        Schema::table('city_master', function($table) {
            $table->dateTime('created_at')->change();
            $table->dateTime('updated_at')->change();
        });


        // update timestamps for state_master
        Schema::table('state_master', function($table) {
            $table->dateTime('created_at')->change();
            $table->dateTime('updated_at')->change();
        });


        // update timestamps for country_master
        Schema::table('country_master', function($table) {
            $table->dateTime('created_at')->change();
            $table->dateTime('updated_at')->change();
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
