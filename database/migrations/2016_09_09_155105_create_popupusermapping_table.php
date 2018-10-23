<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopupusermappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popupusermapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('popup_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('view_count')->unsigned();
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
        Schema::drop('popupusermapping');
    }
}
