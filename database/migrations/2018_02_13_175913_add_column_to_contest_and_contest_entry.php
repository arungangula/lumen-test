<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToContestAndContestEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_master', function($table) {
            $table->tinyInteger('video_link_enabled')->default(1);
        });

        Schema::table('contest_entries', function($table) {
            $table->string('video_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest_master', function($table) {
            $table->dropColumn('video_link_enabled');
        });

        Schema::table('contest_entries', function($table) {
            $table->dropColumn('video_link');
        });
    }
}
