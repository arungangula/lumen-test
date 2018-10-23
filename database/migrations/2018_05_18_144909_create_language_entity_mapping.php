<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguageEntityMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_entity_mappings', function($table) {
            $table->increments('id');
            $table->integer('language_id');
            $table->integer('entity_id');
            $table->string('entity_type');
            $table->timestamps();

            $table->index('language_id');
            $table->index('entity_id');
            $table->index('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('language_entity_mappings');
    }
}
