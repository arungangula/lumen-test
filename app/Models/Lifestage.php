<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lifestage extends Model {

    protected $table = 'bc_lifestages';

    const NEW_PARENT = 4;
    const TODDLER = 5;
    const EXPECTING = 6;
    const ALL = 8;

    protected $hidden = [ ];

    public $timestamps = false;

    private static $currentLifestage;

    public static function setCurrentLifestage($lifestage){

        self::$currentLifestage = $lifestage; 
    }

    public static function getCurrentLifestage(){

        return self::$currentLifestage;

    }

    public static function getLifestageFromSlug($seo_url) {

        $lifestage = Lifestage::where('seo_url', '=', $seo_url)->first();
        return $lifestage;

    }

    public function collections(){

        return $this->belongsToMany('App\Models\Collection','bc_collection_lifestage_mapping','lifestage_id','collection_id');
    }

    public function interestTags(){
        
        return $this->belongsToMany('App\Models\InterestTag','interest_tags_lifestage_mapping','lifestage_id','tag_id');
    }

    public function serviceCategories(){

        return $this->belongsToMany('App\Models\ServiceCategory', 'subcategory_lifestage_mapping', 'lifestage_id', 'subcategory_id');
    }

    public function followUsers() {
        return $this->hasMany('App\Models\FollowUser', 'lifestage_id', 'id');
    }

}