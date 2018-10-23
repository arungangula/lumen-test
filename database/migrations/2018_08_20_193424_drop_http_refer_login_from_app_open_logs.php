<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropHttpReferLoginFromAppOpenLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_open_logs', function (Blueprint $table) {
            $table->dropColumn(['http_referer', 'login_from']);
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
