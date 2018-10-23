<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class Device extends Model {

	const ANDROID_SERVICE = "android_service";
	const ANDROID_CONSUMER = "android_consumer";
	const IOS_CONSUMER = "ios_consumer";
	const MOBILE_WEB = "mobile_web";
	const DESKTOP_WEB = "desktop_web";
	
    protected $table = 'registered_devices';

    public function user() {

    	return $this->belongsTo('App\Models\User', 'user_id');
    }

}