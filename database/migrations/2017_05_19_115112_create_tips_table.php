<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_tips', function($table) {
            $table->increments('id');
            $table->string('tip_description');
            $table->string("lifestage_range");
            $table->integer("lifestage_start_date");
            $table->integer("lifestage_end_date");
            $table->integer("metric_id");
            $table->integer("milestone_id");
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
        Schema::drop('bc_tips');
    }
}
