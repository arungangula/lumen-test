<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandReferralCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_referral_codes', function($table) {
            $table->increments('id');
            $table->string('brand_id');
            $table->string('code');
            $table->string('description');
            $table->string('status');
            $table->integer('max_usage');
            $table->integer('total_usage');
            $table->date('valid_start_date');
            $table->date('valid_end_date');
            $table->integer('last_modified_by');
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
        Schema::drop('brand_referral_codes');
    }
}
