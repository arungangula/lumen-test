<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HashTag extends Model
{
	const EVENT_WRITE = 'write';
	const EVENT_LOOKUP = 'lookup';
	const EVENT_FOLLOW = 'follow';
	const EVENT_UNFOLLOW = 'unfollow';
	
    public function feedPosts()
    {
        return $this->morphedByMany('App\Models\FeedPost', 'hash_taggable');
    }

    public function interestTags() {
    	return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_hashtag_mapping', 'hashtag_id', 'tag_id');
    }
}
