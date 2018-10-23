<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingpopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketingpopups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('image');
            $table->text('description');
            $table->string('cta_text');
            $table->string('cta_deeplink');
            $table->string('version')->default('v1');
            $table->integer('min_view_count');
            $table->string('platform');
            $table->string('platform_version');
            $table->dateTime('valid_from');
            $table->dateTime('valid_till');
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
        Schema::drop('marketingpopups');
    }
}
