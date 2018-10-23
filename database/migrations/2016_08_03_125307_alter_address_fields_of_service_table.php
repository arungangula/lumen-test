<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddressFieldsOfServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_services_providers_new', function (Blueprint $table) {
            
            $table->string('address_house_number', 2555)->change();
            $table->string('address_street_name', 2555)->change();
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
