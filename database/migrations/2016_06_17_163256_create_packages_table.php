<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->string('name');
            $table->string('description', 1200);
            $table->string('picture');
            $table->integer('instant_booking');
            $table->integer('price');
            $table->string('duration_unit');
            $table->integer('duration_value');
            $table->string('start_date');
            $table->string('end_date');
            $table->timestamps();
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
        Schema::drop('packages');
    }
}
