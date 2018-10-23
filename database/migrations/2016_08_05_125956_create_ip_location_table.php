<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_location', function (Blueprint $table)
        {
            $table->integer('ip_from');
            $table->integer('ip_to');
            $table->char('country_code',2);
            $table->string('country_name');
            $table->string('region_name');
            $table->string('city_name');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('zip_code');
            $table->string('time_zone');
            $table->index('ip_from');
            $table->index('ip_to');
            $table->index(['ip_from', 'ip_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ip_location');
    }
}
