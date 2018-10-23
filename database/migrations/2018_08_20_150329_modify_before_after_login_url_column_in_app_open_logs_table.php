<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyBeforeAfterLoginUrlColumnInAppOpenLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_open_logs', function (Blueprint $table) {
            $table->renameColumn('before_login_url', 'http_referer')->nullable()->change();
            $table->renameColumn('after_login_url', 'login_from')->nullable()->change();
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
