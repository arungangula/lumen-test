<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromToAgeOnAppInGcmPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gcm_push', function ($table) {
            $table->integer('target_user_age_from')->default(-1);
            $table->integer('target_user_age_to')->default(-1);
            $table->integer('recurring_push')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('target_user_age_from');
            $table->dropColumn('target_user_age_to');
            $table->dropColumn('recurring_push');
        });
    }
}
