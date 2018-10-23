<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusInOtpVerification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_otps', function(Blueprint $table) {
            $table->integer('payment_id');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_otps', function(Blueprint $table) {
            $table->dropColumn('payment_id');
            $table->dropColumn('status');
        });
    }
}
