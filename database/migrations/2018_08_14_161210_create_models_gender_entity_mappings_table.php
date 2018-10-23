<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelsGenderEntityMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gender_entity_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gender_id');
            $table->integer('entity_id');
            $table->string('entity_type');
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
        Schema::drop('models_gender_entity_mappings');
    }
}
