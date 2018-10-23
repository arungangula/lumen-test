<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_user_contacts', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('additional_name');
            $table->string('full_name');
            $table->string('phone_number');
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
        Schema::drop('bc_user_contacts');
    }
}
