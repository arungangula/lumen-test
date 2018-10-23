<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('service_id');
            $table->integer('shipping_address_id');

            $table->double('net_value');
            $table->double('shipping_charge');

            $table->string('payment_mode', 10); // Pre paid. COD

            $table->string('shipment_type', 30); //

            $table->string('tracking_provider', 30);
            $table->string('tracking_ref', 50);

            $table->string('notes');

            $table->string('status', 30);

            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['service_id']);
        });

        Schema::create('shipment_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('shipment_id');
            $table->integer('user_id');
            $table->string('log_type');
            $table->string('log_info');
            $table->string('log_meta');

            $table->timestamps();

            $table->index(['shipment_id']);
            $table->index(['user_id']);
            $table->index(['shipment_id', 'log_type']);
            $table->index(['shipment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shipments');
        Schema::drop('shipment_logs');
    }
}
