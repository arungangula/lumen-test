<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToMetricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_metrics', function($table) {
            $table->date('metric_valid_from');
            $table->date('metric_valid_till');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_metrics', function($table) {
            $table->dropColumn('metric_valid_from');
            $table->dropColumn('metric_valid_till');
        });
    }
}
