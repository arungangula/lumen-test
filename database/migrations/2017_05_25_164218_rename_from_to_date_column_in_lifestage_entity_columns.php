<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFromToDateColumnInLifestageEntityColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_lifestages', function (Blueprint $table) {
            $table->renameColumn('from_day', 'start_day');
            $table->renameColumn('to_day', 'end_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entity_lifestages', function (Blueprint $table) {
            //
        });
    }
}
