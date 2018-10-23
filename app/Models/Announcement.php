<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    const LIFESTAGE = "lifestage";
    const LOCATION = "location";
    const CITY = "city";

    const PLATFORM_BOTH     = 'both';
    const PLATFORM_APP      = 'app';
    const PLATFORM_WEB      = 'web';
    const PLATFORM_ANDROID  = 'android';
    const PLATFORM_IOS      = 'ios';

    const TOP_FEED_ANNOUCEMENT = "topfeedannouncement";

    const CACHE_KEY_FEED_ANNOUNCEMENT_APP = "cache_key_feed_announcement_app";
    const CACHE_KEY_FEED_ANNOUNCEMENT_WEB = "cache_key_feed_announcement_web";
    const CACHE_KEY_FEED_ANNOUNCEMENT_IOS = "cache_key_feed_announcement_ios";
    const CACHE_KEY_FEED_ANNOUNCEMENT_APP_TOP = "cache_key_feed_announcement_app_top";
    const CACHE_KEY_FEED_ANNOUNCEMENT_WEB_TOP = "cache_key_feed_announcement_web_top";
    const CACHE_KEY_FEED_ANNOUNCEMENT_IOS_TOP = "cache_key_feed_announcement_ios_top";

    const PERSONALIZE_CARD_MODE_LIFESTAGE = 'lifestage';
    const PERSONALIZE_CARD_MODE_AGE_ON_APP = 'age_on_app';
    const PERSONALIZE_CARD_MODE_BOTH = 'both';

    public function lifestages() {

    	return $this->belongsToMany('App\Models\Lifestage', 'announcement_contraints', 'announcement_id', 'entity_id')->where('entity_type', Announcement::LIFESTAGE);
    }

    public function locations() {

        return $this->belongsToMany('App\Models\Location', 'announcement_contraints', 'announcement_id', 'entity_id')->where('entity_type', Announcement::LOCATION);
    }

    public function cities() {

        return $this->belongsToMany('App\Models\City', 'announcement_contraints', 'announcement_id', 'entity_id')->where('entity_type', Announcement::CITY);
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'announcement');
    }

    public function genders() {
        return $this->belongsToMany('App\Models\Gender', 'gender_entity_mappings', 'entity_id', 'gender_id')->where('entity_type', 'announcement');
    }

    public function getUniqueImagePath($original_filepath=null){

        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('announcement_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }
}