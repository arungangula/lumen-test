<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaDataToCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_content_collections', function (Blueprint $table) {
            
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('default_article_meta_title');
            $table->text('default_article_meta_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_content_collections', function (Blueprint $table) {
            //
        });
    }
}
