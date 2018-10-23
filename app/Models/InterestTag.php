<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Cache, DB;

class InterestTag extends Model
{
    protected $table = 'interest_tags';

    const EVENT_WRITE = 'write';
    const EVENT_LOOKUP = 'lookup';
    const EVENT_FOLLOW = 'follow';
    const EVENT_UNFOLLOW = 'unfollow';

    public function childInterestTag() {
        return $this->hasMany('App\Models\InterestTag', 'parent_id');
    }

    public function parentInterestTag() {
        return $this->belongsTo('App\Models\InterestTag', 'parent_id');
    }

    public function articles(){

        return $this->belongsToMany('App\Models\Article', 'interest_tags_article_mapping', 'tag_id', 'article_id');
    }

    public function questions(){

        return $this->belongsToMany('App\Models\Question', 'interest_tags_question_mapping', 'tag_id', 'question_id');
    }

    public function feedpost(){

        return $this->belongsToMany('App\Models\Feedpost', 'interest_tags_feedpost_mapping', 'tag_id', 'feedpost_id');
    }

    public function collections(){

        return $this->belongsToMany('App\Models\Collection', 'interest_tags_collection_mapping', 'tag_id', 'collection_id');
    }


    public function lifestages(){

        return $this->belongsToMany('App\Models\Lifestage', 'interest_tags_lifestage_mapping', 'tag_id', 'lifestage_id');
    }


    public function subcategories(){

        return $this->belongsToMany('App\Models\ServiceCategory', 'interest_tags_subcategory_mapping', 'tag_id', 'sub_category_id');
    }

    public function services(){

        return $this->belongsToMany('App\Models\Service', 'interest_tags_subcategory_mapping', 'tag_id', 'service_id');
    }

    public function users(){

        return $this->belongsToMany('App\Models\User', 'interest_tags_user_mapping', 'tag_id', 'user_id');
    }

    public function hashtags() {
        return $this->belongsToMany('App\Models\HashTag', 'interest_tags_hashtag_mapping', 'tag_id', 'hashtag_id');
    }

    public function questionCategory() {
        return $this->belongsToMany('App\Models\QuestionCategory', 'interest_tags_lifestage_question_category_mapping', 'tag_id', 'question_category_id');
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'interesttag');
    }

    public static function getQuestionInterestMap() {

        return Cache::remember('question_interest_map', 1440, function() {
            
            $_questionInterestMap = DB::select("SELECT interest_tags.slug as interest_tag_name, interest_tags.id as interest_tag_id, question_category_mapping.lifestage_id, question_category.slug as question_name, question_category.id as question_id  FROM interest_tags_lifestage_question_category_mapping as question_category_mapping LEFT JOIN interest_tags on question_category_mapping.tag_id = interest_tags.id JOIN question_category on question_category_mapping.question_category_id = question_category.id where interest_tags.parent_id != 0");

            $questionInterestMap = [];
            foreach ($_questionInterestMap as $item) {
                $questionInterestMap[$item->lifestage_id][$item->question_id][] = $item->interest_tag_id;
            }
            return $questionInterestMap;
        });
    }

    public function getUniqueImagePath($extension=null){

        if(!$extension){
            $extension = 'jpg';
        }
        $unique_id = uniqid('interest_tag_');
        $path = $unique_id.'.'.$extension;
        return $path;

    }
}
