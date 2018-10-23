<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HashTagLog extends Model
{
	protected $table = 'hashtag_logs';

	public function hashtag()
	{
		return $this->belongsTo('App\Models\HashTag', 'hashtag_id');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id');
	}
}
