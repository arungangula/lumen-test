<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthorToArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('bc_content', function(Blueprint $table){

            $table->integer('author_id')->unsigned()->nullable()->default(null);
            $table->dropColumn('fulltext');
        
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
         Schema::table('bc_content', function(Blueprint $table){

            $table->dropColumn('author_id');
        
        });
    }
}
