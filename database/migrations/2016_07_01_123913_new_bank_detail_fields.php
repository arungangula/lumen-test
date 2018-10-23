<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewBankDetailFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('service_bank_details', function (Blueprint $table) {
            $table->string('pan_no')->after('name');
            $table->string('tin_vat_no')->after('pan_no');
            $table->string('bank_state')->after('branch_name');
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
