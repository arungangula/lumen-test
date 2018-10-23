<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Tip extends Model
{
	protected $table = 'bc_tips';

	public function milestones() {
		return $this->belongsTo('App\Models\Milestone','milestone_id');
	}

	public function metrics() {
		return $this->belongsTo('App\Models\Metric', 'metric_id');
	}
}