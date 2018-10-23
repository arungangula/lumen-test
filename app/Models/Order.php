<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LineItem;
use Illuminate\Http\Request;

class Order extends Model
{
    const STATUS_DRAFT      = 'DRAFT';
    const STATUS_CREATED    = 'CREATED';

    const STATUS_RECEIVED   = 'RECEIVED';

    const STATUS_PAID       = 'PAID';

    const STATUS_CONFIRMED  = 'CONFIRMED';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_COMPLETED  = 'COMPLETED'; // When Service Delivered

    const STATUS_CANCELLED  = 'CANCELLED';

    const TYPE_CONSUMER     = 'CONSUMER';
    const TYPE_COMMISSION   = 'COMMISSION';

    public static function PUBLIC_STATUS_LIST(){
        return [
            self::STATUS_CREATED,
            self::STATUS_RECEIVED,
            self::STATUS_PAID,
            self::STATUS_CONFIRMED,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED];
    }

    public static function COMMISSION_STATUSES(){
        return [self::STATUS_CREATED, self::STATUS_PAID, self::STATUS_RECEIVED];
    }

    protected $table = 'orders';

    public function user()
    {
      return $this->belongsTo('App\Models\User','user_id');
    }

    public function service()
    {
      return $this->belongsTo('App\Models\Service','service_id');
    }

    public function line_items()
    {
      return $this->hasMany('App\Models\LineItem','order_id');
    }

    public function package_items()
    {
      return $this->hasMany('App\Models\LineItem','order_id')->where('item_type', LineItem::ITEM_TYPE_PACKAGE);
    }

    public function payments() {
      return $this->hasMany('App\Models\Payment','order_id');
    }

    public function paidPayment() {
      return $this->hasOne('App\Models\Payment','order_id')->whereIn('gateway_status', ['CHARGED', 'NUMBER_VERIFIED']);
    }

    public function shipping_address() {
        return $this->belongsTo('App\Models\UserShippingAddress','shipping_address_id');
    }

    public function shipments() {
      return $this->hasMany('App\Models\Shipment','order_id');
    }

    public function logisticsOrders() {
        return $this->hasManyThrough('App\Models\LogisticsOrder', 'App\Models\Payment', 'order_id', 'payment_id');
    }

    public function update_status($status, $changed_by, $user_id = null) {

        // TODO : Log User updating status Old Status, New Status, changed_by [User, System, Job, API], user_id

        $this->order_status = $status;
        $this->save();
    }

    public function getInvoiceNumber() {
        if(!$this->invoice_number) {
            $prefix = config('app.env') == 'production' ? "P-" : "DEV-";
            return $prefix.str_pad($this->id, 8, 0, STR_PAD_LEFT);
        }
        return $this->invoice_number;
    }
}
