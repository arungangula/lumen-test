<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class UserMetric extends Model
{
	protected $table = 'bc_user_metrics';

	public function user() {
      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function milestone() {
    	return $this->belongsTo('App\Models\Milestone', 'milestone_id');
    }
}