<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BrandStory extends Model
{
    
    public function getUniqueImagePath($original_filepath=null){

        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('brandstory_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }

    public function recordView($userId) {

        DB::table('brand_story_users')->insert(['user_id' => $userId, 'brand_story_id' => $this->id, 'viewed_on' => date('Y-m-d h:i:s')]);
    }

    public function postLikes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', 'brandstory');
    }

    public function postShares()
    {
        return $this->hasMany('App\Models\FeedShare', 'entity_id')->where('entity_type', 'brandstory');
    }
}
