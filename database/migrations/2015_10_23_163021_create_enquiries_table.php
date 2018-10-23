<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enquiry_from_id');
            $table->string('enquiry_type');
            $table->integer('user_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('location');
            $table->text('enquiry');
            $table->json('extra_fields');
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