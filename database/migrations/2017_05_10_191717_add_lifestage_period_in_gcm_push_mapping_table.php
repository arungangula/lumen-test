<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLifestagePeriodInGcmPushMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gcm_push_mapping', function (Blueprint $table) {
            $table->string('lifestage_period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gcm_push_mapping', function (Blueprint $table) {
            //
        });
    }
}
