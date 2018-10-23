<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentLog extends Model
{

    // Constant User IDs
    const USER_SYSTEM = -1;

    // Log Types
    const STATUS_UPDATED    = "STATUS_UPDATED";


    protected $table = 'shipment_logs';

    public function shipment() {
      return $this->belongsTo('App\Models\Shipment','order_id');
    }

    public static function record($shipment_id, $user_id, $log_type, $log_info = null, $log_meta = null) {
        $log = new self;
        $log->shipment_id   = $shipment_id;
        $log->user_id       = $user_id;
        $log->log_type      = $log_type;
        $log->log_info      = $log_info;
        $log->log_meta      = json_encode($log_meta);
        $log->save();
    }
}
