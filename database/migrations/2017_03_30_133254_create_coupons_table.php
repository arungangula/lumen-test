<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('coupon_code');
            $table->string('coupon_description');
            $table->double('coupon_minimum_amount');
            $table->double('coupon_maximum_amount');
            $table->string('coupon_type');
            $table->integer('coupon_discount');
            $table->date('coupon_valid_start_date');
            $table->date('coupon_valid_end_date');
            $table->string('coupon_maximum_use_count');
            $table->string('coupon_current_use_count');
            $table->string('coupon_redeem_type');
            $table->integer('coupon_created_user_id');
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
        Schema::drop('coupons');
    }
}
