<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOldCityNameAndSlugToCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city_master', function (Blueprint $table) {
            
            $table->string('city_old_name');
            $table->string('city_old_slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_master', function (Blueprint $table) {
            //
        });
    }
}
