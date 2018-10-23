<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAppIdentifierToAppVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_mobile_app_api_signature_mapping', function (Blueprint $table) {
            $table->string('app_identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_mobile_app_api_signature_mapping', function (Blueprint $table) {
            //
        });
    }
}
