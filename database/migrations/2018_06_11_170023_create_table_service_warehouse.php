<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServiceWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_warehouses', function($table) {
            $table->increments('id');
            $table->string('logistics_partner');
            $table->integer('service_id');
            $table->string('name');
            $table->string('city');
            $table->string('pincode');
            $table->string('client');
            $table->text('address');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone_number');
            $table->string('status');
            $table->text('message');
            $table->timestamps();

            $table->index('logistics_partner');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_warehouses');
    }
}
