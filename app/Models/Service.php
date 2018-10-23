<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;
use DB;
use Slugify;
use App\Models\User;
use App\Models\Feedpost;
// use App\Helpers\Quickblox;
use App\Interfaces\INotifiable as Notifiable;
use App\Interfaces\IUrlable as Urlable;
use Storage;

class Service extends SleepingOwlModel implements Notifiable{

     private $rules = array(
        'name' => 'Required',
        'email'  => 'Email',
        'pincode' => 'Integer',
        'contact' => 'Integer',
        'mobile_number' => 'Integer|Min:3|Max:80'
    );

    const CHAT_INTEGRATION = 'chat';
    const TRANSACTION_INTEGRATION = 'trans';
    const LYBRATE_INTEGRATION = 'lybrate';

    const DEFAULT_IMAGE='default-image.png';
    const BUCKET_URL='http://s3-ap-southeast-1.amazonaws.com/babychakraserviceproviders/serviceproviders/';
    const EXOTEL_NUMBER='02233814064';//For Mumbai

    const SERVICES_PUBLISHED_TODAY = 'services_published_today';

    const TYPE_SERVICE = 'service';
    const TYPE_PRODUCT = 'product';

    public function getValidationRules(){
        return $this->rules;
    }

    protected $table = 'bc_services_providers_new';

    protected $hidden = [ 'image1', 'image2','image3', 'image4', 'image5','image6','other_services','created_on','admin_push'];

    public $timestamps = false;

    //Relationship between service providers and their subcategories
    public function service_bank_detail()
    {
        return $this->hasOne('App\Models\ServiceBankDetail','service_id','id');
    }

    public function serviceTest(){
        return $this->response->array(array('message'=>"This is a service Test"));
    }


    //Relationship between service providers and their subcategories
    public function category()
    {
        return $this->hasMany('App\Models\Service_category','service_provider_id','id');
    }

    //Relationship between service providers and their subcategories
    public function priceCategory()
    {
        return $this->hasMany('App\Models\ServicePackage','service_provider_id','id');
    }

    public function activePackages()
    {

        return $this->hasMany('App\Models\Package','service_id')->where('packages.status', 2)->orderBy('sort_order');
    }

    public function packages()
    {

        return $this->hasMany('App\Models\Package','service_id');
    }

    //Relationship between service and it's calls
    public function exotel_calls()
    {
        return $this->hasMany('App\Models\ExotelManager','digits','exotel_digits');
    }

    public function subcategories()
    {
        // return $this->belongsToMany('App\Models\ServiceCategory', 'service_provider_category_mapping_new', 'service_provider_id', 'category_id')->where('parent_id','!=',0);

        return $this->belongsToMany('App\Models\ServiceCategory', 'service_provider_category_mapping_new', 'service_provider_id', 'category_id')
                    ->withPivot('age_group_max','age_group_min','cord_blood_bank','day_care_facility','transportation','air_conditioner',
                                'home_blood_tests','cafe_reading_room','kids_special_menu','children_entertainment_option','kiddie_cutlery','kids_friendly_chairs_tools',
                                'english_speaking_nany');


    }

    public function images(){

        return $this->hasMany('App\Models\ServiceImage', 'service_provider_id');
    }

    public function brandimages(){

        return $this->hasMany('App\Models\BrandImage', 'service_provider_id');
    }

    public function integrations(){

        return $this->hasMany('App\Models\ServiceIntegration', 'service_id');
    }

    public function likes(){

        return $this->hasMany('App\Models\Likes','service_provider_id');
    }

    public function reviews(){

        return $this->hasMany('App\Models\Review','provider_id');
    }

    public function officeHours()
    {
        return $this->hasMany('App\Models\ServiceOfficeHours','service_provider_id','id');
    }

    public function city(){

        return $this->belongsTo('App\Models\City','city_id');
    }

    public function dailyTip()
    {
        return $this->hasMany('App\Models\DailyTip', 'service_provider_id');
    }

    public function getCitynameAttribute(){

        //dd($this->city['city_name']);
        return $this->city['city_name'];
    }

    public function area(){

        return $this->belongsTo('App\Models\City','area_id');
    }

    public function service_location(){

        return $this->belongsTo('App\Models\Location','location_id');
    }

    public function facebook_reviews() {
        return $this->hasMany('App\Models\ServiceFacebookReview', 'service_id');
    }

    public function service_rating(){

        return $this->hasOne('App\Models\Review','provider_id')
                    ->selectRaw('provider_id, AVG(sp_reviews.rating) as rating')
                    ->where('sp_reviews.rating', '!=', 0)
                    ->groupBy('provider_id');
    }

    public function documents($service_id){
        $docs = Storage::disk('s3')->allFiles(config('aws.services_document_directory')."/".$service_id."/");
        $documents = array();
        foreach ($docs as $doc) {
            $documents[] = serviceDocumentImage($doc);
        }

        return $documents;
    }

    public function getServiceType() {
        $sub_category = $this->subcategories()->first();
        if($sub_category) {
            return $this->subcategories()->first()->category_type;
        }
        return false;
    }

    public function getNewDocumentPath($original_filepath=null,$service_provider_id=null){

        $extension = $original_filepath? pathinfo($original_filepath)['extension'] : 'jpg';

        $name = uniqid('service_doc_');

        $s_id = $service_provider_id?$service_provider_id:$this->id;

        $path = join('/',[ $s_id, $name.'.'.$extension]);

        return $path;

    }

    public function getLocationAttribute(){

        //dd($this->service_location->location_name);
        return $this->service_location['location_name'].", ".$this->getCitynameAttribute();
    }

    public function getCategoryAttribute(){
        $category = [];
        foreach ($this->subcategories as $subCategory) {
            $category[] = $subCategory->category_name;
        }

        return join(', ', $category);
    }

    public function getParentAttribute()
    {
        $count = self::where('parent_service_id', $this->id)->count();
        if($count > 0)
        {
            return "Y";
        }
        else
        {
            return "";
        }
    }

    public function addIntegration($integration_code, $params){

        $si = ServiceIntegration::where('service_id',$this->id)->where('integration_code',$integration_code)->first();
        if(!$si){
            $si = new ServiceIntegration;
        }
        $si->service_id = $this->id;
        $si->integration_params = $params;
        $si->integration_code = $integration_code;
        if($integration_code == Service::CHAT_INTEGRATION){
            $this->updateServiceParamsForChat();
        }
        return $si->save();
    }

    public function removeIntegration($integration_code){
        if($integration_code == Service::CHAT_INTEGRATION){
            $this->updateServiceParamsForChat(false);
        }

        return ServiceIntegration::where('service_id',$this->id)->where('integration_code',$integration_code)->delete();
    }

    public function updateServiceParamsForChat($add = true){

            if($this->manager_id){
                // $qckblx = new Quickblox;
                $manager = User::find($this->manager_id);
            } else {
                return false;
            }

            if($add){
                $custom_data = json_encode([ 'service_id' => $this->id , 'service_name' => $this->name ,'service_icon' => '' ]);
            } else {
                $custom_data = json_encode([],JSON_FORCE_OBJECT);
            }

            // return $qckblx->updateUser($manager->quickblox_id, ['custom_data' => $custom_data , 'phone' => 'service']);
            return false;

    }

    //Vegeta
    //use this awesome relation to get reviews count for any service model
    public function reviewsCount()
    {
        //the hasOne below is just useful to
        //return one collection, instead of many if hasMany were to be used
        return $this->hasOne('App\Models\Review','provider_id')
                    ->selectRaw('provider_id, count(*) as count')
                    ->groupBy('provider_id');
    }

    public function serviceManager()
    {
        return $this->hasOne('App\Models\User','id','manager_id');
    }

    public function serviceContactPoint()
    {
        return $this->hasOne('App\Models\ServiceContactPoint','service_id','id');
    }

    //Vegeta
    //use this awesome relation to get likes count for any service model
    public function likesCount()
    {
        return $this->hasOne('App\Models\Likes','service_provider_id')
                    ->selectRaw('service_provider_id, count(*) as count')
                    ->groupBy('service_provider_id');
    }

    /**
     * Get Keyword Mapping
     * @return [type] [description]
     */
    public function keywordMapping()
    {
        return $this->belongsToMany('App\Models\Keyword', 'keywords_mapping', 'element_id', 'keyword_id')->where('element_type', 'service');
    }


    //Carbon is just messy date, still if a date is needed in
    //carbon format, return the column names within array below
    //refer more on github.com/briannesbitt/Carbon
    public function getDates()
    {
        return array();
    }

    //Get category name from category id
    public static function categoryName($cid){
        $category=DB::table('service_category')->where('id','=',$cid)->select('category_name')->first();
        return $category->category_name;
    }

    //Get service providers recommendation count
     public function  getRecommandationCount($spid){
      $type='serviceprovider';
      $sql="SELECT count(*) AS likes FROM bc_services_likes  where service_provider_id = '".$spid."' AND likes_status=1 AND type='".$type."'";

        $data = DB::select(DB::raw($sql));

       return $data[0]->likes;
    }


    //Notifiable Functions

    public function isChannelSupported($channel){

        //for push check if
        switch($channel){
            case Notifiable::PUSH:
                                $result = $this->manager_id > 0? true:false;
                                return $result;
            case Notifiable::SMS:
                                $result = $this->mobile_number?true:false;
                                return $result;
            case Notifiable::EMAIL:
                            return true;
            default:return false;
        }
       return false;
    }

    public function isChannelAllowed($channel){

        return true;
    }

    public function getEmailAddress(){

        $emails = explode(",",$this->email);
        return trim($emails[0]);
    }

    public function getMobileNumber()
    {
        $mobileNumbers = $this->mobile_number;
        $mobileNumbers = explode(',',$mobileNumbers);
        return trim($mobileNumbers[0]);
    }

    public function getDevice($device_type){

        if($this->manager_id){
            $user = User::with('devices')->where('id', $this->manager_id)->first();
             foreach($user->devices as $device){
                if($device->channel == $device_type){
                    return $device;
                }
            }
        }
        return null;
    }

    //end of Notifiable implementation

    //start of urlable implementation


    public function getUrl($type=Urlable::FULLURL,$params=[]){

        if($this->online_flag){
            $url = serviceurl($this->name, 'online', $this->id);
        } else {
            $url = serviceurl($this->name, $this->city['city_name'],$this->id);
        }

        switch($type){
            case Urlable::FULLURL: return web_url($url);
                            break;
            case Urlable::RELATIVEURL:  return $url;
                            break;
            default: return url($url);
                break;
        }

    }

    //end of urlable implementation


    //Get exotel digits of a service provider
    public function getExotelString($sid){
        $string='';
        $service=Service::where('id','=',$sid)->first();
        $digits=$service['exotel_digits'];
        $string='number:'.self::EXOTEL_NUMBER.' '.'extension:'.$digits;
        return $string;
    }

    //Get contact with extension of a service provider
    public static function getContactWithExotelExt($ext,$city_name){
        $city_name = Slugify::slugify($city_name);
        $cityNumber = config('exotel.numbers.'.$city_name);
        $string='number:'.$cityNumber.' '.'extension:'.$ext;
        return $string;
    }

    public function makeExotelDigit(){
        
        if($this->exotel_digits == 0 && $this->published == 1){
            $max_digit = Service::select(DB::raw('max(exotel_digits) as max_exo'))->first()->max_exo;
            $this->exotel_digits = $max_digit + 1;
            $this->save();
        }

        return $this;
    }


    public static function allServices($city_id=null,$limit=0){
        $query = self::where('published',1);
        if($city_id){

            $query = $query->where('city_id',$city_id);
        }
        if($limit>0){

            $query = $query->take($limit);
        }
        return $query->get();
    }

    public static function updatedServices($city_id=null){

         $query = self::where('published',1)->where('isindexed',0);
        if($city_id){

            $query = $query->where('city_id',$city_id);
        }
        return $query->get();
    }

    //Vegeta.
    public static function getIndexableServices($for,$offset=0)
    {
        //May consider the isindexed column

        if($for == "forAutocomplete")
        {   //not possible to get the following done
            //using one query in Eloquent, have to use query builder
            $data = DB::table('bc_services_providers_new as a')
                ->leftjoin('bc_services_providers_new as b','a.id','=','b.parent_service_id')
                ->where('a.parent_service_id','=','0')
                ->where('a.published','=',1)
                ->select(DB::raw('a.id as id,a.name as name,a.city_name as city_name,a.city_id as city_id,b.parent_service_id,count(a.id) as count,a.website as website,a.location as location,a.area_id as area_id, a.online_flag as online_flag, a.home_delivery_flag as home_delivery_flag, a.home_delivery_cities as home_delivery_cities'))
                ->groupBy('a.id')
                ->get();
        }
        elseif ($for == "forServices")
        {   //this is not as simple as it looks.
            //btw if you're wondering, the further
            //calculations, and comparisons
            //are done over the gathered data below
            //where the function is used.
            $data = Service::with([
                            'subcategories'=>function($query)
                                            {
                                                $query->with(['parentCategory'],['lifestages'],['interestTags'=>function($query){$query->with('lifestages');}]);
                                            }
                                    ],
                            ['images'],
                            ['reviews'=>function($query)
                                {
                                    $query->with('user')->where('published',1);
                                }],
                            ['likes'],['officeHours'],['service_location'],['integrations'],['serviceManager'],
                            ['packages'=>function($query)
                            {
                                $query->where('status',2);
                            }
                            ])
                            ->where('published',1)->skip($offset)->take(500)->get();
        }
        else
        {
            $data =[];
        }

        return $data;
    }

    public static function getAdServices ()
    {
        $ads    =   SponsoredItem::with('lifestages','locations','areas')
                                ->where('aditem_type','service')
                                ->where('ad_type','recommend')
                                ->where('status',1)
                                ->get()->unique();
        return $ads;
    }

    //Vegeta
    //The all new, now normalized, popularity index
    public static function getPopularityIndex()
    {

        $views = DB::table('page_view_metrics')
                    ->where('element_type','service')
                    ->get();
        $serviceViews = [];
        foreach ($views as $view)
            $serviceViews[$view->element_id] =   $view->views;

        $data = Service::with(
                    ['likes'=>function($query)
                        {
                        $query->with('user')
                        ->where('type','=','serviceprovider')
                        ->distinct();
                        }],
                    ['reviews'=>function($query)
                        {
                        $query->where('review_type','=','serviceprovider')
                        ->distinct();
                        }],
                    ['integrations']
                )->get();
        $popularityIndex    = [];

        foreach ($data as $value)
        {
            // if (!in_array($value->id, [13638]))
            //     continue;
            $likeUsers          = [];
            $reviewUsers        = [];

            //---Views Index
            $viewIndex          = scoreFunction((isset($serviceViews[$value['id']]))?$serviceViews[$value['id']]:0,config('elasticsearch.pi_views_k_factor'));
            $viewIndex          = $viewIndex*config('elasticsearch.pi_views_weight');
            //---

            //---Likes Index
            $momstarIndex       = 0;
            foreach ($value['likes'] as $like)
            {
                $likeUsers[] = $like['user_id'];
                if ($like->user['momstars'] == 'yes')
                    $momstarIndex   =   $momstarIndex + 1;
            }
            $likesIndex         = scoreFunction(count($likeUsers),config('elasticsearch.pi_likes_k_factor'));
            $likesIndex         = $likesIndex*config('elasticsearch.pi_likes_weight');
            //---

            //---Reviews Index
            foreach ($value['reviews'] as $reviews)
                $reviewUsers[] = $reviews['user_id'];
            $users_with_plus_reviews= array_intersect($likeUsers, $reviewUsers);
            $negative_users         = array_diff_assoc($reviewUsers, $users_with_plus_reviews);
            $reviewsIndex           = count($reviewUsers) - 0.5*count($negative_users);
            $reviewsIndex           = scoreFunction($reviewsIndex,config('elasticsearch.pi_reviews_k_factor'));
            $reviewsIndex           = $reviewsIndex*config('elasticsearch.pi_reviews_weight');
            //---

            //---MomStar Index
            $momstarIndex           = scoreFunction($momstarIndex,config('elasticsearch.pi_momstar_k_factor'));
            $momstarIndex           = $momstarIndex*config('elasticsearch.pi_momstarlike_weight');
            //---

            //---Integrations Index
            $integrationIndex       = 0;
            foreach ($value['integrations'] as $integration)
            {
                //chat, trans, lybrate
                $integrationIndex   = $integrationIndex + 1;
            }
            $integrationIndex       = scoreFunction($integrationIndex,config('elasticsearch.pi_integrations_k_factor'));
            $integrationIndex       = $integrationIndex*config('elasticsearch.pi_integrations_weight');
            //---

            $popularity_index = $viewIndex + $reviewsIndex + $likesIndex + $momstarIndex + $integrationIndex;

            //---Balancing Index
            $balancingIndex         = 0;
            if (config('elasticsearch.pi_bi_switch') == 1)
            {
                if ($popularity_index > config('elasticsearch.pi_bi_threshold') and $value->online_flag == 1)
                    $balancingIndex  = ($popularity_index - config('elasticsearch.pi_bi_threshold')) + rand(0,5)/10;
            }
            $popularity_index        = $popularity_index - $balancingIndex;
            if (in_array($value->id, [13638]))
                $popularity_index = $popularity_index/10;
            //^^the above is simply "$popularity_index = (config('elasticsearch.pi_bi_threshold') - rand(0,5)/10)"
            //^^it is coded in a more meaningful way, that's it.
            //---

            $popularityIndex[$value['id']]  = $popularity_index;
            // if ($popularity_index > 2)
            // echo "PI:".$popularity_index."\tVI:".$viewIndex."\tLI:".$likesIndex."\tRI:".$reviewsIndex."\tMI:".$momstarIndex."\t"."II:".$integrationIndex."\n";
        }
        // rsort($popularityIndex);
        return $popularityIndex;
    }

    //Vegeta
    public static function getServiceBabyChakraURL ($serviceProvider)
    {
        $cityName = $serviceProvider['city_name'];
        $city_name = City::where('id',$serviceProvider['city_id'])->first();
        // echo "yolo!".$city_name['city_slug']."\n";
        if ($city_name == null)
            $city_name = 'online';
        else
            $city_name = $city_name['city_slug'];
        $cityName = Slugify::slugify($city_name);
        if ($serviceProvider['online_flag'] == 1)
            $online_flag = true;
        else
            $online_flag = false;
        return serviceurl(Slugify::slugify($serviceProvider['name']),$cityName,$serviceProvider['id'],$online_flag);
    }

    public static function getChainServices ()
    {
        $services = Service::get();
        // $serviceNames = [];
        $serviceString = '';
        // foreach ($services as $service)
        // {
        //     if (!isset($serviceNames[$service->name]))
        //         $serviceNames[$service->name] = "id:".$service->id.","."parent:".$service->parent_service_id;
        //     else
        //         $serviceNames[$service->name] = "id:".$service->id.","."parent:".$service->parent_service_id."\n".$serviceNames[$service->name];
        // }
        foreach ($services as $service)
        {
            $c = City::where('id',$service->city_id)->first();
            $serviceString = $service->name.",".$service->id.",".$service->parent_service_id.",".(isset($c)?$c->city_name:"")."\n".$serviceString;
        }
        $file = fopen(storage_path("Services.txt"), "a");
        fwrite($file, $serviceString);
        fclose($file);
        return;
    }

    public function getServiceCentersAttribute()
    {
        $centerCount = self::where('parent_service_id', $this->id)
            ->where('published', 1)
            ->where('area_id', $this->area_id)
            ->count();

        if( $centerCount == 0 && $this->parent_service_id != 0 )
        {
            return self::where('parent_service_id', $this->parent_service_id)
                ->where('published', 1)
                ->where('area_id', $this->area_id)
                ->count();
        }
        return $centerCount;
    }

    public static function getServiceAddress($service){
        if($service->address_house_number || $service->address_street_name || $service->address_landmark)
        {
            $serviceAddress = $service->address_house_number;
            $serviceAddress = (strlen($service->address_street_name)>0)?$serviceAddress.', '.$service->address_street_name:$serviceAddress;
            $serviceAddress = (strlen($service->address_landmark)>0)?$serviceAddress.', '.$service->address_landmark:$serviceAddress;
            $serviceAddress = trim($serviceAddress);

        }
        else
        {
            $serviceAddress = $service->address1;
        }

        $serviceAddress = (strlen($service->pincode)>0)?$serviceAddress.' - '.$service->pincode:$serviceAddress;

        return $serviceAddress;
    }

    public function isPremium()
    {
        return in_array($this->id, config('admin.premium_profile'));
    }

    public function preAuthenticatedKey(){
        $base_key = config('app.one_time_base_key');
        $base_string = "service={$this->id}";
        return hash_hmac("sha256", $base_string, $base_key);
    }

    public function is_mahrashtra() {
        if($this->city) {
            return $this->city->parent_id == 1;
        }
        return false;
    }

    public function syncCategoryPackage() {
        $categoryPackages = CategoryPackage::where('service_id', $this->id)->get();
        foreach($categoryPackages as $package) {
            $package->status = $this->published;
            $package->save();
        }
    }

    public static function updateServiceRating($serviceRatings=[]) {

        foreach ($serviceRatings as $serviceId => $rating) {
            
            $rating = floatval($rating);
            $service = self::find($serviceId);
            $message = "Service Id : {$serviceId} not Found.\n";
            if($service) {
                $service->default_rating = $rating;
                $service->save();
                $message = "Service Id : {$service->id} updated.\n";
            }
            print($message);
        }
    }
}



