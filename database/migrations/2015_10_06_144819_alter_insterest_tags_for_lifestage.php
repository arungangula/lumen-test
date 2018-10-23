<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInsterestTagsForLifestage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_tags_lifestage_mapping', function (Blueprint $table) {
            $table->integer('tag_id');
            $table->integer('lifestage_id');
            $table->string('display_message');
            $table->timestamps();
            $table->primary(['tag_id', 'lifestage_id']);
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
