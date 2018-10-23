<?php

namespace App\Models;

use DB;
use Cache;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wishlist;

class UserCollection extends Collection {


    public function articleCounts(){
        
        return $this->belongsToMany('App\Models\Article', 'bc_content_collection_mapping', 'collection_id', 'content_id')->select(DB::raw('count(*) as count'))->where('state',1)->orderBy('created','desc')->first();
    }


    public static function getReadLaterId(){
        // one week cache
        return Cache::remember('read_later_collection_id', 10080, function(){
            $collection = UserCollection::where('seo_url', 'read-later')->first();
            if($collection){
                return $collection->id;
            }
            else{
                $collection = new UserCollection;
                $collection->name = "Read Later";
                $collection->user_id = 0;
                $collection->seo_url = 'read-later';
                $collection->status = "active";
                $collection->save();

                return $collection->id;
            }
            
        });
    }

    public static function readLaterCacheKey($user_id){
        return "article_images_read_later_{$user_id}";
    }

    public static function cacheUserReadLaterImages($user_id){
        $cache_key = UserCollection::readLaterCacheKey($user_id);
        Cache::forget($cache_key);
        return Cache::remember($cache_key, 262800, function() use($user_id){            
                $wishlist = Wishlist::where('user_id', $user_id)->where('service_provider_id', 0)->orderBy('id', 'desc')->take(3)->get()->pluck('article_id');
                $articles = Article::whereIn('id', $wishlist)->where('state', 1)->get();
                $article_images = [];
                foreach ($articles as $article) {
                    $article_images[] = articleimage($article->images, 'normal', true);
                }
                return $article_images;
            });
    }
}
