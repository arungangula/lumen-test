<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class SponsoredItem extends SleepingOwlModel {

    protected $table = 'bc_sponsored_items';

    public function lifestages(){

        return $this->belongsToMany('App\Models\Lifestage', 'bc_sitem_prop_mapping', 'sitem_id', 'lifestage_id');
    }

    public function cities() {
    
        return $this->belongsToMany('App\Models\City', 'bc_sitem_prop_mapping', 'sitem_id', 'city_id');        
    }

    public function locations(){

        return $this->belongsToMany('App\Models\Location', 'bc_sitem_prop_mapping', 'sitem_id', 'location_id');

    }

    public function areas(){
    
        return $this->belongsToMany('App\Models\Area', 'bc_sitem_prop_mapping', 'sitem_id', 'area_id');

    }
    public function subcategories(){
        return $this->belongsToMany('App\Models\ServiceCategory', 'bc_sitem_prop_mapping', 'sitem_id', 'subcategory_id');
    }

    public function articles(){

    	// dd($this->hasOne('App\Models\Article', 'article_id')->get()->toArray());
        return $this->hasOne('App\Models\Article', 'id', 'aditem_id');
    }

    public function serviceproviders(){

        return $this->hasOne('App\Models\Service', 'id','aditem_id');
    }

    public function getUniqueImagePath($original_filepath=null){
        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('ads');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }
}