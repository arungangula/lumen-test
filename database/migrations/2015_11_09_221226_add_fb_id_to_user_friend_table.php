<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFbIdToUserFriendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_user_friends', function (Blueprint $table) {
            $table->bigInteger('user_social_id');
            $table->bigInteger('friend_social_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_user_friends', function (Blueprint $table) {
            //
        });
    }
}
