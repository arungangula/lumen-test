<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToContestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_master', function($table) {
            $table->string('participants_name_enabled')->default(1);
            $table->string('participants_phone_enabled')->default(1);
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
            $table->dropColumn('participants_name_enabled');
            $table->dropColumn('participants_phone_enabled');
        });
    }
}
