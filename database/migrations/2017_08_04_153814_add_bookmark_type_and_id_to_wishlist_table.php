<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBookmarkTypeAndIdToWishlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_services_wishlist', function($table) {
            $table->integer('bookmark_id');
            $table->string('bookmark_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_services_wishlist', function($table) {
            $table->dropColumn('bookmark_id');
            $table->dropColumn('bookmark_type');
        });
    }
}
