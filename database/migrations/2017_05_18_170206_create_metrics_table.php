<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bc_metrics', function($table) {
            $table->increments("id");
            $table->string("metric_name");
            $table->string("metric_type");
            $table->string("metric_title");
            $table->string("metric_description");
            $table->string("metric_hint_text");
            $table->string("metric_image");
            $table->string("metric_unit");
            $table->string("metric_input_type");
            $table->string("metric_template");
            $table->string("show_extra_info");
            $table->string("lifestage_range");
            $table->integer("lifestage_start_date");
            $table->integer("lifestage_end_date");
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
        Schema::drop('bc_metrics');
    }
}
