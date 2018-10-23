<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ContentCollectionMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_content_collection_mapping', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->integer('collection_id')->unsigned();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bc_content_collection_mapping');
    }
}
