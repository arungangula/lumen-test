<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariantNameValueToPackageItemColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_items', function (Blueprint $table) {
            $table->string('variant_name')->default('size');
            $table->string('variant_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_items', function (Blueprint $table) {
            //
        });
    }
}
