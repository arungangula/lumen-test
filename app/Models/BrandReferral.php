<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BrandReferral extends Model
{
    protected $table = 'brand_referral_codes';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public function brand() {
        return $this->belongsTo('App\Models\Service', 'brand_id');
    }

    public function createdUser() {
        return $this->belongsTo('App\Models\User', 'last_modified_by');
    }

    public function users() {
        return $this->morphedByMany('App\Models\User', 'brand_referral_taggable');
    }

    public function cities() {
        return $this->morphedByMany('App\Models\City', 'brand_referral_taggable');
    }

    public function locations() {
        return $this->morphedByMany('App\Models\Location', 'brand_referral_taggable');
    }

    public function getUniqueImagePath($original_filepath=null) {
        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('user_referral_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }
}
