<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KeywordsRepository extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('bc_keywords_repo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text')->unique();
            $table->string('type')->default('general');
            $table->integer('usage')->unsigned();
            $table->integer('sort_order');
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
        //
         Schema::drop('bc_keywords_repo');
    }
}
