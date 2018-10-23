<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CategoryDynamicFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_category_field_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('title');
            $table->string('type');
            $table->string('params');
            $table->timestamps();
        });

        Schema::create('sub_category_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sub_category_id')->default(0);
            $table->integer('field_id')->default(0);
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
        Schema::drop('category_field_types');
        Schema::drop('category_fields');
    }
}
