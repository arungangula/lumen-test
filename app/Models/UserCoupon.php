<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $table = 'user_coupons';

    public function user() {
      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function coupon() {
      return $this->belongsTo('App\Models\Coupon', 'coupon_id');
    }
}
