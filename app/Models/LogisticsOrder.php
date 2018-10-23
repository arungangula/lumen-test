<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogisticsOrder extends Model
{
	const STATUS_ACTIVE    = 'active';
	const STATUS_INACTIVE = 'inactive';

	const STATUS_CREATED = 'Manifested';
	const STATUS_PICK_UP_REQUEST = 'Pickup Requested';
    const STATUS_IN_TRANSIT = 'In Transit';
    const STATUS_PENDING = 'Pending';
    const STATUS_DISPATCHED = 'Dispatched';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_COLLECTED = 'Collected';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_SCHEDULED = 'Scheduled';

    const ORDER_TYPE_REVERSE_PICKUP = 'order_type_reverse_pickup';
    
    protected $table = 'logistics_orders';

    public static function PUBLIC_STATUS_LIST() {
	    return [
	        self::STATUS_CREATED,
	       	self::STATUS_PICK_UP_REQUEST,
	        self::STATUS_IN_TRANSIT,
	        self::STATUS_PENDING,
	        self::STATUS_DISPATCHED,
	        self::STATUS_DELIVERED,
	        self::STATUS_COLLECTED,
	        self::STATUS_CANCELLED,
	        self::STATUS_SCHEDULED	    
	    ];
	}

	public function getStatusValue($status = null) {
		$statusValues = [
			self::STATUS_CREATED 	=> 1,
	       	self::STATUS_PICK_UP_REQUEST 	=> 2,
	        self::STATUS_IN_TRANSIT => 3,
	        self::STATUS_PENDING 	=> 4,
	        self::STATUS_SCHEDULED 	=> 5,
	        self::STATUS_DISPATCHED => 6,
	        self::STATUS_DELIVERED 	=> 7,
	        self::STATUS_COLLECTED 	=> 7,
	        self::STATUS_CANCELLED 	=> 7,
		];

		if(!$status) {
			$status = $this->status;
		}

		return getDefault($statusValues[$status], 0);
	}

    public function payment() {
    	return $this->belongsTo('App\Models\Payment', 'payment_id');
    }

    public function service() {
    	return $this->belongsTo('App\Models\Service', 'service_id');
    }
}
