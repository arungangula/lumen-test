<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelsSeoDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();
            $table->string('title');
            $table->string('description');
            $table->text('keywords');
            $table->text('long_description');
            $table->string('status')->default('active');
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
        Schema::drop('seo_datas');
    }
}
