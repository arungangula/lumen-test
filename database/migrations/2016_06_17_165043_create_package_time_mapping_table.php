<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTimeMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_time_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id');
            $table->string('day');
            $table->string('start_time');
            $table->string('end_time');
            $table->timestamps();
            $table->index('package_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('package_time_mapping');
    }
}
