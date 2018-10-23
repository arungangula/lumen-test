<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBrandReferralMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_brands', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('brand_id');
            $table->integer('brand_referral_id');
            $table->string('brand_referral_code');
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
        Schema::drop('user_brands');
    }
}
