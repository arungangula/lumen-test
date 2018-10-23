<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRouteNewNameToLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_master', function (Blueprint $table) {
            $table->integer('location_route_id')->unsigned();
            $table->string('location_old_name');
            $table->string('location_old_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_master', function (Blueprint $table) {
            //
        });
    }
}
