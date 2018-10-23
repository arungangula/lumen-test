<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBgImageAndColorToBrandReferral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_referral_codes', function($table) {
            $table->string('bg_image');
            $table->string('bg_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand_referral_codes', function($table) {
            $table->dropColumn('bg_image');
            $table->dropColumn('bg_color');
        });
    }
}
