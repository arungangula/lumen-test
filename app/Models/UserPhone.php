<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model {

	protected $table = "bc_user_phone";

	public function user() {
	    return $this->belongsTo('App\Models\User','user_id');
	}
}