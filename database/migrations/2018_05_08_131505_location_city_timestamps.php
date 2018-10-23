<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocationCityTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_master', function($table)
        {
            $table->dateTime('created_at')->change();
            $table->dateTime('updated_at')->change();
        });

        Schema::table('city_master', function($table)
        {
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
