<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Google\Api\Places;

class Country extends Model
{
    protected $table = 'country_master';

    public static function countryFromPlace(Places $place){

    	$country = self::where('country_name', $place->country)->first();

    	if(!$country){
    		if($place->country){
    			$country = new Country;
    			$country->country_name = $place->country;
    			$country->save();
    		}
    	}


    	return $country;
    }
}
