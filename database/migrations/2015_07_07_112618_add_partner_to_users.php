<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartnerToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('bc_users', function(Blueprint $table){
            $table->string('partner_status',10)->default('no');
            $table->integer('partner_id')->default(0);
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_users', function (Blueprint $table) {
            //
        });
    }
}
