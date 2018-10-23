<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPersonaliedCardInAnnouncement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function($table) {
            $table->integer('personalize_card')->default(0);
            $table->string('personalize_card_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function($table) {
            $table->dropColumn('personalize_card');
            $table->dropColumn('personalize_card_mode');
        });
    }
}
