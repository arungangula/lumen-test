<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCtaBgColorsToMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_metrics', function($table) {
            $table->string('first_cta_text_color');
            $table->string('first_cta_button_style');
            $table->string('first_cta_bg_color');
            $table->string('second_cta_text_color');
            $table->string('second_cta_bg_color');
            $table->string('second_cta_button_style');
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
            $table->dropColumn('first_cta_text_color');
            $table->dropColumn('first_cta_button_style');
            $table->dropColumn('first_cta_bg_color');
            $table->dropColumn('second_cta_text_color');
            $table->dropColumn('second_cta_bg_color');
            $table->dropColumn('second_cta_button_style');
        });
    }
}
