<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;


class Gcmpush extends Model {

    protected $table = 'gcm_push';

    public function lifestages(){

        return $this->belongsToMany('App\Models\Lifestage','gcm_push_mapping','gcm_push_id','lifestage_id');
    }

    public function cities() {
    
        return $this->belongsToMany('App\Models\City', 'gcm_push_mapping', 'gcm_push_id', 'area_id');        
    }

    public function locations(){

        return $this->belongsToMany('App\Models\Location', 'gcm_push_mapping', 'gcm_push_id', 'location_id');
    }

    public function interestTags(){

        return $this->belongsToMany('App\Models\InterestTag', 'gcm_push_mapping', 'gcm_push_id', 'interest_tag_id');
    }

    public function sponsoredItem() {

    	return $this->hasOne('App\Models\SponsoredItem', 'id', 'sitem_id');
    }

    public function lifestagePeriod() {
        
        return DB::table('gcm_push_mapping')->where('gcm_push_id', $this->id)->where('lifestage_period', '!=', '')->first();
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'gcmpush');
    }

    public function getTitleAttribute($value){

    	if($this->sitem_id === null)
    	{
    		return $value;
    	}
    	else
    	{
    		//dd($this->sitem_id, $this->sponsoredItem);
            if($this->sponsoredItem == null)
            {
                return '';
            }
            else
            {
                return $this->sponsoredItem->ad_title;
            }
    	}
    }

    public function getContentAttribute($value){

    	if($this->sitem_id === null)
    	{
    		return $value;
    	}
    	else
    	{
    		//dd($this->sponsoredItem);
            if($this->sponsoredItem == null)
            {
                return '';
            }
            else
            {
                return $this->sponsoredItem->other_details;
            }
    	}
    }

    public function getUniqueImagePath($original_filepath=null){

        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('push');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }

    public function lastModifiedBy() {
        return $this->belongsTo('App\Models\User', 'last_modified_by', 'id');
    }
}



