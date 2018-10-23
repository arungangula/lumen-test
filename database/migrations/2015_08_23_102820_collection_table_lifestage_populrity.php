<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CollectionTableLifestagePopulrity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::table('bc_content_collections', function(Blueprint $table){
            
            $table->integer('popularity')->default(20);
        
        });

        Schema::create('bc_collection_lifestage_mapping', function(Blueprint $table){
            
            $table->increments('id');
            $table->integer('collection_id')->unsigned();
            $table->integer('lifestage_id')->unsigned();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    // Create table for storing roles
        Schema::table('bc_content_collections', function(Blueprint $table){
            
            $table->dropColumn('popularity');
        
        });
        
         Schema::drop('bc_collection_lifestage_mapping');

    }
}
