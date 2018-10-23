<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BrandElement extends Model
{
    protected $table = 'brand_elements';

    const MAP_TYPE_UI_ELEMENTS = 'ui_elements';
    const MAP_TYPE_IMAGE_ELEMENTS = 'image_elements';

    const ELEMENT_ALL = 'all';
    const UI_ELEMENT_TIP_OF_THE_DAY = 'tip_of_the_day';

    const BRAND_SCOPE_SYSTEM_WIDE = 'system_wide';
    const BRAND_SCOPE_LIFESTAGE_SPECIFIC = 'lifestage_specific';
    const BRAND_SCOPE_REFERRED_USERS = 'referred_users';

    const CACHE_KEY_SYSTEM_WIDE = 'brand_elements_system_wide';
    const CACHE_KEY_LIFESTAGE_SPECIFIC = 'brand_elements_lifestage_specific';
    const CACHE_KEY_REFERRED_USERS = 'brand_elements_referred_users';
    const CACHE_KEY_BRAND_CONTENT = 'brand_content_elements_';

    const MAP_CONTENT_TYPE_TITLE = 'content_element_title';
    const MAP_CONTENT_TYPE_HEADING = 'content_element_heading';
    const MAP_CONTENT_TYPE_BODY_OF_CONTENT = 'content_element_body_of_content';
    const MAP_CONTENT_TYPE_CTA_TEXT = 'content_element_cta_text';
    const MAP_CONTENT_TYPE_DEEPLINK = 'content_element_deeplink';

    public function brand() {
        return $this->belongsTo('App\Models\Service', 'brand_id');
    }

    public function uiElements() {
        return config('brandElement.uiElements');
    }

    public function contentElements() {
        return $this->hasMany('App\Models\BrandContentElementMapping');
    }

    public function contentElementTitle() {
        return $this->hasOne('App\Models\BrandContentElementMapping')->where('map_type', 'content_element_title');
    }

    public function contentElementHeading() {
        return $this->hasOne('App\Models\BrandContentElementMapping')->where('map_type', 'content_element_heading');
    }

    public function contentElementBodyOfContent() {
        return $this->hasOne('App\Models\BrandContentElementMapping')->where('map_type', 'content_element_body_of_content');
    }

    public function contentElementCtaText() {
        return $this->hasOne('App\Models\BrandContentElementMapping')->where('map_type', 'content_element_cta_text');
    }

    public function contentElementDeeplink() {
        return $this->hasOne('App\Models\BrandContentElementMapping')->where('map_type', 'content_element_deeplink');
    }

    public function getUniqueImagePath($original_filepath=null) {
        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('brandelement_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }

    public function lastModifiedBy() {
        return $this->belongsTo('App\Models\User', 'last_modified_by', 'id');
    }
}
