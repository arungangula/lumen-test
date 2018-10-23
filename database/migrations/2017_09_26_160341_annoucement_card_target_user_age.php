<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnnoucementCardTargetUserAge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->integer('target_user_age_from')->default(-1);
            $table->integer('target_user_age_to')->default(-1);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->integer('target_user_age_from')->default(-1);
            $table->integer('target_user_age_to')->default(-1);
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
