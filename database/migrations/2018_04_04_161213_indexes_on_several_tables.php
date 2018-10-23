<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexesOnSeveralTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_content_lifestages_mapping', function($table){
            $table->index('article_id');
            $table->index('lifestage_value');
        });

        Schema::table('follow_interest_tags', function($table){
            $table->index('user_id');
            $table->index('interest_tag_id');
            $table->index('status');
        });

        Schema::table('popupusermapping', function($table){
            $table->index('popup_id');
            $table->index('user_id');
        });

        Schema::table('bc_user_metrics', function($table) {
            $table->index('user_id');
            $table->index('metric_id');
            $table->index('milestone_id');
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
