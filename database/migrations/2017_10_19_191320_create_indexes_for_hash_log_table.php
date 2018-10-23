<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexesForHashLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hashtag_logs', function($table) {
            $table->index('hashtag_id');
            $table->index('hashtag_name');
            $table->index('hashtag_event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hashtag_logs', function($table) {
            $table->dropIndex('hashtag_id');
            $table->dropIndex('hashtag_name');
            $table->dropIndex('hashtag_event');
        });
    }
}
