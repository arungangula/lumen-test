<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeywordsMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keywords_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->string('element_type');
            $table->bigInteger('element_id')->unsigned();
            $table->bigInteger('keyword_id')->unsigned();
            $table->index(['element_id', 'keyword_id']);
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
        Schema::drop('keywords_mapping');
    }
}
