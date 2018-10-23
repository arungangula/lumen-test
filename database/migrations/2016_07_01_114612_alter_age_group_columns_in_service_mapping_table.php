<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAgeGroupColumnsInServiceMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_provider_category_mapping_new', function (Blueprint $table) {
            
            $table->integer('age_group_max')->unsigned()->change();
            $table->integer('age_group_min')->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_provider_category_mapping_new', function (Blueprint $table) {
            //
        });
    }
}
