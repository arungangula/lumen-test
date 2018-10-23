<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandContentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_content_element_mappings', function($table) {
            $table->increments('id');
            $table->integer('brand_element_id');
            $table->string('map_type');
            $table->string('map_value');
            $table->timestamps();

            $table->index('brand_element_id');
            $table->index('map_type');
            $table->index('map_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand_content_element_mappings');
    }
}
