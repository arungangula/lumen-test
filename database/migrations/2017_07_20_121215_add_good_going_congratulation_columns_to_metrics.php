<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodGoingCongratulationColumnsToMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_metrics', function($table) {
            $table->string('metric_good_going');
            $table->string('metric_congratulations');
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
            $table->dropColumn('metric_good_going');
            $table->dropColumn('metric_congratulations');
        });
    }
}
