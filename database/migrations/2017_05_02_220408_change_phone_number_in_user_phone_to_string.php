<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePhoneNumberInUserPhoneToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_user_phone', function (Blueprint $table) {
            
            $table->string('phone_number')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_user_phone', function (Blueprint $table) {
            $table->integer('phone_number')->change();
        });
    }
}
