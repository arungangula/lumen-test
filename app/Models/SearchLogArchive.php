<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class SearchLogArchive extends Model
{	
	protected $table = 'search_logs_archive';
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}