<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogisticsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_orders', function($table) {
            $table->increments('id');
            $table->integer('payment_id');
            $table->string('logistics_id');
            $table->string('logistics_partner');
            $table->string('status');
            $table->string('payment_mode');
            $table->integer('service_id');
            $table->string('logistics_order_id');
            $table->timestamps();

            $table->index('payment_id');
            $table->index('logistics_id');
            $table->index('service_id');
            $table->index('logistics_partner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logistics_orders');
    }
}
