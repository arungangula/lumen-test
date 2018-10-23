<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CartRemoveReasonLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_remove_log', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('line_item_id');
            $table->integer('package_id');
            $table->integer('order_id');
            $table->string('remove_reason');
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
        Schema::drop('cart_remove_log');
    }
}
