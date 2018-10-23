<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLifestageStartEndDaysToPromotion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', function($table) {
            $table->integer('lifestage_start_date');
            $table->integer('lifestage_end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', function($table) {
            $table->dropColumn('lifestage_start_date');
            $table->dropColumn('lifestage_end_date');
        });
    }
}
