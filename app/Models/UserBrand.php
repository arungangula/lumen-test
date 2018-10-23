<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserBrand extends Model
{
    protected $table = 'user_brands';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function brandReferral() {
        return $this->belongsTo('App\Models\BrandReferral', 'brand_referral_id');
    }

    public function brand() {
        return $this->belongsTo('App\Models\Service', 'brand_id');
    }
}
