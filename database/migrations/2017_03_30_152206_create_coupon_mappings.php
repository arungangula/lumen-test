<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coupon_id');
            $table->integer('coupon_map_id');
            $table->string('coupon_map_type');
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
        Schema::drop('coupon_mappings');
    }
}
