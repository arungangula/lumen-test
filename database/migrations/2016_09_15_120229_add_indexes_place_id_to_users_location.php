<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesPlaceIdToUsersLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_location', function (Blueprint $table)
        {
            $table->index('user_id');
            $table->index('location_id');
            $table->string('place_id');
            $table->index('place_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_location', function (Blueprint $table) {
            //
        });
    }
}
