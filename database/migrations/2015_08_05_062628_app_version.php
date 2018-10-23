<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AppVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_mobile_app_api_signature_mapping', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('app_version')->unique();
            $table->string('app_signature')->unique();
            $table->string('target_api_version');
            $table->string('download_path',1000);
            $table->string('app_agent')->default('android');
            
        });

        Schema::create('bc_mobile_api_versioning', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('api_version')->unique();
            $table->string('update_status')->default('no');
            $table->dateTime('last_support_date');
            $table->string('min_update_version');
            
        });
    

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        Schema::drop('bc_mobile_app_api_signature');
        Schema::drop('bc_mobile_api_versioning');
    
    }

}
