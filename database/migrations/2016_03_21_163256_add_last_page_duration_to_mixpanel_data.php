<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastPageDurationToMixpanelData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mixpanel_data', function (Blueprint $table)
        {
            $table->string('last_page_duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mixpanel_data', function (Blueprint $table) {
            //
        });
    }
}
