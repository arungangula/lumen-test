<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    const LIFESTAGE = "lifestage";
    const CITY = "city";
    const LOCATION = "location";
    const MIN_APPVERSION = "min_appversion";
    const MAX_APPVERSION = "max_appversion";

    const HOME_BANNER      = "home-banner";
    const SERVICE_BANNER   = "service-banner";
    const COLLECTION_BANNER= "collection-banner";
    const SERVICE_PACKAGE  = "service-package";
    const CATEGORY_PACKAGE = "category-package";
    const MARKETING_POPUP  = "marketing-popup";
    const SHOP_BANNER      = "shop-banner";
    const SHOP_PACKAGE     = "shop-product";
    const PINNED_POST      = "pinned-post";
    const SHOP_CATEGORY    = "shop-category";
    const SHOP_PROMOTED_PACKAGE = "shop-promoted-package";
    const SHOP_EXPLORE_STORE    = "shop-explore-store";
    const SHOP_GRID = "shop-grid";
    const SHOP_GRID_BRAND = "shop-grid-brand";
    const SHOP_GRID_STORE = "shop-grid-store";
    const SHOP_STORY = "shop-story";

    const BOOK_WEB_BANNER   = "book-web-banner";
    const SHOP_WEB_BANNER   = "shop-web-banner";
    const BOOK_TRENDING_SERVICE = "book-trending-service";
    const READ_TRENDING_ARTICLE = "read-trending-article";

    const PINNED_POST_TYPE_METRIC_HEIGHT = "metric-height";
    const PINNED_POST_TYPE_METRIC_WEIGHT = "metric-weight";
    const PINNED_POST_TYPE_METRIC_VACCINATION = "metric-vaccination";
    const PINNED_POST_TYPE_METRIC_GROWTH = "metric-growth";
    const PINNED_POST_TYPE_HASHTAG_CAROUSEL = "hashtag-carousel";
    const PINNED_POST_TYPE_PRODUCT_CAROUSEL = "product-carousel";
    const PINNED_POST_TYPE_ARTICLE_CAROUSEL = "article-carousel";

    const SHOP_TAB = 'shop_tab';
    const JUST_IN = 'just-in';
    const BEST_SELLER = 'best-seller';

    public static $serviceTab = [self::SERVICE_BANNER, self::SERVICE_PACKAGE];
    public static $shopTab    = [self::SHOP_BANNER, self::SHOP_PACKAGE];
    public static $v2shopTab    = [self::SHOP_BANNER, self::SHOP_PACKAGE, self::SHOP_CATEGORY, self::SHOP_PROMOTED_PACKAGE];
    public static $v3shopTab    = [self::SHOP_EXPLORE_STORE, self::SHOP_GRID, self::SHOP_PACKAGE, self::SHOP_STORY, self::SHOP_WEB_BANNER];
    public static $collectionTab    = [self::COLLECTION_BANNER];

    public static $bookWeb = [self::BOOK_WEB_BANNER, self::SERVICE_PACKAGE, self::BOOK_TRENDING_SERVICE];
    public static $shopweb = [self::SHOP_WEB_BANNER, self::SHOP_PACKAGE];

    public function lifestages() {

        return $this->belongsToMany('App\Models\Lifestage', 'promotion_conditions', 'promotion_id', 'condition_id')->where('condition_type', Promotion::LIFESTAGE);
    }

    public function cities() {

        return $this->belongsToMany('App\Models\City', 'promotion_conditions', 'promotion_id', 'condition_id')->where('condition_type', Promotion::CITY);
    }

    public function locations() {

        return $this->belongsToMany('App\Models\Location', 'promotion_conditions', 'promotion_id', 'condition_id')->where('condition_type', Promotion::LOCATION);
    }

    public function minappversion() {

        return $this->belongsToMany('App\Models\Appversion', 'promotion_conditions', 'promotion_id', 'condition_id')->where('condition_type', Promotion::MIN_APPVERSION);
    }

    public function maxappversion() {

        return $this->belongsToMany('App\Models\Appversion', 'promotion_conditions', 'promotion_id', 'condition_id')->where('condition_type', Promotion::MAX_APPVERSION);
    }

    public function getPromotionData() {
        return json_decode($this->metadata, true);
    }

    public function tags() {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'promotion');
    }

    public function genders() {
        return $this->belongsToMany('App\Models\Gender', 'gender_entity_mappings', 'entity_id', 'gender_id')->where('entity_type', 'promotion');
    }

    public function getUniqueImagePath($original_filepath=null){

        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('promotion_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }

    public function setDays(){
        $start_day = [];
        $end_day = [];

        foreach ($this->lifestages as $lifestage) {
            $days = lifestageIdToDays($lifestage->id);

            $start_day[]    = $days['start_day'];
            $end_day[]      = $days['end_day'];      
        }
        
        $start_day = array_filter($start_day);
        $end_day = array_filter($end_day);

        if($start_day && $end_day){
            $start_day = min($start_day);
            $end_day = max(array_filter($end_day));    
        }
        else{
            return $this;
        }
        
        if($start_day < 0){
            $day = $start_day + 266;
            $start_string = "P_D_{$day}";
        }
        else{
            $start_string = "B_D_{$start_day}";   
        }

        if($end_day < 0){
            $day = $end_day + 266;
            $end_string = "P_D_{$day}";
        }
        else{
            $end_string = "B_D_{$end_day}";   
        }

        $this->lifestage_period = "{$start_string}-{$end_string}";
        $this->lifestage_start_date = $start_day;
        $this->lifestage_end_date = $end_day;
        
        return $this;
    }
}
