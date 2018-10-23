<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMobileUrlAndTimestampColoumnsToBcUsersSignupuserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_users_signupuser', function (Blueprint $table) {
            $table->string('subscribed_for')->default('');
            $table->string('mob_no')->default('');
            $table->string('source_url')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
