<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ButtonsForDailyCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_metrics', function (Blueprint $table) {
            $table->string('second_cta_text')->default('');
            $table->renameColumn('cta_deeplink', 'second_cta_deeplink');
            $table->string('first_cta_text')->default('');
            $table->string('first_cta_deeplink', 1000)->default('');
        });
        Schema::table('bc_metrics', function (Blueprint $table) {
            $table->string('second_cta_deeplink', 1000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
