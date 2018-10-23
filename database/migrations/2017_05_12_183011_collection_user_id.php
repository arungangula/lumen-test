<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CollectionUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_content_collections', function(Blueprint $table) {
            $table->integer('user_id')->after('id')->default(61518);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_content_collections', function(Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
