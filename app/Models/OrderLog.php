<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{

    // Constant User IDs
    const USER_SYSTEM = -1;

    // Log Types
    const COUPON_APPLIED            = "COUPON_APPLIED";
    const COUPON_REMOVED            = "COUPON_REMOVED";
    const COUPON_REDEEMED           = "COUPON_REDEEMED";
    const ORDER_STATUS_UPDATED      = "ORDER_STATUS_UPDATED";
    const PAYMENT_CREATED           = "PAYMENT_CREATED";
    const PAYMENT_GATEWAY_STATUS_CHANGED    = "PAYMENT_GATEWAY_STATUS_CHANGED";
    const PAYMENT_GATEWAY_REFUND_STATUS_CHANGED    = "PAYMENT_GATEWAY_REFUND_STATUS_CHANGED";
    const PAYMENT_STATUS_UPDATED    = "PAYMENT_STATUS_UPDATED";
    const NOTIFICATION_SENT         = "NOTIFICATION_SENT";
    const NOTIFICATION_SKIPPED      = "NOTIFICATION_SKIPPED";
    const COMISSION_CREATED         = "COMISSION_CREATED";
    const ALERT_TRIGGERED           = "ALERT_TRIGGERED";
    const PAYMENT_GATEWAY_RESPONSE  = "PAYMENT_GATEWAY_RESPONSE";
    const CASH_ON_DELIVERY = "CASH_ON_DELIVERY";
    const CART_MODIFIED             = "CART_MODIFIED";
    const PAYMENT_INFO              = "PAYMENT_INFO";
    const REFUND_INITIATED          = "REFUND_INITIATED";
    const SHIPMENT_CREATED          = "SHIPMENT_CREATED";
    const ORDER_CREATED             = "ORDER_CREATED";
    const LOGISTICS                 = "LOGISTICS";
    const LOGISTICS_STATUS_UPDATED  = "LOGISTICS_STATUS_UPDATED";
    const LOGISTICS_WAREHOUSE_DETAILS = "logistics_warehouse_details";
    const PICKUP_REQUEST_INITIATED  = "pickup_request_initiated";

    protected $table = 'order_logs';

    public function order() {
      return $this->belongsTo('App\Models\Order','order_id');
    }

    public static function record($order_id, $user_id, $log_type, $log_info = '', $log_meta = null) {
        $log = new self;
        $log->order_id  = $order_id;
        $log->user_id   = getDefault($user_id, 0);
        $log->log_type  = $log_type;
        $log->log_info  = $log_info;
        $log->log_meta  = json_encode($log_meta);
        $log->save();
    }
}
