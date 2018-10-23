<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserIdColumnsToUserFriendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_user_friends', function (Blueprint $table) {
            
            $table->string('user_social_id', 250)->change();
            $table->string('friend_social_id', 250)->change();
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
