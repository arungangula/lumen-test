<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_otps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('phone_number');
            $table->string('otp_number');
            $table->integer('verified');
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
        Schema::drop('MobileOtp');
    }
}
