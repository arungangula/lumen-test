<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class SearchLog extends Model
{
	const SOURCE_APP = 'app';
	const SOURCE_IOS = 'ios';
	const SOURCE_WEB = 'web';
	
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}