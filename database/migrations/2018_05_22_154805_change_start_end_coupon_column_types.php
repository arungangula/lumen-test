<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStartEndCouponColumnTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function($table) {
            $table->dateTime('coupon_valid_start_date')->change();
            $table->dateTime('coupon_valid_end_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function($table) {
            $table->date('coupon_valid_start_date')->change();
            $table->date('coupon_valid_end_date')->change();
        });
    }
}
