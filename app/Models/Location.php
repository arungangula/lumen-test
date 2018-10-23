<?php
namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;
use App\Services\Google\Api\Places;

class Location extends SleepingOwlModel {

    protected $table = 'location_master';

    protected $hidden = [ 'created_by','updated_by','parent_id','state_id','country_id' ];

    private static $currentLocation;

    public static function setCurrentLocation($location){
        self::$currentLocation = $location;
    }

    public static function getCurrentLocation(){

        return self::$currentLocation;
    }

    public function city(){

         return $this->belongsTo('App\Models\City','city_id');
    }

    public function area(){

         return $this->belongsTo('App\Models\Area','area_id');
    }

    public function zone() {
        return $this->belongsTo('App\Models\Zone', 'zone_id');
    }

    public static function getLocationFromUrl($url_slug, $city_id){

        //return Location::where('location_for_url', $url_slug)->first();

        $location = Location::with('area')->where('location_for_url', $url_slug)
            ->where('city_id', $city_id)->first();

        if(isset($location))
        {
            if($location->location_route_id != 0 || $location->status == 1)
            {
                $nearbyLocationsArray = explode(',', $location->nearby_locations);
                $nearbyLocations = Location::whereIn('id', $nearbyLocationsArray)->get();

                foreach ($nearbyLocations as $nearbyLocation) {

                    $location->{$nearbyLocation->id} = $nearbyLocation->location_for_url;
                }
                return $location;
            }
        }
        else
        {
            return $location;
        }
    }

    public static function getLocationArrayForSearch($city_id){

        $locations = Cache::get('locations_filter_'.$city_id);
        if(!$locations){
              $locations = Location::where('city_id', $city_id)->where('status', 1)->select('location_name','location_for_url','location_latitude','location_longitude')->orderBy('location_name', 'asc')->get();
              Cache::put('locations_filter_'.$city_id, $locations,1000);
        }

        return $locations;
    }

    public static function fromCache($location_id) {
        $location = Cache::remember('location_'.$location_id, 1440, function() use ($location_id){
            return Location::where('id', $location_id)->where('status', 1)->first();
        });
        return $location;
    }

    public function brandReferrals(){
      return $this->morphToMany('App\Models\BrandReferral', 'brand_referral_taggable');
    }

    public static function locationFromPlace(Places $place){

        $location = self::where('location_name', $place->location)->first();

        if(!$location){
            if($place->location || $place->city || $place->area){

                if(!$place->location && !$place->city){
                    $location_name = $place->area;
                }
                elseif(!$place->location){
                    $location_name = $place->city;
                }
                else{
                    $location_name = $place->location;
                }
                
                $city = City::cityFromPlace($place);
                if($city){
                    $location = Location::where('location_name', $location_name)->where('city_id', $city->id)->where('area_id', $city->parent_id)->first();
                
                    if(!$location){
                        
                            $location = new Location;
                            $location->city_id = $city->id;
                            $location->area_id = $city->parent_id;
                            $location->status = 1;
                            $location->location_name = $location_name;
                            $location->location_for_url = str_slug($location_name);
                            $location->location_latitude = $place->latitude;
                            $location->location_longitude = $place->longitude;
                            $location->pincode = ($place->pincode) ? $place->pincode : 0;
                            $location->place_id = $place->place_id;
                            $location->save();
                        
                    }
                }
            }
        }

        return $location;
    }
}
