<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Services\Google\Api\Places;

class City extends Model {

    private static $currentCity;
    private static $currentArea;

    public static function setCurrentCity($city){

        self::$currentCity = $city;

    }

    public static function setCurrentArea($area){

        self::$currentArea = $area;

    }

    public static function getCurrentCity(){

        return self::$currentCity;

    }

    public static function getCurrentArea(){

        return self::$currentArea;

    }

    protected $table = 'city_master';

    protected $hidden = [ 'created_by','updated_by','state_id','country_id' ];

    public static function getCityFromSlug($city_slug) {

        return Cache::remember("city-slug-{$city_slug}", 1440, function() use ($city_slug){
            return City::where('city_slug','=',$city_slug)->where('status','=',1)->first();
        });
    }

    public static function getCityFromName($city_name) {

        return Cache::remember("city-name-{$city_name}", 1440, function() use ($city_name){
            return City::where('city_name','=',$city_name)->where('status','=',1)->first();
        });
    }

    public function area(){

        return $this->belongsTo('App\Models\City','parent_id');
    }

    public function state(){

        return $this->belongsTo('App\Models\State','state_id');
    }

    public function locations(){

        return $this->hasMany('App\Models\Location','city_id')->where('status',1);

    }

    public function zones(){
        return $this->hasMany('App\Models\Zone','city_id');
    }

    public function subcategories(){
        return $this->belongsToMany('App\Models\ServiceCategory', 'city_category_mapping', 'city_id', 'category_id')->where('parent_id','!=',0)->having('status', '=', 1);
    }

    public function parentCity()
    {
        return $this->belongsTo('App\Models\City','parent_id');
    }

    public static function getCurrentCitiesArrayForSearch($area_id){

        $cities = Cache::remember('cities_of_area_'.$area_id, 1440, function() use ($area_id){
            return City::where('parent_id', $area_id)->where('status', 1)->orderby('city_name', 'asc')->get();
        });

        return $cities;
    }

    public static function fromCache($city_id){
        $city = Cache::remember('city_'.$city_id, 1440, function() use ($city_id){
            return City::where('id', $city_id)->where('status', 1)->first();
        });

        return $city;
    }

    public static function cityIds() {

        // $cityIds = Cache::remember("child_city_ids", 2440, function() {

        //     return City::where('parent_id', '!=', 0)->where('status', 1)->whereIn('id', config('admin.cities_we_serve'))->lists('id')->all();
        // });
        // return $cityIds;

        return self::citiesWeServe()->lists('id')->all();
    }

    public function brandReferrals(){
      return $this->morphToMany('App\Models\BrandReferral', 'brand_referral_taggable');
    }

    public static function cityFromPlace(Places $place){
        
        if(!$place->city){
            $city_name = $place->area;
        }
        else{
            $city_name = $place->city;   
        }
                
        $city = City::where('city_name', $city_name)->where('parent_id', '!=', 0)->first();

        if(!$city){
            $area = Area::areaFromPlace($place);
            if($area){
                $city = new self;
                $city->city_name = $city_name;
                $city->city_slug = str_slug($city_name);
                $city->status = 1;
                $city->parent_id = $area->id;
                $city->save();
            }
        }

        return $city;
    } 

    public static function citiesWeServe(){
        return Cache::remember('all_cities', 1440, function(){
            return self::where('parent_id', '!=', 0)->where('status', 1)->whereIn('id', config('admin.cities_we_serve'))->get();
        });
    }  

    /*public static function getCityLocationsForSearch($city_id){

        $locations = Cache::remember('location_of_city_'.$city_id, 1440, function() use ($city_id){
            return Loca::where('parent_id', $currentArea['id'])->where('status', 1)->get()->toArray();
        });

        return $locations;
    }

    public static function getCurrentCityWithLocations(){
        $currentCity = self::getCurrentCity();

        $cities = Cache::remember('city_'.$currentCity['id'].'_with_location', 1440, function() use ($currentCity){
            dd(City::where('parent_id', $currentCity['id'])->with('locations')->get()->toArray());
            return City::where('parent_id', $currentCity['id'])->with('locations');
        });
        dd($cities);
        return $city;
    }*/
}
