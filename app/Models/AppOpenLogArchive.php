<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class AppOpenLogArchive extends Model
{	
	protected $table = 'app_open_logs_archive';
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}