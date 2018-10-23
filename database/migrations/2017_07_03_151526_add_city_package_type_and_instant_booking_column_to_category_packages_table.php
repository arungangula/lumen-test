<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityPackageTypeAndInstantBookingColumnToCategoryPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_packages', function (Blueprint $table) {
            $table->integer('city_id');
            $table->integer('instant_booking');
            $table->string('package_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_packages', function (Blueprint $table) {
            //
        });
    }
}
