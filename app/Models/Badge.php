<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Badge extends Model
{
	const BADGE_CACHE_TAG = 'badges_cache_tag';
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	const CRITERIA_HE = 'he';
	const CRITERIA_LE = 'le';
	const CRITERIA_MISC = 'misc';
}