<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultDateInCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->date('coupon_valid_start_date')->default(date('Y-m-d'))->change();
            $table->date('coupon_valid_end_date')->default(date('Y-m-d'))->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->date('coupon_valid_start_date')->change();
        $table->date('coupon_valid_end_date')->change();
    }
}
