<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScheduleAtColumnInMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_metrics', function($table) {
            $table->time('schedule_at');
            $table->string('metric_usage')->default(App\Models\Metric::METRIC_USAGE_CARD_ONLY);
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
            $table->dropColumn('schedule_at');
            $table->dropColumn('metric_usage');
        });
    }
}
