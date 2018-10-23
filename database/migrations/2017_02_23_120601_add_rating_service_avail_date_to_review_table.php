<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingServiceAvailDateToReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sp_reviews', function (Blueprint $table) {
            $table->integer("rating")->unsigned();
            $table->dateTime('service_avail_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sp_reviews', function (Blueprint $table) {
            //
        });
    }
}
