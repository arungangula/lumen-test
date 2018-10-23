<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Milestone extends Model
{
	protected $table = 'bc_milestones';

	public function metrics() {
		return $this->belongsTo('App\Models\Metric', 'metric_id');
	}

	public function tips() {
		return $this->hasMany('App\Models\Tip', 'milestone_id', 'id');
	}
}