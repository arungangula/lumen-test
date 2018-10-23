<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsletterMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('newsletter_id');
            $table->integer('element_id');
            $table->string('element_type');
            $table->text('other_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('newsletter_mapping');
    }
}
