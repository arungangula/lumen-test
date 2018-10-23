<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LifestageIdInFeedpost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_content', function(Blueprint $table){
            $table->string('lifestage_ids')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_content', function(Blueprint $table){
            $table->dropColumn('lifestage_ids');
        });
    }
}
