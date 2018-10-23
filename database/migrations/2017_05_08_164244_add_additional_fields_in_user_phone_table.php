<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsInUserPhoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_user_phone', function (Blueprint $table) {
            $table->string('network_carrier');
            $table->string('network_circle');
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
            $table->dropColumn('network_carrier');
            $table->dropColumn('network_circle');
        });
    }
}
