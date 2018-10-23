<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackageIdInReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sp_reviews', function (Blueprint $table)
        {
            $table->integer('package_id')->after('provider_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sp_reviews', function (Blueprint $table)
        {
            $table->dropColumn('package_id');
        });
    }
}
