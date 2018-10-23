<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyTips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('daily_tips', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->integer('article_id');
            $table->integer('collection_id');
            $table->integer('subcategory_id');
            $table->integer('service_provider_id');
            $table->string('link_to');
            $table->integer('babys_age');
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
    }
}
