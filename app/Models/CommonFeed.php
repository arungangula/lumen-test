<?php

namespace App\Models;

use Feed;
use Redis;
use App\Models\FeedPost;
use Illuminate\Database\Eloquent\Model;

class CommonFeed extends Model
{
    protected $table = 'common_feed';

    public function saveCommonFeed($element_id, $element_type, $time = null, $id = null){
    	$exists = Self::where('element_id', $element_id)->where('element_type', $element_type)->first();
        if($exists){
            $id = $exists->id;
        }

        if(!$time){
            $feed_element_id = Feed::getPostId(FeedPost::POST, $element_type, $element_id);
            $redis_read = Redis::connection('read');
            $feed_element = $redis_read->hgetall($feed_element_id);
            $time = (isset($feed_element['created_at'])) ? $feed_element['created_at'] : date('Y-m-d H:i:s', time());
        }

        if(!$id){
            $cf = new Self;
        }
        else{
            $cf = Self::find($id);
        }

		$cf->element_id = $element_id;
		$cf->element_type = $element_type;
		$cf->element_time = $time;
		$cf->save();
        return true;
    }
}
