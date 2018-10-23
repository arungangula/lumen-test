<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAppVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_mobile_app_api_signature_mapping', function (Blueprint $table) {
            
            $table->date('last_support_date')->default('0000-00-00')->change();
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
