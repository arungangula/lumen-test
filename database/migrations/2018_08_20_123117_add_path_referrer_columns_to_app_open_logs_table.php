<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPathReferrerColumnsToAppOpenLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_open_logs', function (Blueprint $table) {
            $table->string('before_login_url');
            $table->string('after_login_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_open_logs', function (Blueprint $table) {
            //
        });
    }
}
