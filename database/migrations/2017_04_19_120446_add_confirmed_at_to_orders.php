<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Order;

class AddConfirmedAtToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->timestamp('confirmed_at');
        });
        $orders = Order::where('order_status', Order::STATUS_CONFIRMED)->with('paidPayment')->get();
        foreach ($orders as $order) {
            if($order->paidPayment) {
                $order->confirmed_at = $order->paidPayment->created_at;
                $order->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->dropColumn('confirmed_at');
        });
    }
}
