<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use Illuminate\Http\Request;

class Shipment extends Model
{

  protected $table = 'shipments';

  protected $fillable = [
    "order_id",
    "service_id",
    "shipping_address_id",
    "net_value",
    "shipping_charge",
    "payment_mode",
    "shipment_type",
    "tracking_provider",
    "tracking_ref",
    "notes",
    "status"
  ];

    // Payment Modes
  const PM_PREPAID  = "PREPAID";
  const PM_COD      = "COD";

    // Shipment Types
  const ST_3PL = "THIRD_PARTY_LOGISTICS";
  const ST_SP  = "SERVICE_PROVIDER";

    // Tracking Providers
  const TP_DELHIVERY  = "DELHIVERY";
  const TP_BLUEDART   = "BLUEDART";
  const TP_FEDEX      = "FEDEX";
  const TP_SP         = "SERVICE_PROVIDER";

    // status
  const STATUS_CREATED    = "CREATED";
  const STATUS_PICKED_UP  = "PICKED_UP";
  const STATUS_TRANSIT    = "TRANSIT";
  const STATUS_OUT_FOR_DELIVERY = "OUT_FOR_DELIVERY";
  const STATUS_DELIVERED  = "DELIVERED";

  const STATUS_RETURNED   = "RETURNED";
  const STATUS_CANCELLED  = "CANCELLED";

  public static function PUBLIC_STATUS_LIST() {
    return [
        self::STATUS_CREATED,
        self::STATUS_PICKED_UP,
        self::STATUS_TRANSIT,
        self::STATUS_OUT_FOR_DELIVERY,
        self::STATUS_DELIVERED,
        self::STATUS_RETURNED,
        self::STATUS_CANCELLED,
    ];
  }

  public static function PROCESSING_STATUS_LIST() {
    return [
        self::STATUS_CREATED,
        self::STATUS_PICKED_UP,
        self::STATUS_TRANSIT,
        self::STATUS_OUT_FOR_DELIVERY
    ];
  }

  public static function SHIPMENT_TYPES() {
    return [
        self::ST_3PL,
        self::ST_SP
    ];
  }

  public static function TRACKING_PROVIDERS() {
    return [
        self::TP_DELHIVERY,
        self::TP_BLUEDART,
        self::TP_FEDEX,
        self::TP_SP
    ];
  }

  public function getNextStatus() {
    $flow = [
        self::STATUS_CREATED,
        self::STATUS_PICKED_UP,
        self::STATUS_TRANSIT,
        self::STATUS_OUT_FOR_DELIVERY,
        self::STATUS_DELIVERED,
        self::STATUS_RETURNED
      ];
    $current = array_search($this->status, $flow);
    if($current >= count($flow)) {
      return false;
    }

    return $flow[$current + 1];
  }

  public function service() {
    return $this->belongsTo('App\Models\Service','service_id');
  }

  public function order() {
    return $this->belongsTo('App\Models\Order','order_id');
  }

  public function shipping_address() {
    return $this->hasOne('App\Models\UserShippingAddress','shipping_address_id');
  }
}
