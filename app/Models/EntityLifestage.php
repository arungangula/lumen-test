<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityLifestage extends Model
{
    const ARTICLE  = "article";
    const PACKAGE = "package";
    const SERVICE = "service";
    const QUESTION = "question";
    const FEEDPOST = "feedpost";

    public static function saveToDb($params) {

    	$entityLifestage = self::where('entity_type', $params['entity_type'])
    		->where('entity_id', $params['entity_id'])
    		->where('start_day', $params['start_day'])
    		->where('end_day', $params['end_day'])
    		->first();

    	if($entityLifestage) return $entityLifestage->id;

    	$entityLifestage = new self;
    	$entityLifestage->entity_type      = $params['entity_type'];
    	$entityLifestage->entity_id        = $params['entity_id'];
    	$entityLifestage->start_day        = $params['start_day'];
        $entityLifestage->end_day          = $params['end_day'];
    	$entityLifestage->lifestage_period = $params['lifestage_period'];
    	$entityLifestage->save();
    	return $entityLifestage->id;
    }


    public function packages(){

        return $this->belongsToMany('App\Models\Package', 'entity_id');

    }
}
