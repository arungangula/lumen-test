<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserNotificationLog extends Model
{
	const ELEMENT_TYPE_GCM_PUSH = 'gcm_push';

    protected $table = "user_notification_logs";

    public function users() {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }
}
