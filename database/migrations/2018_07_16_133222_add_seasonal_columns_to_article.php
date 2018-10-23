<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeasonalColumnsToArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_content', function($table) {
            $table->integer('is_seasonal');
            $table->timestamp('valid_from');
            $table->timestamp('valid_till');
            $table->integer('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_content', function($table) {
            $table->dropColumn('is_seasonal');
            $table->dropColumn('valid_from');
            $table->dropColumn('valid_till');
            $table->dropColumn('is_recurring');
        });
    }
}
