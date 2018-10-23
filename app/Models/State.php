<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Google\Api\Places;

class State extends Model
{
    protected $table = 'state_master';

    public function country(){

        return $this->belongsTo('App\Models\Country','country_id');
    }

    public static function stateFromPlace(Places $place){

    	$state = self::where('state_name', $place->state)->first();

    	if(!$state){
    		if($place->state){
    			$country = Country::countryFromPlace($place);

    			if($country){
	    			$state = new self;
	    			$state->country_id = ($country) ? $country->id : 0;
	    			$state->state_name = $place->state;
	    			$state->save();
	    		}
    		}	
    	}


    	return $state;
    }
}
