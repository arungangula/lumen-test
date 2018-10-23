<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAppOpenLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_open_logs', function($table) {
            $table->integer('build_version');
            $table->string('gcm_token');
            $table->string('android_id');
            $table->string('action')->default('app_open');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_open_logs', function($table) {
            $table->dropColumn('build_version');
            $table->dropColumn('gcm_token');
            $table->dropColumn('android_id');
            $table->dropColumn('action');
        });
    }
}
