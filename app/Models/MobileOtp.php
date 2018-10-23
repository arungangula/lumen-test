<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileOtp extends Model {

  const STATUS_ACTIVE = 'ACTIVE';
  const STATUS_EXPIRED = 'EXPIRED';

  protected $fillable = [
    "id",
    "user_id",
    "phone_number",
    "otp_number",
    "verified",
    "created_at",
    "updated_at",
    "payment_id",
    "status"
    ];


	public function user(){
  	    return $this->belongsTo('App\Models\User','user_id');
    }
}
