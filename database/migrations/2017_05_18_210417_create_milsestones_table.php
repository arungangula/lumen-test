<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilsestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_milestones', function($table) {
            $table->increments('id');
            $table->string('ideal_start_value_boy');
            $table->string('ideal_end_value_boy');
            $table->string('ideal_start_value_girl');
            $table->string('ideal_end_value_girl');
            $table->string("lifestage_range");
            $table->integer("lifestage_start_date");
            $table->integer("lifestage_end_date");
            $table->integer("metric_id");
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
        Schema::drop('bc_milestones');
    }
}
