<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityColumnInLineItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("line_items", function(Blueprint $table) {
            $table->integer('order_id')->change();
            $table->integer('item_id')->change();
            $table->integer('quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("line_items", function(Blueprint $table) {
            $table->integer('order_id')->unsigned()->change();
            $table->integer('item_id')->unsigned()->change();
            $table->dropColumn('quantity');
        });
    }
}
