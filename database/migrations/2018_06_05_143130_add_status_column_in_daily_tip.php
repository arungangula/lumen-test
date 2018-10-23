<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusColumnInDailyTip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_tips', function($table) {
            $table->integer('language_id')->default(1);
        });

        Schema::table('bc_metrics', function($table) {
            $table->text('metric_description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_tips', function($table) {
            $table->dropColumn('language_id');
        });
    }
}
