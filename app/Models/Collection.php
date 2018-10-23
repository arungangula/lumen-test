<?php
namespace App\Models;

use DB;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\UserIdScope;
use App\Models\FeedPost;

class Collection extends Model {
   
    protected $table = 'bc_content_collections';

    protected static function boot() {
    
        parent::boot();
        if(get_called_class() == 'App\Models\Collection') {
            static::addGlobalScope(new UserIdScope('user_id', 61518));    
        }
        else{
            static::addGlobalScope(new UserIdScope('user_id', 61518, '!='));       
        }
    }

    public function articles() {

         return $this->belongsToMany('App\Models\Article', 'bc_content_collection_mapping', 'collection_id', 'content_id')->where('state',1)->orderBy('created','desc');

    }

    public function articlesThumb() {

         return $this->belongsToMany('App\Models\Article', 'bc_content_collection_mapping', 'collection_id', 'content_id')->select('bc_content.id', 'bc_content.title', 'bc_content.publish_up', 'bc_content.images', 'bc_content.metadesc', 'bc_content.alias', 'bc_content.author_id')->where('state',1)->orderBy('created','desc');

    }

    public function lifestages(){

        return $this->belongsToMany('App\Models\Lifestage','bc_collection_lifestage_mapping','collection_id','lifestage_id');
    }

    public function interestTags()
    {
        return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_collection_mapping', 'collection_id', 'tag_id');
    }

    public function dailyTip()
    {
        return $this->hasMany('App\Models\DailyTip', 'collection_id');
    }

    public static function getCollectionFromUrl($url_slug){

        return Collection::where('seo_url',$url_slug)->first(); 

    }

    public static function withArticleCount(){

        return Collection::leftJoin('bc_content_collection_mapping as b','bc_content_collections.id','=','b.collection_id')
                           ->select(DB::raw('bc_content_collections.*, count(b.id) as articleCount'))
                           ->groupBy('bc_content_collections.id');

    }

    public function articleCounts(){
        return $this->belongsToMany('App\Models\Article', 'bc_content_collection_mapping', 'collection_id', 'content_id')->select(DB::raw('count(*) as count'))->where('state',1)->orderBy('created','desc')->first();
    }

    public static function getCollectionBasedOnLifestage($lifestage_ids) {

        $collectionIdQuery = DB::table('bc_collection_lifestage_mapping')->whereIn('lifestage_id',$lifestage_ids)->get();
        $collectionIds = array_map(function($arr){ return $arr->collection_id;  }, $collectionIdQuery);

        $base_query = Collection::whereIn('id',$collectionIds);
        
        return $base_query;
    }

    public static function getCollectionWithArticles($lifestage_ids) {

        $collectionIdQuery = DB::table('bc_collection_lifestage_mapping')->whereIn('lifestage_id',$lifestage_ids)->get();
        $collectionIds = array_map(function($arr){ return $arr->collection_id;  }, $collectionIdQuery);


        $base_query = Collection::whereIn('id',$collectionIds)
                     ->with(['articles' ,'articles.author']);
        
        
        return $base_query;
    }

    public function getUniqueImagePath($original_filepath=null){
        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('collection_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;

    }

    public function languages()
    {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'collection');
    }

}