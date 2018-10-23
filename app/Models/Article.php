<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\FeedPost;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


//Article Model

class Article extends Model {

    protected $table = 'bc_content';

    public $timestamps = false;

    const FIRST_TRIMESTER = '1';
    const SECOND_TRIMESTER = '2';
    const THIRD_TRIMESTER = '3';
    const NEW_PARENT = '4';
    const TODDLER = '5';


    public function category()
    {
        return $this->belongsTo('App\Models\Article_category','catid');
    }

    public function author()
    {
        return $this->belongsTo('App\Models\User','author_id');
    }

    public function keywordMapping()
    {
        return $this->belongsToMany('App\Models\Keyword', 'keywords_mapping', 'element_id', 'keyword_id')->where('element_type', 'article');
    }

    public function interestTags()
    {
        return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_article_mapping', 'article_id', 'tag_id');
    }

    public function languages()
    {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'article');
    }

    public static function likes() {
        return $this->morphMany('Likes', 'provider','type','service_provider_id');
    }
    //To change image path to the json that will be stored in the images column
    public static function getImageJson($image_path){
        $image = [];
        $image['image_intro'] = $image_path;
        $imageJson = $this->imageJson($image);


        return json_encode($imageJson);
    }

    public function imageJson($image){
        return ["image_intro"           =>  getDefault($image['image_intro'], ''),
                "float_intro"           =>  getDefault($image['float_intro'], ''),
                "image_intro_alt"       =>  getDefault($image['image_intro_alt'], ''),
                "image_intro_caption"    => getDefault($image['image_intro_caption'], ''),
                "image_fulltext"         => getDefault($image['image_fulltext'], ''),
                "float_fulltext"         => getDefault($image['float_fulltext'], ''),
                "image_fulltext_alt"     => getDefault($image['image_fulltext_alt'], ''),
                "image_fulltext_caption" => getDefault($image['image_fulltext_caption'], ''),
                "image_shuffle"         =>  getDefault($image['image_shuffle'], '')
                ];
    }

    public function images(){

        return $this->morphMany('App\ContentImage', 'content');
    }

    public function comments(){

        return $this->hasMany('App\Models\Comment', 'commentable_id')->where('commentable_type',FeedPost::ARTICLE)->where('published', 1);
    }

    public function commentsCount(){

        return $this->comments()->selectRaw('commentable_id, count(*) as aggregate')->groupBy('commentable_id');
    }

    public function collections(){

        return $this->belongsToMany('App\Models\Collection', 'bc_content_collection_mapping', 'content_id', 'collection_id');

    }

    public function userCollections(){

        return $this->belongsToMany('App\Models\UserCollection', 'bc_content_collection_mapping', 'content_id', 'collection_id');

    }

    public function lifestages(){

        return $this->belongsToMany('App\Models\Lifestage', 'bc_content_lifestages_mapping', 'article_id', 'lifestage_value');

    }

    public function festivals(){

        return $this->belongsToMany('App\Models\Festival', 'bc_festival_article_mapping', 'article_id', 'festival_id');

    }

    public function dailyTip()
    {
        return $this->hasMany('App\Models\DailyTip', 'article_id');
    }

    public function area(){

        return $this->belongsToMany('App\Models\City', 'bc_content_city_mapping', 'article_id', 'area_id');

    }

    public function location(){

        return $this->belongsToMany('App\Models\City', 'bc_content_city_mapping', 'article_id', 'location_id');

    }

    public function areas(){

        return $this->belongsToMany('App\Models\City', 'bc_content_city_mapping', 'article_id', 'area_id');

    }

    public function hashTags(){
        
        return $this->morphToMany('App\Models\HashTag', 'hash_taggable')->withTimestamps();
    }

    /*public function postLikes()
    {
        return $this->morphMany('App\Models\PostLike', 'likeable');
    }*/

    public function postLikes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', 'article');
    }

    public function likeCounts(){

        return $this->postLikes()->selectRaw('element_id, count(*) as aggregate')->groupBy('element_id');
    }

      //Serializes articles for search and recommendation lists
    public function getArticlesWithId ($articles){

      $articles = Article::with('category')->whereIn('id', $articles)->get();
      $result = [];
      $articleTransformer = new ArticleTransformer;
      foreach($articles as $article){
        $result[] = $articleTransformer->transform($article);
      }
        return $result;
    }

    //To get likes count of articles
    public function  getLikesCount($spid){

      $type='article';
      $sql="SELECT count(*) AS likes FROM bc_services_likes  where service_provider_id = '".$spid."' AND likes_status=1  AND type='".$type."'";

        $data = DB::select(DB::raw($sql));

       return $data[0]->likes;
    }



    private function getCoverImageName(UploadedFile $uploadedFile){
        $name = substr(uniqid(),0,10)."_cover";
        $extension = $uploadedFile->getClientOriginalExtension();
        return $name.".".$extension;
    }

    public function saveCoverImage(UploadedFile $uploadedFile,$updateDB=false){
        $path = join('/', array(config('aws.articles_directory'), $this->id, $this->getCoverImageName($uploadedFile)));
        $fileResult = storeOnS3($path , file_get_contents($uploadedFile->getRealPath()));
        if($fileResult){
            $this->images = Article::getImageJson($path);
            if($updateDB){
                $this->save();
            }
            return true;
        }

            return false;
    }


    public function getUniqueImagePath($original_filepath=null){

        if(!$original_filepath){
            if($this->image_url){
                $pInfo = pathinfo($this->getCoverImageUrl());
            }
        } else {
            $pInfo = pathinfo($original_filepath);
        }
        if(isset($pInfo)){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('article_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;

    }

    public function getCoverImageUrl(){

        $images = json_decode($article->images,true);
        $img = $images['image_intro'];
        return $img;

    }

    public function getArticleOldUrl(){

        return url(join('/',[ $this->category->alias, $this->id.'-'.$this->alias ]));

    }

    public function setCoverImageUrl($image_path, $shuffle_image_path){

        $image = [];
        $image['image_intro'] = $image_path;
        $image['image_shuffle'] = $shuffle_image_path;
        $image_json = $this->imageJson($image);

        $this->images = json_encode($image_json);
    }


    //Vegeta
    public static function getIndexableArticles ()
    {
        //return all the articles which satisfy the SQL query.
        // $data   =   Article::with(['category'=>function($query)
        //                             {
        //                                 $query  ->where('published','=',1)
        //                                         ->where('path','like','learn%')
        //                                         ->whereNotIn('level', [0,1]);
        //                             }],['lifestages'],['author'])
        //             ->where('state','=',1);
        $data   =   Article::with(['collections'],['lifestages'],['author'], ['area'],
                        ['interestTags'=>function($query)
                        {
                            $query->with('lifestages');
                        }], ['postLikes'], ['comments'])
                    ->where('state','=',1);
        return $data;
    }

    public static function getAdArticles ()
    {
        $ads    =   SponsoredItem::with('lifestages','locations','areas')
                                ->where('aditem_type','article')
                                ->where('ad_type','recommend')
                                ->where('status',1)
                                ->get()->unique();
        return $ads;
    }

    //Article Ajax Requests

    public static function formatTitle($title)
    {
        $title = explode(" ", ucwords($title));
        $title = join(" ", array_map(function($item) {
            if(strlen($item) <= 2)
                return strtolower($item);
            return $item;
        }, $title));
        return ucfirst($title);
    }

    public static function getArticleForLifestage($lifestage=4, $take=6) {
        
        return DB::table("bc_content_lifestages_mapping")->join('bc_content', 'bc_content.id', '=', 'bc_content_lifestages_mapping.article_id')->where('lifestage_value', $lifestage)->where('bc_content.state', 1)->orderBy('bc_content.id', 'desc')->take($take)->get();
    }
}
