<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Deeplinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deeplinks', function($table) {
            $table->increments('id');
            $table->integer('element_id');
            $table->string('element_type');
            $table->string('deeplink');
            $table->string('dynamic_deeplink');
            $table->timestamps();
            $table->index('element_id');
            $table->index('element_type');
            $table->index('deeplink');
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
