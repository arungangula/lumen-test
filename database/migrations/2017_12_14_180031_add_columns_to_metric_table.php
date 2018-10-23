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
            $table->integer('parent')->default(0);
            $table->string('frequency_month')->default(0);
            $table->string('frequency_week')->default(0);
            $table->string('frequency_day')->default(0);
            $table->integer('expiry_year')->default(0);
            $table->integer('expiry_month')->default(0);
            $table->integer('expiry_week')->default(0);
            $table->integer('expiry_day')->default(0);
            $table->integer('expiry_hour')->default(0);
            $table->integer('expiry_minute')->default(0);
            $table->integer('expiry_second')->default(0);
            $table->string('alignment')->default('horizontal');
            $table->string('position')->default(0);
            $table->string('start_time');
            $table->string('expiry_time');
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
            $table->dropColumn('parent');
            $table->dropColumn('frequency_month');
            $table->dropColumn('frequency_week');
            $table->dropColumn('frequency_day');
            $table->dropColumn('expiry_year');
            $table->dropColumn('expiry_month');
            $table->dropColumn('expiry_week');
            $table->dropColumn('expiry_day');
            $table->dropColumn('expiry_hour');
            $table->dropColumn('expiry_minute');
            $table->dropColumn('expiry_second');
            $table->dropColumn('alignment');
            $table->dropColumn('position');
            $table->dropColumn('start_time');
            $table->dropColumn('expiry_time');
        });
    }
}
