<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_elements', function($table) {
            $table->increments('id');
            $table->integer('brand_id');
            $table->string('referral_code');
            $table->string('map_type');
            $table->string('scope');
            $table->string('ui_elements');
            $table->string('image_elements');
            $table->string('sponsored_image');
            $table->string('sponsored_text');
            $table->string('sponsored_text_deeplink');
            $table->string('sponsored_image_deeplink');
            $table->string('sponsored_bg_color');
            $table->string('lifestage_range');
            $table->integer('lifestage_start_date');
            $table->integer('lifestage_end_date');
            $table->date('valid_start_date');
            $table->date('valid_end_date');
            $table->integer('last_modified_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand_elements');
    }
}
