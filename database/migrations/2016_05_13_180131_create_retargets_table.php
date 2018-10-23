<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retargets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('desc',2000);
            $table->integer('start');
            $table->integer('end');
            $table->string('dependencies');
            $table->integer('resource_id');
            $table->string('resource_type');
            $table->string('channels');
            $table->integer('recurring');
            $table->string('metadata',2000);
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
        Schema::drop('retargets');
    }
}
