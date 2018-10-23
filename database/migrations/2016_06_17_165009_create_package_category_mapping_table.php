<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageCategoryMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_category_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id');
            $table->integer('category_id');
            $table->timestamps();
            $table->index('package_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('package_category_mapping');
    }
}
