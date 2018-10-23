<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Sitemap extends Model
{
	protected $table = 'sitemaps';

	public function createdUser() {
		return $this->belongsTo('App\Models\User', 'last_modified_by');
	}

}