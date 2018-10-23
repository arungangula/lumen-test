<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToFeedImpressionsAggregateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feed_impression_aggregates', function($table) {
            $table->index(['entity_type', 'entity_id']);
            $table->index('impressions_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feed_impression_aggregates', function($table) {
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropIndex('impressions_date');
        });
    }
}
