<?php

namespace App\Models;

use App\Scopes\ReviewTypeScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use DB;
use App\Models\FeedPost;
use App\Tranformers\ReviewTransformer;
use App\Models\Area;
use Cache;

class Review extends Model{

    protected $table = 'sp_reviews';

    public $timestamps = false;

    const MEDIUM_WD = 'web_desktop';
    const MEDIUM_WM = 'web_mobile';
    const MEDIUM_AND = 'android_app';
    const MEDIUM_ADP = 'admin_panel';

    const IMAGE = "image";
    const VIDEO = "video";

    const TYPE_SERVICE = "serviceprovider";
    const TYPE_PACKAGE = "package";

    protected static function boot() {
        parent::boot();
        if(get_called_class() == 'App\Models\Review') {
            static::addGlobalScope(new ReviewTypeScope());    
        }
    }

    public function user() {

      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function replies(){

        return $this->hasMany('App\Models\ReviewReply','sp_review_id');
    }

    public function likes()
    {
      return $this->morphMany('App\Models\PostLike', 'likeable');
    }

    public function getUsernameAttribute(){

      //dd($this->user['name']);
      return $this->user['name'];
    }

    // public function getMomstaridAttribute(){

    //   //dd($this->user['id']);
    //   return $this->user['id'];
    // }

    // public function getTotalreviewsAttribute(){
    //   //dd($this->where('user_id', $this->getMomstaridAttribute())->count());
    //   return $this->where('user_id', $this->getMomstaridAttribute())->count();
    // }

    public static function getList(){

      return ['service already exist','service added','service does not exist'];
    }

    public function serviceprovider() {//Consider this for Camel Casing

      return $this->belongsTo('App\Models\Service','provider_id');
    }

    public function getNameAttribute(){

      //dd($this->serviceprovider);
      return $this->serviceprovider['name'];
    }

    public function postLikes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', 'review');
    }

    public function likeCounts(){

        return $this->postLikes()->selectRaw('element_id, count(*) as aggregate')->groupBy('element_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'commentable_id')->where('commentable_type', FeedPost::REVIEW)->where('published', 1);
    }

    public function commentsCount(){

        return $this->comments()->selectRaw('commentable_id, count(*) as aggregate')->groupBy('commentable_id');
    }

    public function hashTags(){
        
        return $this->morphToMany('App\Models\HashTag', 'hash_taggable');
    }

    public function getStatusAttribute() {
        $published = ($this->published) ? $this->published : 0;
        return config("admin.review_status")[$published];
    }

    public static function getReviewWithId($review_id){

      $review = Review::with('user','serviceprovider')->find($review_id);
      if($review){
        $reviewTransformer = new ReviewTransformer();
        return $reviewTransformer->tranform($review);
      } else {
        return null;
      }
    }

    //Get user review count
     public static function getReviewCount($user){
     	$user_id=$user->id;
    	$reviews= Review::where('user_id','=',$user_id)->get()->toArray();
    	return $reviews;
     }

    //Get service provider reviews
    public static function getReviewsOfService($sid){
       //Getting review data
       $reviews= Review::where('provider_id','=',$sid)->where('sp_reviews.published',1)->where('user_id','!=','')->where('review_type','=','serviceprovider')->select('user_id')->leftjoin('bc_users','user_id','=','bc_users.id')->select('review_title','review','time','bc_users.name','bc_users.avatar','sp_reviews.id','sp_reviews.user_id')->get()->toArray();
       return $reviews;
    }

    public static function getReviewsOfUserObj($user_id)
    {
       //Getting review data
       // $reviews= Review::where('user_id','=',$user_id)->where('sp_reviews.published',1)->leftjoin('bc_services_providers_new','provider_id','=','bc_services_providers_new.id')->select('review_title','review','time', 'provider_id','bc_services_providers_new.id as service_id','bc_services_providers_new.name','bc_services_providers_new.location as service_location','bc_services_providers_new.email','sp_reviews.id')->get();
       // return $reviews;

       $reviews=Review::with(['serviceprovider' => function($query)
                        {
                          $query->with('area');
                        }])
                      ->where('user_id','=',$user_id)
                      ->where('published','=',1)
                      ->get()->toArray();
       if(count($reviews)==0) return $reviews;

       //Preparing Result
       foreach($reviews as $review)
       {
          if($review['serviceprovider']['area_id'] == 0 and $review['serviceprovider']['online_flag'] == 1)
          {
            $customCity = isset(explode(',',$review['serviceprovider']['home_delivery_cities'])[0]) ? explode(',',$review['serviceprovider']['home_delivery_cities'])[0]:1;
            $area_slug = Area::where('id',$customCity)->first()->city_slug;
          }
          else
            $area_slug = $review['serviceprovider']['area']['city_slug'];

          $provider_url = serviceurl($review['serviceprovider']['name'], $area_slug, $review['serviceprovider']['id'] );

          $entry['provider_id']=$review['provider_id'];
          $entry['name']=$review['serviceprovider']['name'];
          $entry['service_id']=$review['serviceprovider']['id'];
          $entry['service_location']=$review['serviceprovider']['location'];
          $entry['email']=$review['serviceprovider']['email'];
          $entry['id']=$review['id'];
          $entry['review_title']=$review['review_title'];
          $entry['review']=$review['review'];
          $entry['time']=$review['time'];
          $entry['user_id']=$user_id;
          $entry['provider_url']=$provider_url;
          $entry['media_type']=$review['media_type'];
          $result[]=$entry;
       }
       return $result;
    }

     //Get service provider reviews
    public static function getReviewsOfServiceObj($sid,$object_only=false){

       //Getting review data


      // $reviews= Review::with('user','replies')->where('provider_id','=',$sid)->where('sp_reviews.published',1)->where('user_id','!=','')->select('user_id')->leftjoin('bc_users','user_id','=','bc_users.id')->select('review_title','review','time','bc_users.name','bc_users.avatar','sp_reviews.id','sp_reviews.user_id');

       $reviews= Review::with(['user',
                  'likeCounts',
                  'replies' => function($query)
                    {
                      $query->where('published', 1);
                    },
                  'commentsCount'
                  ])->where('provider_id','=',$sid)
                  ->where('sp_reviews.published',1)
                  ->where('user_id','!=','')
                  ->select('user_id')
                  ->leftjoin('bc_users','user_id','=','bc_users.id')
                  ->orderby('id', 'desc')
                  ->select('review_title','review','time','bc_users.name','bc_users.avatar','sp_reviews.id','sp_reviews.user_id', 'published');

       if($object_only){
         return $reviews;
       } else {
         return $reviews->get();
       }

    }

    /**
     * get the reviews of a service with comments
     * @param  integer $sid
     * @return collection
     */
    public static function getReviewsOfServiceWithComments($sid)
    {
      return Review::with(['user','likeCounts','comments' => function($query){$query->where('published', 1);}, 'commentsCount'])->where('provider_id','=',$sid)->where('sp_reviews.published',1)->where('user_id','!=','')->select('user_id')->leftjoin('bc_users','user_id','=','bc_users.id')->select('review_title','review','time','bc_users.name','bc_users.avatar','sp_reviews.id','sp_reviews.user_id', 'sp_reviews.review_image', 'sp_reviews.media_type', 'published')->orderby('id', 'desc')->get();
    }

    //Get Article reviews
    public static function getReviewsOfArticles($sid){
       //Getting review data
       $reviews= Review::where('provider_id','=',$sid)->where('sp_reviews.published',1)->where('review_type','=','article')->get()->toArray();
       return $reviews;
    }

    //Below functions are from the Website Review Model and are required for the functionality of the
    //NewsFeed Model and the Feed API(GetStream Cron)

    public function getReviews($spid,$review_count=3,$type='serviceprovider'){
          $sql = "SELECT a.id as review_id,a.review,u.id,p.social_profile_uid AS slogin_id,p.provider,u.f_name,u.l_name
         FROM sp_reviews as a
          JOIN bc_users as u on a.user_id=u.id
          LEFT JOIN user_social_profile_providers as p  on p.user_id=a.user_id and p.provider='facebook'
         WHERE a.provider_id = '".$spid."' AND a.review_type='".$type."' ORDER BY a.id DESC";


        if($review_count>0){
            $sql .=" LIMIT ".$review_count;
        }

        $result = DB::select(DB::raw($sql));

        return $result;

    }

    public function getUniqueImagePath($original_filepath=null){

        if(!$original_filepath){
            $extension = '.jpg';
        } else {
            $pInfo = pathinfo($original_filepath);
            $extension = $pInfo['extension'];
        }
        $unique_id = uniqid('review_');
        $path = join('/',[ $this->provider_id, $unique_id.'.'.$extension]);
        return $path;
    }


     public function getReviewCountRe($id,$type=''){

        $sql="SELECT count(*) AS count FROM sp_reviews as c where c.provider_id = '".$id."' and c.user_id <> 0 and c.published = 1";
        if($type!=''){
            $sql .=" AND c.review_type='".$type."'";
        }

        $data = DB::select(DB::raw($sql));
        return $data[0]->count;
    }

    public function getReviewCommentcount($review_id){
        $sql = "SELECT count(*) as replies FROM sp_replytoreview WHERE sp_review_id = $review_id AND published = 1";

        $data = DB::select(DB::raw($sql));

        return $data[0]->replies;
    }

    public function share_count($review_id){
        $sql = "SELECT count(*) as count FROM sp_reviews_share WHERE review_id = '".$review_id."'";
         $data = DB::select(DB::raw($sql));

        return $data[0]->count;
    }

    public function getReviewLikecount($review_id){

        $sql = "SELECT count(*) as likes FROM sp_review_likes WHERE review_id = $review_id";

        $data = DB::select(DB::raw($sql));

        return $data[0]->likes;
    }

    public function getObjectReplytoReviewsByReviewId($review_id,$page='',$type='',$comment_count='3'){


       $sql = "SELECT a.*, u.f_name,u.l_name,p.provider, p.social_profile_uid AS slogin_id
                FROM sp_replytoreview as a
                JOIN bc_users u on a.user_id = u.id
                LEFT JOIN user_social_profile_providers p on u.id = p.user_id  and p.provider = 'facebook'
                WHERE a.sp_review_id = $review_id AND a.published = 1";


            if(isset($page) && $page!=''){

             $sql .=" ORDER BY a.created_date DESC";
            //if($page!='view_more'){
                if($comment_count>0){
                $sql .=" LIMIT ".$comment_count;
                }
            }


            $data = DB::select(DB::raw($sql));

            $data = json_decode(json_encode($data), FALSE);
            return $data;


    }

    public static function getLikedReviewIds($userId, $reviewIds = [])
    {
      $reviews = DB::table('post_likes')->select('element_id')
        ->where('element_type', 'review')->where('user_id', $userId)->whereIn('element_id', $reviewIds)->get();
      return array_map(function($item) {
        return $item->element_id;
      }, $reviews);
    }

    public static function userReviewCount($user_id, $status = 1){
        $reviewCount = Cache::remember('user_review_count_'.$user_id."_".$status, 1440, function() use ($user_id, $status){
            return self::where('user_id', $user_id)->where('published', $status)->count();
        });
        return $reviewCount;
    }

    public static function serviceReviewCount($service_id, $status = 1){
        $reviewCount = Cache::remember('service_review_count_'.$service_id."_".$status, 1440, function() use ($service_id){
            return self::where('provider_id', $service_id)->where('published', 1)->count();
        });
        return $reviewCount;
    }
}
