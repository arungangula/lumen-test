<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGatewayResponseAndModeInPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_new', function(Blueprint $table) {
            $table->string('pg_method');
            $table->string('pg_method_type');
            $table->string('pg_error_message');
            $table->string('pg_error_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments_new', function(Blueprint $table) {
            $table->dropColumn('pg_method');
            $table->dropColumn('pg_method_type');
            $table->dropColumn('pg_error_message');
            $table->dropColumn('pg_error_code');
        });
    }
}
