<?php
namespace App\Models;

use DB;
use Hash;
use Mail;
use Facebook;
use Slugify;
use Storage;
use Google_Client;
use Google_Service_Plus;
use Request;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

use SleepingOwl\Models\SleepingOwlModel;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Interfaces\INotifiable as Notifiable;
use App\Interfaces\IPointsHolder as PointsHolder;
use App\Interfaces\IUrlable as Urlable;
use App\Http\Controllers\NotificationController;
use App\Models\Review;
use App\Models\Babies;
use App\Models\Wishlist;
use App\Models\SocialUserProfile;
use App\Helpers\Util;
use App\Models\Image;
use App\Models\MomRecommendation;
use App\Models\City;
use App\Models\Referral;
use App\Models\UserFriend;
use App\Jobs\SaveFriends;
use App\Jobs\FollowFriends;
use App\Jobs\OnUserOnboard;
use App\Helpers\Quickblox;
use App\Jobs\QueueEmail;
use App\Models\UserSource;
use App\Models\PersonalizedCardTemplate;
use App\Models\Promotion;
use App\Models\AppOpenLog;
use Redis;
use App\Feed\Cache\UserCache;

use App\Events\UserWasCreated;

use App\Repositories\PromotionRepository;
use App\Services\Communication\Chat\Adapters\SendBirdAdapter;
use App\Services\Communication\Chat\Clients\SendBird;

class User extends SleepingOwlModel implements AuthenticatableContract, CanResetPasswordContract, Notifiable, PointsHolder, Urlable {

    use Authenticatable, CanResetPassword;
    use EntrustUserTrait;
    use DispatchesJobs;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    //$firstTime notes if the session is user's first time sign on
    
    const CHANNEL_WEB    = "web";
    const CHANNEL_MOBILE = "mobile";

    const LOGIN_FB = "facebook";
    const LOGIN_GOOGLE = "google";
    
    private static $firstTime;

    protected $table = 'bc_users';

    // contants for user types
    const TYPE_OPS_EXEC = 'ops_executive';

    //user images default image
    const DEFAULT_IMAGE='default.jpg';
    //user images directory
    const BUCKET_URL='https://s3-ap-southeast-1.amazonaws.com/babychakraserviceproviders/users/';

    // constants for user tracking
    const TRACKING_TYPE         = 'user_tracking_type'; // paid/unpaid
    const TRACKING_UTM_SOURCE   = 'user_tracking_utm_source';
    const TRACKING_UTM_MEDIUM   = 'user_tracking_utm_medium';
    const TRACKING_UTM_CAMPAIGN = 'user_tracking_utm_campaign';
    const TRACKING_UTM_KEYWORDS = 'user_tracking_utm_keywords';
    const TRACKING_SOURCE_URL   = 'user_tracking_source_url';
    const EXPERT_BADGE          = 'expert_badge';
    const EXPERT_FEED_BADGE     = 'expert_feed_badge';
    const MOMSTAR_BADGE         = 'mom_star_badge';
    const MOMSTAR_FEED_BADGE    = 'mom_star_feed_badge';

    const MOM_BUDDY_BADGE       = 'mom_buddy_badge';
    const MOM_BUDDY_FEED_BADGE  = 'mom_buddy_feed_badge';
    const BESTIE_MOMSTAR_BADGE  = 'bestie_momstar_badge';
    const BESTIE_MOMSTAR_FEED_BADGE     = 'bestie_momstar_feed_badge';
    const SUPER_MOMSTAR_BADGE           = 'super_momstar_badge';
    const SUPER_MOMSTAR_FEED_BADGE      = 'super_momstar_feed_badge';
    const TOP_MOMSTAR_BADGE             = 'top_momstar_badge';
    const TOP_MOMSTAR_FEED_BADGE        = 'top_momstar_feed_badge';

    const ECA_CATEGORY_ID = 376;

    protected $hidden = array('username','password', 'registerDate','lastvisitDate','last_visit_city',
                              'registration_ip','last_visit_ip','http_refferer','resetCount','lastResetTime'
                              ,'facebook_accesstoken','invitation_code','source','invited_by' );

    //Set Password with Hash Attribute
    public function setPasswordAttribute($pass){

        $this->attributes['password'] = Hash::make($pass);

    }

     //Set Password with Hash Attribute
    public function setMobileNumberAttribute($number){

        $this->attributes['mobile_number'] = format_number($number);

    }

    public function babies(){
        return $this->hasMany('App\Models\Babies','parent_id','id');
    }

    public function articles(){
        return $this->hasMany('App\Models\Article', 'author_id');
    }

    public function articlesCount(){
        return $this->articles()->selectRaw('author_id, count(*) as aggregate')->groupBy('author_id');
    }

    public function contentCount(){
        return $this->articles()->where('state', 1)->count();
    }

    public function feedposts() {
        return $this->hasMany('App\Models\FeedPost', 'user_id');
    }

    public function feedpostsCount(){
        return $this->feedposts()->where('published', 1)->count();
    }

    public function questions() {
        return $this->hasMany('App\Models\Question', 'user_id');
    }

    public function questionsCount(){
        return $this->questions()->where('published', 1)->count();
    }

    public function partner(){
        return $this->hasOne('App\Models\User','id','partner_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\User_category','id','user_id');
    }

    public function questionCategory(){
        return $this->belongsToMany('App\Models\ExpertCategory', 'user_question_category_mapping', 'user_id', 'question_category_id');
    }

    public function reviews(){

        return $this->hasMany('App\Models\Review','user_id')->where('published', 1);
    }

    public function shipping_addresses(){
        return $this->hasMany('App\Models\UserShippingAddress','user_id');
    }

    public function reviewsCount(){
        return $this->reviews()->selectRaw('user_id, count(*) as aggregate')->where('published', 1)->groupBy('user_id');
    }

     public function redeems(){

        return $this->hasMany('App\Models\UserRedeemActivity','user_id');
    }

    public function likes(){

        return $this->hasMany('App\Models\Likes','user_id');
    }

    public function likesCount(){

        return $this->likes()->selectRaw('user_id, count(*) as aggregate')->groupBy('user_id');
    }

    public function liked_services(){

        return $this->belongsToMany('App\Models\Service', 'bc_services_likes', 'user_id', 'service_provider_id')->with([ 'subcategories'=> function($q){ $q->select('category_name');  } ]);
    }

    public function wishlists(){

         return $this->belongsToMany('App\Models\Service', 'bc_services_wishlist', 'user_id', 'service_provider_id');
    }

    public function bookmark_articles(){

         return $this->belongsToMany('App\Models\Article', 'bc_services_wishlist', 'user_id', 'article_id');
    }

    public function following(){

        return $this->belongsToMany('App\Models\User', 'user_follows', 'user_id', 'following_user_id');

    }

    public function friends(){
        return $this->hasMany('App\Models\UserFriend', 'user_id')->where('bc_user_friends.friend_id', '!=', 0);
    }


    public function user_lifestage(){

        return $this->hasOne('App\Models\Lifestage', 'id', 'lifestage_id');
    }

    public function last_location(){

        return $this->hasOne('App\Models\UserLocation', 'user_id')->orderby('id', 'desc')->take(1);
    }

    public function location(){

        return $this->hasOne('App\Models\Location', 'id', 'location_id');
    }

    public function events(){

        return $this->belongsToMany('App\Models\Event', 'event_registration', 'user_id', 'event_id');

    }

    public function socialProfile(){

        return $this->hasMany('App\Models\SocialUserProfile','user_id');

    }

    public function followers(){

        return $this->belongsToMany('App\Models\User', 'user_follows', 'following_user_id', 'user_id');

    }

    public function devices(){

        return $this->hasMany('App\Models\Device','user_id')->orderBy('updated_at','desc');

    }

    public function blockedUsers(){

        return $this->hasMany('App\Models\BlockedUser','user_id');

    }

    public function momRecommendation(){
        return $this->hasMany('App\Models\MomRecommendation','user_id');
    }

    public function interestTags()
    {
        return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_user_mapping', 'user_id', 'tag_id');
    }

    public function interests() {

        return $this->belongsToMany('App\Models\InterestTag', 'follow_interest_tags', 'user_id', 'interest_tag_id')->where('follow_interest_tags.status', 1);
    }

    public function referral()
    {
        return $this->belongsTo('App\Models\BrandReferral', 'referral_id');
    }

    public function referredUser()
    {
        return $this->belongsTo('App\Models\User', 'refer_user_id');
    }

    public function brandReferral()
    {
        return $this->hasOne('App\Models\UserBrand', 'user_id');
    }

    public function opsAssignments(){
        return $this->hasMany('App\Models\OpsAssignment','user_id','id');
    }

    public function metric(){
         return $this->belongsToMany('App\Models\Metric', 'bc_user_mertics', 'user_id', 'metric_id');
    }

    public function opsAssignmentsCount(){
        return $this->opsAssignments()->selectRaw('user_id, count(*) as count')->groupBy('user_id');
    }

    public function user_contacts() {
        return $this->hasMany('App\Models\UserContacts', 'user_id');
    }

    public function brandReferrals(){
      return $this->morphToMany('App\Models\BrandReferral', 'brand_referral_taggable');
    }

    public function userReadBrandStory() {
        return DB::table("brand_story_users")->distinct('brand_stories.show_on')->join('brand_stories', 'brand_stories.id', '=', 'brand_story_users.brand_story_id')->where('brand_story_users.user_id', $this->id)->lists('brand_stories.show_on');
    }

    public function referralOfUser() {
        $referral_user = User::where('referral_code', $this->refer_user_code)->first();
        return $referral_user;
    }

    public function addReferralId($user, $referral_code) {
        // $referral = Referral::where('referral_code',$referral_code)->first();
        
        // if(isset($referral)) {
        //     $user = self::find($user->id);
        //     $user->referral_id = $referral->id;
        //     $user->save();
        //     $user_cache = new UserCache();
        //     $user_cache->delete($user, $user->id);
            
        //     return $referral->referral_name;;
        // } else {
        $user_code = preg_replace('/(q|w|x|y|z)$/', "", $referral_code);
        $refer_user_id = hexdec($user_code);
        if(isAppVersionGreaterThan('2.9.2.5') || request()->header('app-identifier') == 'ios_consumer') {
            $referral_user = User::where('id', '<>', $user->id)->where('referral_code', $referral_code)->first();
        }
        else {
            $referral_user = User::where('id', '<>', $user->id)->find($refer_user_id);
        }
        if($referral_user){
            $user->refer_user_id = $referral_user->id;
            $user->refer_user_code = $referral_code;
            $user->save();
            return [
                'referral_user_id' => $referral_user->id,
                'referral_first_name' => $referral_user->f_name
            ];
        }
        // }
        return false;
    }

    public function getNoreviewsAttribute()
    {
        // dd(count($this->reviews->filter(function($item){
        //             return $item->published == 1;
        //         })));
        return count($this->reviews->filter(function($item){
                    return $item->published == 1;
                }));
    }

    public function getProcessedgenderAttribute(){

        // dd($this->gender);
        if($this->gender == 1)
        {
            return "Male";
        }
        if($this->gender == 2)
        {
            return "Female";
        }
    }

    public function getLocationnameAttribute()
    {

        if($this->location != null)
        {
            return $this->location->location_name;
        }
        else
        {
            return '';
        }
    }

    public static function getUserById($id){

        return self::with('babies','category','reviews','wishlists','partner')->where('id', $id)->get()[0];
    }


    //Notifiable Functions

    public function isChannelSupported($channel){

        return true;
    }

    public function isChannelAllowed($channel){

        return true;
    }

    public function getEmailAddress(){

        return $this->email;
    }

    public function getMobileNumber(){

        if($this->mobile_number){
            return $this->mobile_number;
        }else{
            return null;
        }
    }

    public function getDevice($device_type){

        foreach($this->devices as $device){
            if($device->channel == $device_type){
                return $device;
            }
            return null;
        }
    }

    public function getUrl($type=Urlable::FULLURL,$params=[]){

        $url = user_url($this->id);
        switch($type){
            case Urlable::FULLURL: return web_url($url);
                            break;
            case Urlable::RELATIVEURL:  return $url;
                            break;
            default: return url($url);
                break;
        }

    }

    //end of Notifiable implementation
    public static function addPointsToUser($user_id,$points){

        DB::transaction(function() use ($user_id, $points){

            $old_points = User::where('id', $user_id)->lists('points')->get(0);
            $new_points = $old_points + $points;
            $points = User::where('id', $user_id)->update(['points' => $new_points ]);
        });

    }

    //start of pointsholder implementation
    public function addPoints($points){

        DB::transaction(function() use ($points){

            $old_points = User::where('id',$this->id)->lists('points')->get(0);
            $new_points = $old_points + $points;
            $user = User::where('id',$this->id)->update(['points' => $new_points ]);
            $this->points = $new_points;
        });

    }

    public function redeemPoints($points){

        DB::transaction(function() use ($points){

            $old_points = User::where('id',$this->id)->lists('points')->get(0);
            $new_points = $old_points - $points;
            if($new_points<0){
                throw new \Exception('Insufficient Points');
            }
            $user = User::where('id',$this->id)->update(['points' => $new_points ]);
            $this->points = $new_points;
        });

    }

    public function getPoints(){

        return $this->points;

    }

    //end of pointsholder implementation

    public function setProfileImageFromUrl($original_path){

            if(Util::imageExistsAtUrl($original_path)){

                     $image = new Image($original_path, Image::PROFILE_IMAGE,['url' => true]);

                     $s3_path = $this->getUniqueProfileImagePath();
                     $s3_thumb_path = join('/',[ config('aws.users_directory'),'thumb', $s3_path ]);
                     $s3_normal_path = join('/',[ config('aws.users_directory'),'normal', $s3_path ]);
                     $s3_original_path = join('/',[ config('aws.users_directory'),'original', $s3_path ]);

                     storeOnS3($s3_original_path, $image->getOriginalImageBlob());
                     storeOnS3($s3_normal_path, $image->getNormalImageBlob());
                     storeOnS3($s3_thumb_path, $image->getThumbImageBlob());

                     $this->avatar = $s3_path;
                     return true;
            }else{
                    return false;
            }

    }




    //Get list of momstars
    public static function getMomstars(){
        $momstars=User::where('momstars','=','yes')->select('id','name','email','momstars','avatar','lifestage')->get();
        return $momstars;
    }


    //returns a unique path to store the original file or the already set file...needs filename for extension
    public function getUniqueProfileImagePath($original_filepath=null){

        if(!$original_filepath){
            if($this->avatar){
                $pInfo = pathinfo($this->avatar);
            }
        } else {
            $pInfo = pathinfo($original_filepath);
        }
        if(isset($pInfo)){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('profile_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }

    private static function checkBlockList($email) {

        $blockUser = Redis::command('ZRANK', ["blocklist", $email]);

        if( is_null($blockUser) )
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    public static function makeDummyEmail($entity_id, $type){
        return "{$entity_id}-{$type}@babyc.in";
    }

    public static function doFacebookSignup($access_token,$options=[]) {

            $newUser = 0;

            try {
                //getting info from facebook
                Facebook::setDefaultAccessToken($access_token);
                //$graphUser = Facebook::get('/me?fields=id,first_name,last_name,gender,link,email,location')->getGraphUser();
                $graphUser = Facebook::get('/me?fields=id,first_name,last_name,email')->getGraphUser();  
            } catch (Exception $e) {
                logException($e, ['place' => 'Facebook Login']);
            }
            

            if($graphUser && isset($graphUser['email'])) {

                // Checking if user is blocked
                if(self::checkBlockList($graphUser['email']) )
                {
                    return ['user' => null, 'message' => 'You are blocked.', 'newUser' => $newUser];
                }

                $user = User::where('email','=', $graphUser['email'])->first();

                if(!$user && isset($options['user_id']) && $options['user_id']){
                    $user = User::find($options['user_id']);
                }

                // $logFile = storage_path().'/signupLog.txt';//this log file keeps the latest update date
                // file_put_contents($logFile, date('Y-m-d H:i:s')."\tSuccess\tuser:$user->id\t$access_token"."\n", FILE_APPEND);
            }
            else{
                $user = User::where('email','=', Self::makeDummyEmail($graphUser['id'], 'facebook') )->first();
                //$user = null;
            }
            // else {
            //     // $logFile = storage_path().'/signupLog.txt';//this log file keeps the latest update date
            //     // file_put_contents($logFile, date('Y-m-d H:i:s')."\tFail\t$access_token"."\n", FILE_APPEND);
            //     return ['user' => null, 'message' => 'Problem in getting Info From Facebook.', 'newUser' => $newUser];
            // }


            //checking if user in database


            if($user){

                $shouldUpdateUser = false;
                
                // if($user->quickblox_id == ''){
                //     // Quickblox sign up
                //     $QB = new Quickblox;
                //     $QBUser = $QB->signUp($user);
                //     if(isset($QBUser->user)){
                //             $user->quickblox_id = $QBUser->user->id;
                //             $shouldUpdateUser = true;
                //     }

                // }

                if(isset($options['source']) && strpos($user->uses, $options['source']) === false) {
                    $uses_arr = explode(',',$user->uses);
                    array_push($uses_arr, $options['source']);
                    $user->uses = implode(',',$uses_arr);
                    $shouldUpdateUser = true;
                    if ($options['source'] == 'android' and strpos($user->uses, 'web') !== false)
                    {
                        $newUser = 2;//this is the case when the user signs up on the app, but is already our web user.
                    }
                }

                // dd($graphUser);
                 $userFriend = UserFriend::where('user_id', '=',$user->id)
                                            ->where('provider','facebook')
                                            ->first();

                //user exists with this email - checking if he is registered with facebook
                 $facebookProvider = SocialUserProfile::where('user_id','=',$user->id)->where('provider','facebook')->first();

                 if($facebookProvider){

                    $facebookProvider->access_token = $access_token;
                    $facebookProvider->social_profile_uid = $graphUser['id'];
                    $facebookProvider->save();

                    //This goes to queue
                    if(!$userFriend)
                    {
                        $userData = [];
                        $userData['access_token'] = $access_token;
                        $userData['user_id'] = $user->id;
                        $userData['user_social_id'] = $graphUser['id'];
                        $userData['provider'] = 'facebook';
                        $userData['update_flag'] = true;


                        self::saveFriendsQueue($user, $userData);
                    }


                    if(isset($options['area_id'])&& $user->city_id==0){

                        $user->city_id = $options['area_id'];
                        $shouldUpdateUser = true;
                    }

                 } else {

                    $facebookProvider = new SocialUserProfile;
                    $facebookProvider->access_token = $access_token;
                    $facebookProvider->social_profile_uid = $graphUser['id'];
                    $facebookProvider->provider = 'facebook';
                    $facebookProvider->user_id = $user->id;
                    $facebookProvider->created_date =  date('Y-m-d H:i:s');
                    $facebookProvider->save();

                    $fb_profile_url = 'https://graph.facebook.com/'.$facebookProvider->social_profile_uid.'/picture?type=large';


                    $user->setProfileImageFromUrl($fb_profile_url);
                    $user->city_id = isset($options['area_id'])? $options['area_id']:$user->city_id;
                    $shouldUpdateUser = true;

                    //This goes to queue
                    if(!$userFriend)
                    {
                        $userData = [];
                        $userData['access_token'] = $access_token;
                        $userData['user_id'] = $user->id;
                        $userData['user_social_id'] = $graphUser['id'];
                        $userData['provider'] = 'facebook';
                        $userData['update_flag'] = true;

                        self::saveFriendsQueue($user, $userData);
                    }

                }

                if(isset($options['user_id']) && $options['user_id']){
                    $user->name  = $graphUser['first_name'].' '.$graphUser['last_name'];
                    $user->f_name = $graphUser['first_name'];
                    $user->l_name = $graphUser['last_name'];

                    if(isset($graphUser['gender']) && $graphUser['gender'] == 'male'){

                        $user->gender = '1';

                    } else {

                        $user->gender = '2';

                    }

                    $user->social_profile_link = getDefault($graphUser['link'], '');
                    $user->email = $user->email = (isset($graphUser['email'])) ? $graphUser['email'] : Self::makeDummyEmail($graphUser['id'], 'facebook') ;
                    $user->facebook_accesstoken = $access_token;
                    $fb_profile_url = 'https://graph.facebook.com/'.$graphUser['id'].'/picture?type=large';
                    $user->setProfileImageFromUrl($fb_profile_url);
                    $shouldUpdateUser = true;
                }

                if(isset($graphUser['verified_mobile_phone'])) {
                    $shouldUpdateUser = true;
                    $user->facebook_phone = $graphUser['verified_mobile_phone'];
                }

                if($shouldUpdateUser){
                    $user->save();
                }

                self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_LOGGED_IN]));
            } else {
                    // creating new user here
                    $user = new User;
                    $user->name  = $graphUser['first_name'].' '.$graphUser['last_name'];
                    $user->f_name = $graphUser['first_name'];
                    $user->l_name = $graphUser['last_name'];

                    if(isset($graphUser['gender']) && $graphUser['gender'] == 'male'){

                        $user->gender = '1';

                    } else {

                        $user->gender = '2';

                    }

                    $user->social_profile_link = '';//getDefault($graphUser['link'], '');
                    $user->email = (isset($graphUser['email'])) ? $graphUser['email'] : Self::makeDummyEmail($graphUser['id'], 'facebook') ;
                    $user->facebook_accesstoken = $access_token;
                    $user->city_id = isset($options['area_id'])?$options['area_id']:0;
                    $user->source_params = isset($options['source_params'])?$options['source_params']:'';
                    $user->lifestage = '';
                    $user->momstars = 'no';
                    $user->source = isset($options['source'])?$options['source']:'web';
                    $user->uses = isset($options['source'])?$options['source']:'web';
                    $user->lifestage_id = isset($options['lifestage_id'])?$options['lifestage_id']:null;
                    //$user->save();
                    $fb_profile_url = 'https://graph.facebook.com/'.$graphUser['id'].'/picture?type=large';

                    $user->setProfileImageFromUrl($fb_profile_url);
                    if(isset($graphUser['verified_mobile_phone'])) {
                        $user->facebook_phone = $graphUser['verified_mobile_phone'];
                    }

                    $user->provider = 'facebook';

                    // Quickblox sign up
                    // $QB = new Quickblox;
                    // $QBUser = $QB->signUp($user);
                    // if(isset($QBUser->user)){
                    //     $user->quickblox_id = $QBUser->user->id;
                    // }
                    // if(isset($options['referral'])) {
                    //     $user->refer_link = $options['referral']['refer_link'];
                    //     // $user->referrer_id = $options['referral']['referrer_id'];
                    // }
                    $user->http_referer = isset($_COOKIE['HTTP_REFERER']) ? $_COOKIE['HTTP_REFERER'] : '';
                    $user->login_from = request()->server('HTTP_REFERER') ? request()->server('HTTP_REFERER') : "";

                    // hack for solving user duplication
                    $existingUser = User::where('email', $user->email)->first();
                    if ($existingUser) {
                        $user = $existingUser;
                    }
                    $user->save();

                    $facebookProvider = new SocialUserProfile;
                    $facebookProvider->access_token = $access_token;
                    //TODO
                    $facebookProvider->social_profile_uid = $graphUser['id'];
                    $facebookProvider->provider = 'facebook';
                    $facebookProvider->user_id = $user->id;
                    $facebookProvider->created_date =  date('Y-m-d H:i:s');
                    $facebookProvider->save();

                    // Save babies Details
                    if( isset($options['baby_details']) )
                    {
                        if($options['baby_details']){
                            self::addBabyDetails($user->id, $options['baby_details']);
                        }
                    }

                    //This goes to queue
                    $userData = [];
                    $userData['access_token'] = $access_token;
                    $userData['user_id'] = $user->id;
                    $userData['user_social_id'] = $graphUser['id'];
                    $userData['provider'] = 'facebook';
                    $userData['update_flag'] = false;

                    self::saveFriendsQueue($user, $userData);
                    $newUser = 1;
                    self::setFirstTime(1);

                    $user->saveUserSource($user, $options);

                    // if(strpos($user->email, "@babyc.in") !== false) {
                    //     enqueueOnResque(config('queue.resque.queue.add_user_mail_chimp'), 'App\Jobs\Resque\AddUserToMailChimp', ['user_id' => $user->id]);
                    // }
                    // sending welcome email here
                    // $nc = new NotificationController;
                    // $nc->queueEmail($user, [ 'template' => 'emails.welcome', 'subject' =>  'Welcome to babychakra', 'data' => [ 'user' => $user]]);
                    self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_SIGNED_UP]));
            }


            //self::followFriendsQueue($user);

            // follow random Momstars
            return [ 'user' => $user, 'provider' => $facebookProvider, 'newUser'=>$newUser];
    }

    /**
     * Adds the Baby's details
     * @param integer $parentId
     * @param array   $babyDetails
     */
    public static function addBabyDetails($parentId, $babyDetails)
    {
        $baby = new Babies();
        $baby->parent_id = $parentId;
        $baby->birth_date = $babyDetails['birth_date'];
        $baby->gender    = $babyDetails['gender'];
        $baby->save();
        return true;
    }

    public static function saveFriendsQueue($user, $userSocialData){
        // save social friends
        $saveFriends = (new SaveFriends($userSocialData));
        $user->dispatch($saveFriends);

        return true;
    }

    public static function registerUserOnChat($user) {
        $sendBird = new SendBird();
        $sendBirdAdapter = new SendBirdAdapter($sendBird);
        $sendBirdAdapter->registerUser($user);
    }

    public static function onUserOnboardJob($user){
        if(!checkDummyUser($user) && !checkDummyEmail($user)){
            // enque welcome email
            enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['job_type' => OnUserOnboard::JOB_TYPE_WELCOME_EMAIL, 'user_id' => $user->id]);
            // enque welcome email 2
            enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['job_type' => OnUserOnboard::JOB_TYPE_WELCOME_EMAIL_DAY2, 'user_id' => $user->id], Carbon::now()->addDays(2)->timestamp);
            // enque momstar follows - DISABLED AFTER DISCUSSION IN GOOGLE LPA
            // enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['job_type' => OnUserOnboard::JOB_TYPE_FOLLOW_MOMSTARS, 'user_id' => $user->id]);
            // 
            self::registerUserOnChat($user);
            if($user->gender == 1) {
                enqueueOnResque('chat', 'App\Jobs\OnUserOnboard', ['job_type' => OnUserOnboard::JOB_TYPE_CHAT_GROUP_DAD, 'user_id' => $user->id]);
            }
        }

        // follow friends - two way
        // $onUserOnboardJob = new OnUserOnboard($user, OnUserOnboard::JOB_TYPE_WELCOME_NOTIF);
        // $onUserOnboardJob->delay(Carbon::now()->addMinutes(1));
        // dispatch($onUserOnboardJob); Carbon::now()->addMinutes(2)->timestamp
        // enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['user_id' => $user->id, 'job_type' => OnUserOnboard::JOB_TYPE_WELCOME_NOTIF ], Carbon::now()->addMinutes(15)->timestamp);
        enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['user_id' => $user->id, 'job_type' => OnUserOnboard::JOB_TYPE_FOLLOW_FRIENDS ], Carbon::now()->addMinutes(15)->timestamp);

        if(!isStringSet($user->lifestage_id)) {
            self::queueJobsToNotifyCompleteRegistration($user);
        }
        return true;
    }

    private static function queueJobsToNotifyCompleteRegistration($user) {

        enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['user_id' => $user->id, 'job_type' => OnUserOnboard::JOB_TYPE_COMPLETE_REGISTRATION_NOTIFY ], Carbon::now()->addMinutes(15)->timestamp);

        $plus2Hours = Carbon::now()->addHours(2);
        if(!isTimeBetweenRestrictedNotificationHours($plus2Hours)) {
            enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['user_id' => $user->id, 'job_type' => OnUserOnboard::JOB_TYPE_COMPLETE_REGISTRATION_NOTIFY ], $plus2Hours->timestamp);
        }

        $tomorrow = Carbon::now()->addDays(1);
        $tomorrowAt10 = $tomorrow->hour(10)->minute(0)->second(0);
        enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['user_id' => $user->id, 'job_type' => OnUserOnboard::JOB_TYPE_COMPLETE_REGISTRATION_NOTIFY ], $tomorrowAt10->timestamp);

        $tomorrowAt20 = $tomorrow->hour(20)->minute(0)->second(0);
        enqueueOnResque('user_onboard', 'App\Jobs\OnUserOnboard', ['user_id' => $user->id, 'job_type' => OnUserOnboard::JOB_TYPE_COMPLETE_REGISTRATION_NOTIFY ], $tomorrowAt20->timestamp);
    }

    /**
    * @param $user = user object to whom email is going to be sent
    * @param $follower = follower's object or array of followers object in case of multiple followers
    * @param $multipleFollower = set true when array of followers object sent
    */
    public static function notifyFollowerEmail($user, $follower, $multipleFollower = false){
        if($user && $follower && $user->expert == 0){
            $mail = array();
            $mail['to']             = $user->email;
            $mail['subject']        = 'You are getting popular at BabyChakra. Checkout why.';
            $mail['blade']          = 'emails.followedYou';
            $mail['bladeObject']    = [ 'user' =>$user, 'follower' => $follower, 'multipleFollower' => $multipleFollower];

            $email = (new QueueEmail($mail));
            $user->dispatch($email);
        }
    }

    public static function doDummyLoginForApp($device_id, $options = []){
        $newUser = 0;
        if(Self::where('email', Self::makeDummyEmail($device_id, 'device_id'))->exists()){
            $user = Self::where('email', Self::makeDummyEmail($device_id, 'device_id'))->first();
        }
        else{
            $user = new Self;
            $newUser = 1;
        }
        $user->name = "Babychakra User";
        $user->f_name = "Babychakra User";
        $user->l_name = "";
        $user->email = Self::makeDummyEmail($device_id, 'device_id');
        $user->save();

        $user = self::find($user->id);

        return [ 'user' => $user, 'provider' => null, 'newUser'=>$newUser];
    }

    public static function doTrueCallerLogin($access_token, $options = []) {
        $newUser = 0;
        
        $trueCallerProvider = [];
        $userDetailDecoded = base64_decode($access_token);
        $userDetails = json_decode($userDetailDecoded, true);

        if (!$userDetails) {

            return ['user' => null, 'message' => 'Wrong Access Token.', 'newUser' => $newUser];
        }

        if (isset($userDetails['email']) && !empty($userDetails['email'])) {

            if (self::checkBlockList($userDetails['email'])) {

                return ['user' => null, 'message' => 'You are blocked.', 'newUser' => $newUser];
            }
        }

        if (!isset($userDetails['phoneNumber'])) {
            return ['user' => null, 'message' => 'No Mobile Number Found.', 'newUser' => $newUser];
        }

        if (isset($userDetails['email'])) {
            $user = User::where('email', $userDetails['email'])->orWhere('mobile_number', format_number($userDetails['phoneNumber']))->first();
        }
        else {
            $user = User::where('mobile_number', format_number($userDetails['phoneNumber']))->first();
        }

        if ($user) {

            if(isset($options['source']) && strpos($user->uses, $options['source']) === false) {
                $uses_arr = explode(',',trim($user->uses,','));
                array_push($uses_arr, $options['source']);
                $user->uses = implode(',',$uses_arr);
                $shouldUpdateUser = true;
                if ($options['source'] == 'android' and strpos($user->uses, 'web') !== false)
                {
                    $newUser = 2;//this is the case when the user signs up on the app, but is already our web user.
                }
            }

            if(isset($options['referral']))
            {
                $user->refer_link  = $options['referral']['refer_link'];
            }

            $userName = trim(getDefault($userDetails['firstName'], '').' '.getDefault($userDetails['lastName'], ''));
            $user->name  = $userName;
            $user->f_name =  getDefault($userDetails['firstName'], '');
            $user->l_name =getDefault($userDetails['lastName'], '');
            $user->username = $userName;
            if ($user->email == '' || trim($user->email) == '') {
                $user->email = getDefault($userDetails['email'], '');
            }

            $user->mobile_number = format_number($userDetails['phoneNumber']);
            $user->provider = 'truecaller';
            $user->mobile_verified = 1;
            $user->save();
            self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_LOGGED_IN]));
        }
        else {
            $userName = trim(getDefault($userDetails['firstName'], '').' '.getDefault($userDetails['lastName'], ''));
            $user = new User;
            $user->name  = $userName;
            $user->f_name =  getDefault($userDetails['firstName'], '');
            $user->l_name =getDefault($userDetails['lastName'], '');
            $user->username = $userName;
            $user->email = getDefault($userDetails['email'], '');
            $user->mobile_number = format_number($userDetails['phoneNumber']);

            $user->lifestage='';
            $user->momstars='no';
            $user->city_id = isset($options['area_id'])?$options['area_id']:0;
            $user->source = isset($options['source'])?$options['source']:'web';
            $user->uses = isset($options['source'])?$options['source']:'web';
            $user->source_params = isset($options['source_params'])?$options['source_params']:'';
            $user->lifestage_id = isset($options['lifestage_id'])?$options['lifestage_id']:null;
            $user->provider = 'truecaller';
            $user->mobile_verified = 1;

            if(isset($options['referral']))
            {
                $user->refer_link  = $options['referral']['refer_link'];
            }

            $user->http_referer = isset($_COOKIE['HTTP_REFERER']) ? $_COOKIE['HTTP_REFERER'] : '';
            $user->login_from = request()->server('HTTP_REFERER') ? request()->server('HTTP_REFERER') : "";

            // hack for solving user duplication
            $existingUser = User::where('mobile_number', $user->mobile_number)->where('mobile_verified', 1)->first();
            if ($existingUser) {
                $user = $existingUser;
            }

            $user->save();

            $trueCallerProvider = new SocialUserProfile;
            $trueCallerProvider->access_token = "";
            $trueCallerProvider->social_profile_uid = '';
            $trueCallerProvider->provider = 'truecaller';
            $trueCallerProvider->user_id = $user->id;
            $trueCallerProvider->created_date =  date('Y-m-d H:i:s');
            $trueCallerProvider->save();

            // Save babies Details
            if( isset($options['baby_details']) )
            {
                if($options['baby_details']){
                    self::addBabyDetails($user->id, $options['baby_details']);
                }
            }

            $newUser = 1;
            self::setFirstTime(1);

            $user->saveUserSource($user, $options);

            self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_SIGNED_UP]));
        }

        return [ 'user' => $user, 'provider' => $trueCallerProvider, 'newUser'=>$newUser];
    }

    public static function doGoogleLoginForMobile($access_token,$options = []){

        $newUser = 0;
        //getting info from facebook
        $googleUserDetails = SocialUserProfile::getGoogleDetailsFromAccessToken($access_token);
        
        //dd($googleUserDetails['sub']);
        //checking if user exists already
        if($googleUserDetails && isset($googleUserDetails['email']))
        {
            // Checking if user is blocked
            if( self::checkBlockList($googleUserDetails['email']) )
            {
                return ['user' => null, 'message' => 'You are blocked.', 'newUser' => $newUser];
            }

            $user = User::where('email','=',$googleUserDetails['email'])->first();
            if(!$user && isset($options['user_id']) && $options['user_id']){
                $user = User::find($options['user_id']);
            }

        }
        else
        {
            return ['user' => null, 'message' => 'Problem in getting Info From Google.', 'newUser' => $newUser];
        }

        if (env('LOGIN_FLOW_LOG', false)) {
            $param = [
                'info' => [
                    'text' => "login - google", 
                    'more' => [
                        'file' => "user.php",
                        'path' => "checked user exists",
                        'user' => ($user) ? $user->toArray() : [],
                    ]
                ]
            ];
            logRequest($param, 'login');
        }

        if($user)
        {

            // if($user->quickblox_id == '') {
            //         // Quickblox sign up
            //         $QB = new Quickblox;
            //         $QBUser = $QB->signUp($user);
            //         if(isset($QBUser->user)){
            //             $user->quickblox_id = $QBUser->user->id;
            //         }
            // }
            
            if(isset($options['source']) && strpos($user->uses, $options['source']) === false) {
                $uses_arr = explode(',',trim($user->uses,','));
                array_push($uses_arr, $options['source']);
                $user->uses = implode(',',$uses_arr);
                $shouldUpdateUser = true;
                if ($options['source'] == 'android' and strpos($user->uses, 'web') !== false)
                {
                    $newUser = 2;//this is the case when the user signs up on the app, but is already our web user.
                }
            }

            if(isset($options['referral']))
            {
                $user->refer_link  = $options['referral']['refer_link'];
                // $user->referrer_id = $options['referral']['referrer_id'];
            }
            //user exists - Checking if google plus login exists
            $googleProvider = SocialUserProfile::where('user_id','=',$user->id)->where('provider','google')->first();

            $userFriend = UserFriend::where('user_id', '=',$user->id)
                                    ->where('provider','google')
                                    ->first();

            if($googleProvider)
            {

                //google Provider Exists
                $googleProvider->access_token = $access_token;
                $googleProvider->save();

                if(isset($options['area_id'])&& $user->city_id==0)
                {
                    $user->city_id = $options['area_id'];
                }

                // Update only if Image does not exist
                if(!$user->avatar)
                {
                    $user->setProfileImageFromUrl(strtok($googleUserDetails['picture'], '?'));
                }


                //This goes to queue
                if(!$userFriend)
                {
                    $userData = [];
                    $userData['access_token'] = $access_token;
                    $userData['user_id'] = $user->id;
                    $userData['user_social_id'] = $googleUserDetails['sub'];
                    $userData['provider'] = 'google';
                    $userData['update_flag'] = true;

                    self::saveFriendsQueue($user, $userData);
                }
            }
            else
            {

                //google Provider Details
                $googleProvider = new SocialUserProfile;
                $googleProvider->access_token = $access_token;
                $googleProvider->social_profile_uid = $googleUserDetails['sub'];
                $googleProvider->provider = 'google';
                $googleProvider->user_id = $user->id;
                $googleProvider->created_date =  date('Y-m-d H:i:s');
                $googleProvider->save();

                //google Image Save
                $user->city_id = isset($options['area_id'])? $options['area_id']:$user->city_id;
                $user->setProfileImageFromUrl(strtok($googleUserDetails['picture'], '?'));


                //This goes to queue
                if(!$userFriend)
                {
                    $userData = [];
                    $userData['access_token'] = $access_token;
                    $userData['user_id'] = $user->id;
                    $userData['user_social_id'] = $googleUserDetails['sub'];
                    $userData['provider'] = 'google';
                    $userData['update_flag'] = true;

                    self::saveFriendsQueue($user, $userData);
                }
            }

            if(isset($options['user_id']) && $options['user_id']){
                $user->name  = isset($googleUserDetails['name']) ? $googleUserDetails['name']:'';
                $user->f_name =  isset($googleUserDetails['given_name']) ? $googleUserDetails['given_name']:'';
                $user->l_name =isset($googleUserDetails['family_name']) ? $googleUserDetails['family_name']:'';
                $user->username = isset($googleUserDetails['name']) ? $googleUserDetails['name']:'';
                $user->email = $googleUserDetails['email'];
            }

            // if(isset($googleUserDetails['gender']) && $googleUserDetails['gender'] == 'male')
            // {
            //     $user->gender = '1';
            // }
            // else
            // {
            //     $user->gender = '2';
            // }

            if (env('LOGIN_FLOW_LOG', false)) {
                $param = [
                    'info' => [
                        'text' => "login - google", 
                        'more' => [
                            'file' => "user.php",
                            'path' => "existing user flow - before saving",
                            'user' => ($user) ? $user->toArray() : [],
                        ]
                    ]
                ];
                logRequest($param, 'login');
            }
            $user->save();
            if (env('LOGIN_FLOW_LOG', false)) {
                $param = [
                    'info' => [
                        'text' => "login - google", 
                        'more' => [
                            'file' => "user.php",
                            'path' => "existing user flow - after saving",
                            'user' => ($user) ? $user->toArray() : [],
                        ]
                    ]
                ];
                logRequest($param, 'login');
            }
            self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_LOGGED_IN]));
        }
        else
        {
            // creating new user here
            //making user object
            $user = new User;
            $user->name  = isset($googleUserDetails['name']) ? $googleUserDetails['name']:'';
            $user->f_name =  isset($googleUserDetails['given_name']) ? $googleUserDetails['given_name']:'';
            $user->l_name =isset($googleUserDetails['family_name']) ? $googleUserDetails['family_name']:'';
            $user->username = isset($googleUserDetails['name']) ? $googleUserDetails['name']:'';

            if(isset($googleUserDetails['gender']) && $googleUserDetails['gender'] == 'male')
            {
                $user->gender = '1';
            }
            else
            {
                $user->gender = '2';
            }

            $user->social_profile_link = isset($googleUserDetails['profile']) ? $googleUserDetails['profile']:'';
            $user->email = $googleUserDetails['email'];

            $user->lifestage='';
            $user->momstars='no';
            $user->city_id = isset($options['area_id'])?$options['area_id']:0;
            $user->source = isset($options['source'])?$options['source']:'web';
            $user->uses = isset($options['source'])?$options['source']:'web';
            $user->source_params = isset($options['source_params'])?$options['source_params']:'';
            $user->lifestage_id = isset($options['lifestage_id'])?$options['lifestage_id']:null;

            $user->provider = 'google';
            // Quickblox sign up
            // $QB = new Quickblox;
            // $QBUser = $QB->signUp($user);
            // if(isset($QBUser->user)){
            //     $user->quickblox_id = $QBUser->user->id;
            // }

            if(isset($options['referral']))
            {
                $user->refer_link  = $options['referral']['refer_link'];
                // $user->referrer_id = $options['referral']['referrer_id'];
            }
            $user->http_referer = isset($_COOKIE['HTTP_REFERER']) ? $_COOKIE['HTTP_REFERER'] : '';
            $user->login_from = request()->server('HTTP_REFERER') ? request()->server('HTTP_REFERER') : "";
            //google Image Save
            // $user->save();

            $user->setProfileImageFromUrl(strtok($googleUserDetails['picture'], '?'));

            // hack for solving user duplication
            $existingUser = User::where('email', $user->email)->first();
            if ($existingUser) {
                $user = $existingUser;
            }

            if (env('LOGIN_FLOW_LOG', false)) {
                $param = [
                    'info' => [
                        'text' => "login - google", 
                        'more' => [
                            'file' => "user.php",
                            'path' => "new user flow - before saving",
                            'user' => ($user) ? $user->toArray() : [],
                        ]
                    ]
                ];
                logRequest($param, 'login');
            }
            $user->save();

            if (env('LOGIN_FLOW_LOG', false)) {
                $param = [
                    'info' => [
                        'text' => "login - google", 
                        'more' => [
                            'file' => "user.php",
                            'path' => "new user flow - after saving",
                            'user' => ($user) ? $user->toArray() : [],
                        ]
                    ]
                ];
                logRequest($param, 'login');
            }

            //making entry for Google Social Profile
            $googleProvider = new SocialUserProfile;
            $googleProvider->access_token = $access_token;
            $googleProvider->social_profile_uid = $googleUserDetails['sub'];
            $googleProvider->provider = 'google';
            $googleProvider->user_id = $user->id;
            $googleProvider->created_date =  date('Y-m-d H:i:s');
            $googleProvider->save();

            // Save babies Details
            if( isset($options['baby_details']) )
            {
                if($options['baby_details']){
                    self::addBabyDetails($user->id, $options['baby_details']);
                }
            }

            //This goes to queue

            $userData = [];
            $userData['access_token'] = $access_token;
            $userData['user_id'] = $user->id;
            $userData['user_social_id'] = $googleUserDetails['sub'];
            $userData['provider'] = 'google';
            $userData['update_flag'] = false;

            self::saveFriendsQueue($user, $userData);

            $newUser = 1;
            self::setFirstTime(1);

            $user->saveUserSource($user, $options);

            // enqueueOnResque(config('queue.resque.queue.add_user_mail_chimp'), 'App\Jobs\Resque\AddUserToMailChimp', ['user_id' => $user->id]);

            self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_SIGNED_UP]));
            // send welcome email
            // $nc = new NotificationController;
            // $nc->queueEmail($user, [ 'template' => 'emails.welcome', 'subject' =>  'Welcome to babychakra', 'data' => [ 'user' => $user]]);
        }
        //self::followFriendsQueue($user);
        return [ 'user' => $user, 'provider' => $googleProvider, 'newUser'=>$newUser];
    }

    public static function doPhoneLogin($mobileNumber,$options = []){
        $newUser = 0;

        $accountKitProvider = [];
        $mobileNumber = format_number($mobileNumber);
        $user = self::where('mobile_number', $mobileNumber)->where('mobile_verified', 1)->first();
        if($user)
        {
            
            if(isset($options['source']) && strpos($user->uses, $options['source']) === false) {
                $uses_arr = explode(',',trim($user->uses,','));
                array_push($uses_arr, $options['source']);
                $user->uses = implode(',',$uses_arr);
                $shouldUpdateUser = true;
                if ($options['source'] == 'android' and strpos($user->uses, 'web') !== false)
                {
                    $newUser = 2;//this is the case when the user signs up on the app, but is already our web user.
                }
            }

            if(isset($options['referral']))
            {
                $user->refer_link  = $options['referral']['refer_link'];
            }

            if(isset($options['area_id'])&& $user->city_id==0)
            {
                $user->city_id = $options['area_id'];
            }

            if(isset($options['name']) && !empty($options['name'])) {
                $user->name = $options['name'];
            }
            $user->mobile_verified = 1;
            $user->provider = 'phone_number';
            $user->save();
            self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_LOGGED_IN]));
        }
        else
        {
            // creating new user here
            //making user object
            $user = new User;

            $user->name = isset($options['name']) ? $options['name'] : '';
            $user->mobile_number = $mobileNumber;
            $user->lifestage='';
            $user->momstars='no';
            $user->city_id = isset($options['area_id'])?$options['area_id']:0;
            $user->source = isset($options['source'])?$options['source']:'web';
            $user->uses = isset($options['source'])?$options['source']:'web';
            $user->source_params = isset($options['source_params'])?$options['source_params']:'';
            $user->lifestage_id = isset($options['lifestage_id'])?$options['lifestage_id']:null;
            $user->provider = 'phone_number';
            $user->city = "";
            $user->about = "";
            $user->mobile_verified = 1;

            if(isset($options['referral']))
            {
                $user->refer_link  = $options['referral']['refer_link'];
            }
            $user->http_referer = isset($_COOKIE['HTTP_REFERER']) ? $_COOKIE['HTTP_REFERER'] : '';
            $user->login_from = request()->server('HTTP_REFERER') ? request()->server('HTTP_REFERER') : "";


            // hack for solving user duplication
            $existingUser = User::where('mobile_number', $user->mobile_number)->where('mobile_verified', 1)->first();
            if ($existingUser) {
                $user = $existingUser;
            }
            $user->save();

            //making entry for Google Social Profile
            $accountKitProvider = new SocialUserProfile;
            $accountKitProvider->access_token = "";
            $accountKitProvider->social_profile_uid = $options['id'];
            $accountKitProvider->provider = 'accountkit';
            $accountKitProvider->user_id = $user->id;
            $accountKitProvider->created_date =  date('Y-m-d H:i:s');
            $accountKitProvider->save();
            
            // Save babies Details
            if( isset($options['baby_details']) )
            {
                if($options['baby_details']){
                    self::addBabyDetails($user->id, $options['baby_details']);
                }
            }

            $newUser = 1;
            self::setFirstTime(1);

            $user->saveUserSource($user, $options);
            //enqueueOnResque(config('queue.resque.queue.add_user_mail_chimp'), 'App\Jobs\Resque\AddUserToMailChimp', ['user_id' => $user->id]);

            self::trackUserLogin($user, array_merge($options, ['action' => AppOpenLog::ACTION_SIGNED_UP]));
        }
        // dd("why");
        return [ 'user' => $user, 'provider' => $accountKitProvider, 'newUser'=>$newUser];
    }

    //Vegeta
    public static function getIndexableUsers ()
    {
        //return all the articles which satisfy the SQL query.

        $data   = User::with('socialProfile','location');
        return $data;

    }

    //Vegeta
    public static function getUserBestCityID ($user)
    {
        if ($user->last_visit_city)
        {
            $city = City::getCityFromName($user->last_visit_city);
            if (!$city)
                return '1';
            elseif ($city->parent_id != 0)
                return $city->parent_id;
            else
                return $city->id;
        }
        elseif ($user->city)
        {
            $city = City::getCityFromName($user->city);
            if (!$city)
                return '1';
            elseif ($city->parent_id != 0)
                return $city->parent_id;
            else
                return $city->id;
        }
        else
            return '1';
    }

    public function syncUserToQuickBloxServer(){

        $QB = new Quickblox;

        $users = User::orderBy('id')->take(20)->get();

        $data = [];

        foreach($users as $user){

            //emptying all users quickblox id and update user
            $user->quickblox_id = '';
            $user->save();

            //signing up user to quickblox
            $response = $QB->syncQuickBloxId($user);

            if(isset($response->user))
            {
                //setting new quickblox id and update user
                $user->quickblox_id = $response->user->id;
                $user->save();
                $data[] = $response->user;
            }
        }

        return $data;
    }

    public static function setFirstTime($value)
    {
        self::$firstTime = $value;
    }

    public static function getFirstTime()
    {
        return self::$firstTime;
    }

    public static function trackUserLogin($user, $option = null){
        
        $appOpenLog = new AppOpenLog();
        $appOpenLog->user_id = $user->id;
        $appOpenLog->channel = (isset($option['source'])) ? $option['source'] : "web" ;
        $appOpenLog->action  = $option['action'];
        $appOpenLog->app_version  = (request()->header('app-version')) ? request()->header('app-version') : "";
        $appOpenLog->save();

        return;
    }

    public function saveUserSource($user, $option = null){

        $userSource = new UserSource;
        $userSource->user_id        = $user->id;
        $userSource->uses           = (isset($option['source'])) ? $option['source'] : "web" ;
        $userSource->login_url      = (isset($option['source_params'])) ? $option['source_params'] : "" ;
        $userSource->source_url     = (Request::session()->has(User::TRACKING_SOURCE_URL)) ? Request::session()->get(User::TRACKING_SOURCE_URL) : "" ;
        $userSource->type           = (Request::session()->has(User::TRACKING_TYPE)) ? Request::session()->get(User::TRACKING_TYPE) : "unpaid" ;
        $userSource->utm_source     = (Request::session()->has(User::TRACKING_UTM_SOURCE)) ? Request::session()->get(User::TRACKING_UTM_SOURCE) : "" ;
        $userSource->utm_medium     = (Request::session()->has(User::TRACKING_UTM_MEDIUM)) ? Request::session()->get(User::TRACKING_UTM_MEDIUM) : "" ;
        $userSource->utm_campaign   = (Request::session()->has(User::TRACKING_UTM_CAMPAIGN)) ? Request::session()->get(User::TRACKING_UTM_CAMPAIGN) : "" ;
        $userSource->utm_keywords   = (Request::session()->has(User::TRACKING_UTM_KEYWORDS)) ? Request::session()->get(User::TRACKING_UTM_KEYWORDS) : "" ;

        $userSource->save();

        return;
    }

    public function babiesAge($dob = null, $timestamp = null){
        if(!$timestamp) {
            $timestamp = time();
        }
        
        if($dob == "0000-00-00"){
                return [];
        }
        $gender = "k";
        if($dob){

            $baby_birth_date = $dob;
        }
        else{

            if(isset($this->babies[0])) {
                if($this->babies[0]->birth_date == "0000-00-00") {
                    $this->babies[0]->birth_date = date('Y-m-d');
                }
                $baby_birth_date = $this->babies[0]->birth_date;
                $gender = $this->babies[0]->gender;
            }
            else {
                $baby_birth_date = date('Y-m-d');
            }
        }

        $baby_birth_date = strtotime($baby_birth_date);
        $time_diff = $baby_birth_date - $timestamp;
        $time_diff = ($time_diff < 0) ? $timestamp - $baby_birth_date : $time_diff ;
        $days_diff = (int)floor($time_diff / 86400); // 86400 = 1 day * 60 secs
        $week_diff = (int)floor($time_diff / 604800); // 604800 = 1 week * 60 secs

        if($baby_birth_date > $timestamp){
            // expecting
            $week_diff = ceil(38 - $week_diff);
            $age = ($week_diff == 1) ? "$week_diff week" : "$week_diff weeks";
            $ageNewFormat = trans('messages.week_text')." $week_diff";
            $ageHindi = "$week_diff सप्ताह";
            $raw_age = "$week_diff:week";
            $status = "pregnant";
            $days_diff = (int)"-$days_diff";
        }
        else{
            // parent
            if($week_diff > 3){
                $month_diff = floor($week_diff / 4);
                if($month_diff >= 12){
                    $year_diff = floor($month_diff / 12);
                    $age = ($year_diff == 1) ? "$year_diff year" : "$year_diff years";
                    $ageNewFormat = trans('messages.year_text')." $year_diff";
                    $ageHindi = "$year_diff साल";
                }
                else{
                    $age = ($month_diff == 1) ? "$month_diff month" : "$month_diff months";
                    $ageNewFormat = trans('messages.month_text')." $month_diff";
                    $ageHindi = "$month_diff महीने";
                }
                $raw_age = "$month_diff:month";
            }
            else{
                $age = "$week_diff weeks";
                $ageNewFormat = trans('messages.week_text')." $week_diff";
                $ageHindi = "$week_diff सप्ताह";
                $raw_age = "$week_diff:week";
            }
            $status = "parent";
        }
        if($gender == 'm') {
            $gender = "son";
        } elseif($gender == 'f') {
            $gender = "daughter";
        }
        elseif($status == 'pregnant') {
            $gender = "mom";
        }
        else {
            $gender = "kid";
        }

        return ['age' => $age,
                'age_new_format' => $ageNewFormat,
                'age_hindi' => $ageHindi,
                'raw' => $raw_age,
                'raw_days' => $days_diff,
                'raw_weeks' => "$week_diff",
                'status' => $status,
                'text' => ($status == 'parent') ? $age." old baby" : $age." pregnant",
                'gender' => $gender
            ];

    }

    public function personalizedCardData(Self $user, $app_version, $app_identifier){

        // template decision logic here
        if($user->user_lifestage){
            if(request()->api_version && (version_compare(request()->api_version, '2.3.1.1') > 0)) {
                $template_name = 'generic_message-'.$user->user_lifestage->seo_url."-above-2-3-1";
            }
            else {
                $template_name = 'generic_message-'.$user->user_lifestage->seo_url;
            }
        }
        else{
            $template_name = 'generic_message';
        }

        $userCache = new UserCache;
        $authUser = $userCache->fetch($user->id);

        $template = PersonalizedCardTemplate::where('template_type', $template_name)->first();

        if(!$template){
            $template = PersonalizedCardTemplate::where('template_type', 'generic_message')->first();
        }

        $currentDateTime = date("Y-m-d H:i:s");
        $banners = collect();
        $show_message = "1";
        $activeBanner = null;
        if($app_identifier != 'ios_consumer'){

            $promotionRepo = new PromotionRepository();
            $banners = $promotionRepo->getHomeBanners();
            
            if($banners){
                $show_message = "0";
            }

            $activeBanner = $promotionRepo->filterHomeBanner($banners, $authUser);
        }

        if($activeBanner)
        {
            $bannerData = json_decode($activeBanner->metadata, true);
            if(count($bannerData) > 0 && isset($bannerData['home_banner_deeplink']) && isset($bannerData['home_banner_text_color']) && isset($bannerData['home_banner_text_color']) && isset($bannerData['home_banner_image'])) {
                $image = $bannerData['home_banner_image'];
                $deeplink = $bannerData['home_banner_deeplink'];
                $color = $bannerData['home_banner_text_color'];
                $text = "<p><font color=".$color."><medium><i>Hey ".ucwords($this->f_name)."!</i></medium></font><br/></p>";

                if(request()->api_version && (version_compare(request()->api_version, '2.3.1.1') > 0)) {
                    if(isset($bannerData['home_banner_image_3_1']) && !empty($bannerData['home_banner_image_3_1'])) {
                        $image = $bannerData['home_banner_image_3_1'];
                    }
                }
            }
        }
        else
        {
            if(checkDummyUser($user)){
                $text = ($this->babiesAge()['age']) ? templateTranslator("<p><medium><i>Hey :name!</i><br>Welcome!</medium></p>", ['name' => ucwords($this->f_name), 'babies_age' => $this->babiesAge()['age'] ]) : '';
            }
            else{
                $text = ($this->babiesAge()['age']) ? templateTranslator($template->personalized_message, ['name' => ucwords($this->f_name), 'babies_age' => $this->babiesAge()['age'] ]) : '';
            }
            $image = $template->image_url;
            $deeplink = "";
            $image3x1 = $image;
        }

        $daily_tip_visible = 1;
        if(request()->api_version && version_compare('2.9.2', request()->api_version, '<=')){
            $daily_tip_visible = 0;
        }

        return [
            "image_url" => $image,
            "personalized_message" => $text,
            "CTA_text" => $template->cta_text,
            "CTA_normal_bg" => $template->cta_normal_bg,
            "CTA_normal_fg" => '#ffffff',
            "CTA_pressed_bg" => $template->cta_pressed_bg,
            "active" => $app_identifier == 'ios_consumer' ? "0" : env('SHOW_BANNER', "0"),  //10th May 2018 : dont show banner in ios ANU
            "cta_active" => $daily_tip_visible,
            "personalized_message_active" => $show_message,
            "banner_image_deeplink" => $deeplink,
        ];
    }

    public function isBabychakraUser(){
        if(strpos($this->email, '@babychakra.com')){
            return true;
        }
        return false;
    }

    public function updateLifestage($lifestageDetails)
    {
        // dd($this->lifestage_id);
        if(!isset($lifestageDetails['lifestage_id']))
            return false;

        $childBirthDate = "";
        // toddler = 5
        // newparent = 4
        // expecting-a-baby = 6
        // planning-a-baby = 7
        $lifestageId = $lifestageDetails['lifestage_id'];
        if ($lifestageId == 5 || $lifestageId == 4){

            if(!isset($lifestageDetails['born_day']) && !isset($lifestageDetails['born_month']) && !isset($lifestageDetails['born_year']))
                return false;
            $childBirthDate = $lifestageDetails['born_year'].'-'.$lifestageDetails['born_month'].'-'.$lifestageDetails['born_day'];
            // if DOB more than 18 months ago set toddler
            if(strtotime($childBirthDate) < strtotime("-18 months")){
                $lifestageId = 5; // toddler
            }
            else{
                $lifestageId = 4; // new parent
            }
        }
        elseif ($lifestageId == 6){
            if(!isset($lifestageDetails['pregnant_week']))
                return false;

            $lifestage_pregnancy_week = $lifestageDetails['pregnant_week'];
            $weekDiff = abs($lifestage_pregnancy_week - 38);
            $childBirthDate = date("Y-m-d", strtotime("+$weekDiff week"));
        }
        // elseif ($lifestageId == 7)
        // {
        //     if(isset($lifestageDetails['planning-month']))
        //     {
        //         $planningMonth = $lifestageDetails['planning-month'] + 9;
        //         $childBirthDate = date("Y-m-d", strtotime("+{$planningMonth} months"));
        //     }
        // }

        if(isset($lifestageDetails['name']) && !empty(trim($lifestageDetails['name']))) {

            $this->name = $lifestageDetails['name'];
        }
        $this->lifestage_id = $lifestageId;
        $this->save();

        $babyGender = (isset($lifestageDetails['baby_gender'])) ? $lifestageDetails['baby_gender'] : "";
        $baby = Babies::where('parent_id','=',$this->id)->first();
        if(!$baby)
        {
            $baby = new Babies();
        }
        $baby->birth_date = $childBirthDate;
        if($babyGender != null){
            $baby->gender = $babyGender;
        }
        $baby->parent_id = $this->id;
        $baby->save();
    }


    public function getUniqueUserCode() {
        return "BABYCH ".$this->id.uniqid();
    }

    public function getBackgroundImageAndColor() {
        $background_color = "";
        $background_image = "";
        $background_text = "";

        if(isset($this->brandReferral->brandReferral)) {
            $background_image = userreferralimage($this->brandReferral->brandReferral->bg_image);
            $background_color = ($this->brandReferral->brandReferral->bg_color) ? $this->brandReferral->brandReferral->bg_color : "";
        }

        if($this->momstars == "yes") {
            $background_image = "";
            $background_color = config('referral.momstar_color');
            $background_text = config('referral.momstar_text');
        }
        if($this->expert != 0) {
            $background_image = "";
            $background_color = config('referral.expert_color');
            $background_text = config('referral.expert_text');
        }

        $badge_name = getUsersBadge($this->id);
        if($badge_name != '') {
            $background_color = config('referral.'.$badge_name.'_color');
            $background_text = config('referral.'.$badge_name.'_text');
        }

        return [
            "background_image" => $background_image,
            "background_color" => $background_color,
            "background_text"  => $background_text
        ];
    }

    public function inviteURL() {
        return short_url("/ai/$this->referral_code");
        // $hex_id = dechex($this->id);
        // return short_url("/ai/$hex_id");
    }

    public function referralCode() {
        if(!isStringSet($this->referral_code)) {
            $search = [0, 1];
            $replace = ['y', 'z'];
            $codePrefix = 'bc';

            if($this->name == utf8_decode($this->name)) {
                $codePrefix = substr($this->name, 0, 2);
            }

            $this->referral_code = $codePrefix.str_replace($search, $replace, dechex($this->id));
            $this->save();
        }
        return $this->referral_code;
        //old method of user referral code {hex of userid}
        // $hex_id = dechex($this->id); 
        // return str_pad($hex_id, 5, "qwxyz");
    }

    public function languages() {
        return $this->belongsToMany('App\Models\Language', 'language_entity_mappings', 'entity_id', 'language_id')->where('entity_type', 'user');
    }

    public static function getAdminUsers(){
        $users = User::select('id')->whereIn('id', config('admin.admin_users'))->get();

        $arr = [];
        foreach($users as $user){
            $arr[$user->id] = $user->name;
        }

        return $arr;
    }
}
