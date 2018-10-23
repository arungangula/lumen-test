<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserShippingAddress extends Model
{
    protected $table = 'user_shipping_address';

    public function user() {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
