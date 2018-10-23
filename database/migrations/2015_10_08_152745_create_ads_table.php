<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_sponsored_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ad_title');
            $table->string('image');
            $table->string('weblink');
            $table->string('deeplink');
            $table->string('aditem_type');
            $table->integer('aditem_id')->unsigned();
            $table->integer('clicks')->unsigned();
            $table->integer('calls')->unsigned();
            $table->string('other_details',2000);
            $table->timestamps();
        });

        Schema::create('bc_sitem_lf_mapping', function (Blueprint $table) {
            $table->integer('sitem_id')->unsigned();
            $table->integer('lifestage_id')->unsigned();
            $table->timestamps();
            $table->primary(['sitem_id', 'lifestage_id']);
        });

        Schema::create('bc_sitem_loc_mapping', function (Blueprint $table) {
            $table->integer('sitem_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->timestamps();
            $table->primary(['sitem_id', 'location_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('bc_sponsored_items');
         Schema::drop('bc_sitem_lf_mapping');
         Schema::drop('bc_sitem_loc_mapping');
    }
}
