<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedPost extends Model
{
    protected $table = "post_content";

    const ARTICLE           = 'article';        // type
    const EVENT             = 'event';          // type
    const REVIEW            = 'review';         // type
    const FEEDPOST          = 'feedpost';       // type
    const QUESTION          = 'question';       // type
    const LIKE              = 'like';           // action
    const SERVICE_RECOMMEND = 'recommend';      // action
    const WISHLIST          = 'wishlist';       // action
    const BOOKMARK          = 'bookmark';       // action
    const EVENT_REGISTER    = 'register';       // action
    const POST              = 'post';           // post element
    const CATEGORY          = 'category';       // service category
    const EXPERT_CATEGORY   = 'expert_category';
    const COLLECTION        = 'collection';     // collection
    const USER_COLLECTION   = 'user_collection';// user collection
    const USER_COLLECTION_MAPPING   = 'user_collection_mapping';// user collection mapping
    const USER              = 'user';           // user element
    const USERFEED_SERVED   = 'userfeed_served';// served user feed
    const USERFEED          = 'userfeed';       // user's feed element
    const USER_ACTIVITY     = 'useractivity';   // user's feed element
    const COMMONFEED        = 'commonfeed';     // commonfeed key
    const FOLLOWING         = 'following';      // user's following
    const FOLLOWERS         = 'followers';      // user's following
    const FOLLOWING_FOLLOWERS = 'following_followers';
    const INVITEES          = 'invitees';
    const SERVICE           = 'service';        // service element
    const SERVICE_IMAGE     = 'service_image';  // service images
    const HAS_FEED          = 'hasfeed';        // users has feed
    const ENGAGED_MOMSTAR   = 'engaged_momstar';// engaged momstar
    const COMMENT           = 'comment';        // comment key name
    const USER_LAST_COMMENT = 'user_last_comment';// users last comment key name
    const EXPERTS           = 'expert_user';    // expert sorted set key name
    const HORZ_CARDS        = 'horizontal_cards';
    const ANNOUNCEMENT_CARD = 'announcement';
    const PROMOTION         = 'promotion';
    const BANNER_CARDS      = "banner_cards";
    const BANNER            = "banner";
    const PACKAGE           = "package";
    const PRODUCT           = "product";
    const PRODUCT_DETAIL    = "product_detail";
    const GENERIC_CARD      = "generic_cards";
    const CARD_TYPE_GENERIC = "generic_card";
    const BUTTON            = "button";
    const USER_FOLLOWS_FIRST= "user_follows_first";
    const USER_FOLLOWS      = "user_follows";
    const TIP               = "tip";
    const GRID              = "grid";
    const BANNER_CATEGORY   = "banner_category";
    const SERVICE_CATEGORY  = "service_category";
    const STRIP             = "strip";
    const HASHTAG           = "hashtag";
    const INTERESTTAG       = "interesttag";

    //growth tracker
    const METRIC             = "metric";
    const METRIC_HEIGHT      = "metric_height";
    const METRIC_WEIGHT      = "metric_weight";
    const METRIC_INFO        = "info";
    const METRIC_DETAIL      = "metric_detail";
    const METRIC_VACCINATION = "vaccine";
    const METRIC_VACCINATION_DETAIL = "metric_vaccination_detail";
    const USER_METRIC_VACCINATION   = "user_metric_vaccination";
    const METRIC_VACCINATION_FEED   = "vaccine_feed";
    const METRIC_FEED        = "metric_feed";
    const METRIC_HEIGHT_FEED = "metric_height_feed";
    const METRIC_WEIGHT_FEED = "metric_weight_feed";
    const DAILY_CARD_FEED    = "daily_card_feed";
    const METRIC_INFO_FEED   = "info_feed";
    const METRIC_INFO_ACHIEVED_FEED = "info_achieved_feed";

    const REQUEST_CALLBACK  = "request_call_back";
    const REQUEST_CALLBACK_TYPE_MOBILE  = "mobile";
    const REQUEST_CALLBACK_TYPE_CHAT    = "chat";

    const DAILY_CARD    = "daily_card";
    const OVERFLOW_CARDS = "overflow_cards";

    //const FEED_ELEMENT_LIMIT= 1500;             // limit of elements in a user's feed

    const BEHAVE_DETAIL = 'details';
    const BEHAVE_CARD = 'card';
    const BEHAVE_THUMB = 'thumb';
    const BEHAVE_LISTING = 'listing';

    // order statuses of the post
    const ORDERING_DATA     = 'ordering_data';
    const ORDERING_LIKED    = 'order_liked';
    const ORDERING_COMMENTED= 'order_commented';
    const ORDERING_SHARED   = 'order_shared';
    const ORDERING_ANSWERED = 'order_answered';
    const ORDERING_RECOMMENDED = 'order_recommended';

    const ALL = 'all';


    // user sets types
    const USER_SETS = 'user-set';
    const USER_SETS_LIFESTAGE = 'lifestage';
    const USER_SETS_LOCATION = 'location';
    const USER_SETS_LIFESTAGE_LOCATION = 'lifestage-location';
    const USER_SETS_AUTHORS = 'authors';
    const USER_SETS_HELPFULL_MOMS = 'help-full-moms';
    const USER_SETS_QUESTION_CATEGORY = 'question-category';

    const CATEGORY_VERTICAL_CARD_FIRST = 'category_card_first';
    const CATEGORY_VERTICAL_CARD = 'category_card';
    const CATEGORY_HORIZONTAL_CARD = 'category_horizontal_card';

    const BRANDSTORY = 'brandstory';

    const EXPLORESTORE = "explore-store";
    const GENERIC_CARD_CACHE = "genericcard";
    const ENTITY_COLLECTION = "entity_collection";

    const CHAT_GROUP = "chat_group";
    const TESTIMONIAL = "testimonial";

    const GCMPUSH = "gcmpush";

    const USER_PINNED_POSTS = "u_pin_pos";

    const PARENT_CATEGORY = "parent_category";
    const SUB_CATEGORY = "sub_category";
    const BRAND_PRODUCT = "brand_product";
    const PRODUCT_CATEGORY = "product_category";

    const MIXED_SECTION = "mixed_section";
    const DAILY_CARD_TIMELINE = "timeline";

 	public function getUniqueImagePath($extension=null){

		if(!$extension){
			$extension = 'jpg';
		}
        $unique_id = uniqid('feedpost_');
        $path = $unique_id.'.'.$extension;
        return $path;

    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', FeedPost::FEEDPOST);
    }

    public function likeCounts(){

        return $this->likes()->selectRaw('element_id, count(*) as aggregate')->groupBy('element_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'commentable_id')->where('commentable_type', FeedPost::FEEDPOST)->where('published', 1);
    }

    public function commentCounts(){

        return $this->comments()->selectRaw('commentable_id, count(*) as aggregate')->groupBy('commentable_id');
    }

    public function tags(){
        
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    public function hashTags(){
        
        return $this->morphToMany('App\Models\HashTag', 'hash_taggable')->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany('App\Models\Photo', 'imageable_id')->where('imageable_type', FeedPost::FEEDPOST)->where('status', 1);
    }

    public function interestTags(){
        return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_feedpost_mapping', 'feedpost_id', 'tag_id');
    }

    public function languages(){
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'feedpost');
    }

    public function getNameAttribute(){
        if(isset($this->user)){
            return $this->user->name;
        }
        else{
            return '';
        }
    }
}
