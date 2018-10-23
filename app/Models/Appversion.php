<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appversion extends Model
{
    protected $table = 'bc_mobile_app_api_signature_mapping';

    const ANDROID_CONSUMER  = "android_consumer";
    const ANDROID_SERVICE   = "android_service";
    const ANDROID_OPERATION = "android_operation";

    const IOS_CONSUMER  = "ios_consumer";
    const IOS_SERVICE   = "ios_service";
    const IOS_OPERATION = "ios_operation";

	const WINDOW_CONSUMER  = "window_consumer";
    const WINDOW_SERVICE   = "window_service";
    const WINDOW_OPERATION = "window_operation";    

    public $timestamps = false;
}
