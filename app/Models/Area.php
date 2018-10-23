<?php
namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;
use App\Services\Google\Api\Places;

class Area extends SleepingOwlModel {

    protected $table = 'city_master';

    protected $hidden = [ 'created_by','updated_by' ];

    public static function getAreaFromSlug($city_slug) {

        $city = City::where('city_slug','=',$city_slug)->where('status','=',1)->first();
        
        return $city;
    }

    public function cities() {

        return $this->hasMany('App\Models\City','parent_id')->where('status',1);

    }

    public function locations(){

        return $this->hasMany('App\Models\Location','area_id')->where('status',1);
    }

    public function subcategories(){
        return $this->belongsToMany('App\Models\ServiceCategory', 'city_category_mapping', 'city_id', 'category_id');
    }

    public static function getAllAreas(){
        $areas = Cache::remember('areas_all', 1440, function(){
                        return Area::where('parent_id', 0)->where('status', 1)->get();
                    });        
        return $areas;
    }

    public static function areaFromPlace(Places $place){
        $area = Area::where('city_name', $place->area)->where('parent_id', 0)->first();

        if(!$area){
            if($place->area){
                $state = State::stateFromPlace($place);

                if($state){
                    $area = new Area;
                    $area->state_id = $state->id;
                    $area->country_id = $state->country_id;
                    $area->city_name = $place->area;
                    $area->city_slug = str_slug($place->area);
                    $area->status = 1;
                    $area->parent_id = 0;
                    $area->save();
                }

            }

        }

        return $area;
    }

}