<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailShooterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_shooters', function($table) {
            $table->increments('id');
            $table->string('subject');
            $table->text('html');
            $table->string('status')->default('created');
            $table->string('file_names');
            $table->string('batch');
            $table->timestamp('start_from');
            $table->integer('created_by');
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
        Schema::drop('email_shooters');
    }
}
