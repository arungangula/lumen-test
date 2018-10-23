<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorAPI;

use Redis;

class Payment extends Model {

    protected $table = 'payments_new';

    const PRICE_MIN = 4999;
    const PRICE_BETWEEN_LOWER = 5000;
    const PRICE_BETWEEN_UPPER = 9999;
    const PRICE_MAX = 10000;

    const PRICE_MIN_PERCENTAGE = 0.10;
    const PRICE_BETWEEN_PERCENTAGE = 0.10;
    const PRICE_MAX_PERCENTAGE = 0.15;

    const SERVICE_TAX = 0.14;
    const SWACHCH_BHARAT_CESS = 0.005;
    const KRISHI_KALYAN_CESS  = 0.005;

    const CGST = 0.09;
    const SGST = 0.09;
    const IGST = 0.18;

    const CONSUMER_TAX = 0.15;
    const CONSUMER_TAX_TYPE = 'ST';

    const CONSUMER_INVOICE  = 'Transaction-Advice';

    const SERVICE_INVOICE   = 'Invoice';

    const APP_PAYMENT_REGEX = ".*order_id=([0-9_a-zA-Z].*)&status=CHARGEDdummytexttoavoidregexmatch.*";

    const STATUS_INITIATED = "INITIATED";
    const STATUS_PROCESSING = "PROCESSING";
    const STATUS_VERIFIED = "VERIFIED";
    const STATUS_CHARGED_PARTIAL = 'CHARGED_PARTIAL';
    const STATUS_CHARGED = 'CHARGED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_UNKNOWN = "UNKOWN";

    const STATUS_REFUND_PROCESSING = "REFUND_PROCESSING";
    const STATUS_REFUND_SUCCESS    = "REFUND_SUCCESS";
    const STATUS_REFUND_FAILED     = "REFUND_FAILED";

    const STATUS_NEW = "NEW";
    const CHARGED = 'CHARGED';
    const FAILED = 'FAILED';

    const REDIS_LOCK_KEY = "payment_sync_lock_";

    public static function PUBLIC_STATUS_LIST(){
        return [
            self::STATUS_NEW,
            self::STATUS_CHARGED,
            self::STATUS_FAILED,
            self::STATUS_INITIATED,
            self::STATUS_PROCESSING,
            self::STATUS_UNKNOWN,
        ];
    }

    public function serviceprovider() {//Consider this for Camel Casing

      return $this->belongsTo('App\Models\Service','service_id');

    }

    public function user(){

        return $this->belongsTo('App\Models\User','user_id');

    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order','order_id');
    }

    public function package()
    {
        return $this->belongsTo('App\Models\Package','package_id');
    }

    public function getUniquePayId() {
        $prefix = config('app.env') == 'production' ? "P-" : "DEV-";
        return $prefix.$this->order_id."-".$this->id;
    }

    private function getLockKey() {
        return self::REDIS_LOCK_KEY."".$this->id;
    }

    public function lockSync() {
        Redis::command("SET", [ $this->getLockKey(), time()]);
        $expire_time = time() + (6*60);
        Redis::command("EXPIREAT", [ $this->getLockKey(), $expire_time]);
    }

    public function isLocked() {
        return !(Redis::command("GET", [ $this->getLockKey()]) == null);
    }

    public function unlockSync() {
        Redis::command("DEL", [ $this->getLockKey()]);
    }
}
