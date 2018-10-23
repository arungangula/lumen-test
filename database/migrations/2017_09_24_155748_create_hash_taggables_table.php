<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHashTaggablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hash_taggables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hash_tag_id');
            $table->integer('hash_taggable_id');
            $table->string('hash_taggable_type', 100);
            $table->timestamps();

            $table->index('hash_tag_id');
            $table->index('hash_taggable_id');
            $table->index('hash_taggable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hash_taggables');
    }
}
