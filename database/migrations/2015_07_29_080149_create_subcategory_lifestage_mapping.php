<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcategoryLifestageMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcategory_lifestage_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lifestage_id')->unsigned();
            $table->integer('subcategory_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subcategory_lifestage_mapping');
    }
}
