<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceBankDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_bank_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->string('name');
            $table->string('account_number');
            $table->string('ifsc_code');
	    $table->string('branch_name');
            $table->string('service_tax_no');
            $table->string('verified');
            $table->timestamps();
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
