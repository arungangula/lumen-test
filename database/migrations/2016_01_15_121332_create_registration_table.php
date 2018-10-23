<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id');
            $table->string('business_name');
            $table->string('person_name');
            $table->string('email');
            $table->string('mobile_no');
            $table->integer('city_id');
            $table->text('other_details');
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
