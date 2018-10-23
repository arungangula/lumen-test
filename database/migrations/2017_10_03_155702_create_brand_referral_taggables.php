<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandReferralTaggables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_referral_taggables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_referral_id');
            $table->integer('brand_referral_taggable_id');
            $table->string('brand_referral_taggable_type');
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
        Schema::drop('brand_referral_taggables');
    }
}
