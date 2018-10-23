<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class AppOpenLog extends Model
{
	const ACTION_APP_OPEN = 'app_open';
	const ACTION_LOGGED_IN = 'logged_in';
	const ACTION_SIGNED_UP = 'signed_up';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}