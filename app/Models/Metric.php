<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class Metric extends Model
{

    const CATEGORY_METRIC = 'metric';
    const CATEGORY_GENERIC = 'generic';

    const MONTH_LABEL = "Month";
    const DAY_LABEL = "Day";

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const MAPPING_CITY = 'city';
    const MAPPING_LOCATION = 'location';

    const METRIC_HEIGHT_UNIT = 'Cm';
    const METRIC_WEIGHT_UNIT = 'Kg';

    const METRIC_USAGE_CARD_ONLY = 'card_only';
    const METRIC_USAGE_NOTIFICATION_ONLY = 'notification_only';
    const METRIC_USAGE_NOTIFICATION_AND_CARD = 'notification_and_card';

	protected $table = 'bc_metrics';

	public function users() {
         return $this->belongsToMany('App\Models\User', 'bc_user_mertics', 'metric_id', 'user_id');
    }

    public function milestones() {
    	return $this->hasMany('App\Models\Milestone', 'metric_id', 'id');
    }

    public function tips() {
        return $this->hasMany('App\Models\Tip', 'metric_id', 'id');
    }

    public function parent() {
        return $this->belongsTo('App\Models\Metric', 'parent', 'id');
    }

    public function children() {
        return $this->hasMany('App\Models\Metric', 'parent', 'id');
    }

    public function locations() {
        return $this->belongsToMany('App\Models\Location', 'metric_mappings', 'metric_id', 'entity_id')->where('entity_type', self::MAPPING_LOCATION);
    }

    public function cities() {
        return $this->belongsToMany('App\Models\City', 'metric_mappings', 'metric_id', 'entity_id')->where('entity_type', self::MAPPING_CITY);
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'metric');
    }

    public function genders() {
        return $this->belongsToMany('App\Models\Gender', 'gender_entity_mappings', 'entity_id', 'gender_id')->where('entity_type', 'metric');
    }

    public function getUniqueImagePath($original_filepath=null) {
        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('metric_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }
}