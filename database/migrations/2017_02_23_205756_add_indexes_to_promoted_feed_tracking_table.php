<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToPromotedFeedTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promoted_feed_tracking', function (Blueprint $table)
        {
            $table->index('user_id');
            $table->index('promotion_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promoted_feed_tracking', function (Blueprint $table) {
            //
        });
    }
}
