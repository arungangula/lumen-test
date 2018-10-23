<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BcMobileAppApiSignatureMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('bc_mobile_app_api_signature_mapping', function(Blueprint $table)
            {
                $table->integer('android_code_number')->unsigned();
                $table->string('update_status')->default('no');
                $table->string('last_support_date')->default('0000-00-00 00:00:00');
                $table->string('min_update_version');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
