<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Coupon extends Model
{
    const CITY = "city";
    const CATEGORY = "category";
    const SERVICE = "service";
    const PACKAGE_TYPE = "package_type";
    const PRODUCT_PACKAGE = "product_package";
    const SERVICE_PACKAGE = "service_package";
    const USER = "user";

    const COUPON_INVALID        = "Invalid Coupon Code";
    const MAX_LIMIT_EXCEEDED    = "You have exceeded the maximum limit of coupon usage";
    const REGISTERED_USERS      = "Coupon Valid for Only Registered Users";
    const ALREADY_APPLIED       = "Coupon Already Applied";
    const EXPIRED               = "Coupon Expired";
    const FIRST_TIME_USERS      = "Coupon Valid For Only First Time Users";
    const REFERRED_USERS        = "Coupon Valid for Only Referred Users";

    public function city() {
    	return $this->belongsToMany('App\Models\City', 'coupon_mappings', 'coupon_id', 'coupon_map_id')->where('coupon_map_type', self::CITY);
    }

    public function user() {
        return $this->belongsToMany('App\Models\User', 'coupon_mappings', 'coupon_id', 'coupon_map_id')->where('coupon_map_type', self::USER);
    }

    public function category() {
    	return $this->belongsToMany('App\Models\ServiceCategory', 'coupon_mappings', 'coupon_id', 'coupon_map_id')->where('coupon_map_type', self::CATEGORY);
    }

    public function service() {
    	return $this->belongsToMany('App\Models\Service', 'coupon_mappings', 'coupon_id', 'coupon_map_id')->where('coupon_map_type', self::SERVICE);
    }

    public function product() {
    	return $this->belongsToMany('App\Models\Package', 'coupon_mappings', 'coupon_id', 'coupon_map_id')->where('coupon_map_type', self::PRODUCT_PACKAGE);
    }

    public function package() {
    	return $this->belongsToMany('App\Models\Package', 'coupon_mappings', 'coupon_id', 'coupon_map_id')->where('coupon_map_type', self::SERVICE_PACKAGE);
    }

    public function lastModified() {
        return $this->belongsTo('App\Models\User','coupon_created_user_id');
    }

    public function getCouponDetails($coupon_code) {
        return self::where('coupon_code', $coupon_code)->get();
    }

    public function getCouponMappings($coupon_id) {
        return DB::table('coupon_mappings')->where('coupon_id',$coupon_id)->get();
    }
}
