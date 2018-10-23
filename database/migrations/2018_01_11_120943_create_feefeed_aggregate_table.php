<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeefeedAggregateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_impression_aggregates', function($table) {
            $table->increments('id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->integer('impressions_count');
            $table->date('impressions_date');
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
        Schema::drop('feed_impression_aggregates');
    }
}
