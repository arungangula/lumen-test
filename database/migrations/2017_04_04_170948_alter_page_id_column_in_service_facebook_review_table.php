<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPageIdColumnInServiceFacebookReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_facebook_reviews', function (Blueprint $table) {
            $table->string('fb_user_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_facebook_reviews', function (Blueprint $table) {
            //
        });
    }
}
