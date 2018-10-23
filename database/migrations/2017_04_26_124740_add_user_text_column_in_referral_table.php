<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTextColumnInReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("bc_user_referral", function (Blueprint $table) {
            $table->string("referral_name");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("bc_user_referral", function (Blueprint $table) {
            $table->dropColumn("referral_name");
        });
    }
}
