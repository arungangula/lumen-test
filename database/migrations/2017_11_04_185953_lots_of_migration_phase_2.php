<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LotsOfMigrationPhase2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        echo "\n packages: ".date('Y-m-d H:i:s', time());
        Schema::table('packages', function($table){
            $table->index('instant_booking');
            $table->index('price');
            $table->index('duration_unit');
            $table->index('duration_value');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
            $table->index('end_price');
            $table->index('package_type');
            $table->index('override_service_shipping_charge');
            $table->index('shipping_charge');
            $table->index('lifestage_period');
            $table->index('show_on_homepage');
        });
  
        echo "\n entity_lifestages: ".date('Y-m-d H:i:s', time());
        Schema::table('entity_lifestages', function($table){
            $table->index('entity_id');
            $table->index('entity_type');
            $table->index('lifestage_period');
            $table->index('start_day');
            $table->index('end_day');
        });

        echo "\n bc_users: ".date('Y-m-d H:i:s', time());
        Schema::table('bc_users', function($table){
            $table->index('referral_id');
            $table->index('referral_code');
            $table->index('refer_user_code');
            $table->index('lifestage_id');
            $table->index('location_id');
            $table->index('city_id');
            $table->index('expert');
        });

        echo "\n promotion_conditions: ".date('Y-m-d H:i:s', time());
        Schema::table('promotion_conditions', function($table){
            $table->index('promotion_id');
            $table->index('condition_type');
            $table->index('condition_id');
        });

        echo "\n location_master: ".date('Y-m-d H:i:s', time());
        Schema::table('location_master', function($table){
            $table->index('zone_id');
            $table->index('city_id');
            $table->index('area_id');
            $table->index('status');
        });

        echo "\n questions: ".date('Y-m-d H:i:s', time());
        Schema::table('questions', function($table){
            $table->index('user_id');
            $table->index('published');
            $table->index('anonymous');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
