<?php

namespace App\Models;

use Hash;
use App\Models\FeedPost;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = "questions";

    public function getUniqueImagePath($extension=null){

		if(!$extension){
			$extension = 'jpg';
		}
        $unique_id = uniqid('question_');
        $path = $unique_id.'.'.$extension;
        return $path;

    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', FeedPost::QUESTION);
    }

    public function likeCounts(){

        return $this->likes()->selectRaw('element_id, count(*) as aggregate')->groupBy('element_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'commentable_id')->where('commentable_type', FeedPost::QUESTION)->where('published', 1);
    }

    public function commentCounts(){

        return $this->comments()->selectRaw('commentable_id, count(*) as aggregate')->groupBy('commentable_id');
    }

    public function unanswered(){
        return $this->comments()->join('bc_users', 'bc_users.id', '=', 'bc_commentable.user_id')->where('bc_users.expert', 1);
    }

    public function expertCategories(){
        return $this->belongsToMany('App\Models\ExpertCategory', 'question_category_mapping', 'question_id', 'experts_category_id');        
    }

    public function preAuthenticatedKey($user){
        $base_key = config('app.one_time_base_key');
        $base_string = "question_id={$this->id}&user_id={$user->id}";
        return hash_hmac("sha256", $base_string, $base_key);
    }

    public function hashTags(){
        
        return $this->morphToMany('App\Models\HashTag', 'hash_taggable')->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany('App\Models\Photo', 'imageable_id')->where('imageable_type', FeedPost::QUESTION)->where('status', 1);
    }

    public function interestTags()
    {
        return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_question_mapping', 'question_id', 'tag_id');
    }

}
