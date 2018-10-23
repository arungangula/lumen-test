<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstablishedContactpersonAddressToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_services_providers_new', function (Blueprint $table)
        {
            $table->integer('establishment_year');
            $table->string('contact_person_name');
            $table->string('address_house_number');
            $table->string('address_street_name');
            $table->string('address_landmark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_services_providers_new', function (Blueprint $table) {
            //
        });
    }
}
