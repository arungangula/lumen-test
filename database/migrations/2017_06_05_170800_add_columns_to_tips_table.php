<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_tips', function($table) {
            $table->string("tip_deeplink");
            $table->string("tip_cta_more")->default('Ask An Expert');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_tips', function($table) {
            $table->dropColumn("tip_deeplink");
            $table->dropColumn("tip_cta_more");
        });
    }
}
