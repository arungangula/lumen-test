<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function(Blueprint $table){
            $table->integer('coupon_maximum_use_count_by_user')->default(1);
            $table->string('source')->default('both');
            $table->date('coupon_valid_date_for_signedup_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function(Blueprint $table){
            $table->dropColumn('coupon_maximum_use_count_by_user');
            $table->dropColumn('coupon_valid_date_for_signedup_users');
        });
    }
}
