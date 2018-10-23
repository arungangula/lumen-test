<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id');
            $table->integer('parent_category_id');
            $table->integer('subcategory_id');
            $table->integer('service_id');
            $table->float('rating');
            $table->float('price');
            $table->timestamps();
            $table->index('package_id');
            $table->index('parent_category_id');
            $table->index('subcategory_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('category_packages');
    }
}
