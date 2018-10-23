<?php
//                          ~~Vegeta~~
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Service;
use App\Models\Review;
use App\Models\ServiceImage;
use App\Models\Article;
use App\Models\User;
use App\Models\Event;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \DateTime;
use Slugify;
use App\Helpers\helpers;
use App\Models\City;
use App\Transformers\ServiceIntegrationTransformer;
/**
 *      Feed Model Class 
 *  Will respond to all requests related to extracting data from other models
 *  such as Service, Article, etc., This can be used to get any desired feed
 *  depending on various parameters.
 *  All the default parameters are stored at: config/feed.php
 *  Do have a look at it before trying to understand the code.
 */


// Feed:
//     Make config more commands based
//     events to be added in feed
//     collection article to pushed as well
//     So many people have joined babychakra .. not so important as of now.    


class Feed extends Model {

    /**     
    *       getData function
    */


    /**
     * Feed Type
     *  All/cumulative          => '0'
     *  Expecting               => '1'
     *  New                     => '2'
     *  Toddler                 => '3'
     *  Personalized User Feed  => '4' (or maybe the userID)
     */
    private $type;


    /**
     * Params
     *  they are the custom parameters
     *  they will help in adding extra, custom elements to the feed
     *  For instance, in Feed Type '1', 
     *      you may want to push certain articles and services,
     *      which may lie outside of date range or 
     *      may need to be given preference to appear earlier in the feed.
     *  I think its best that these are given in the config file itself,
     *  Since it would be cumbersome to feed them in the console
     *  
     *  Check for params structure in the config/feed.php
     */
    private $params = array();

    /**
     * Date
     *  from the config file 
     *  we here construct the duration span of query
     *  dates are of the format '1904-05-10 04:52:19'
     *  date('Y-m-d H:i:s');
     */
    private $date = array(
                    'service'   =>
                        [
                            'start' => '',
                            'end'   => ''
                        ],
                    'article'   =>
                        [
                            'start' => '',
                            'end'   => ''
                        ],
                    'spReview'  =>
                        [
                            'start' => '',
                            'end'   => ''
                        ]
                );


    private $city_name;
    private $city_id;

    //use original city names when querying databases for city.
    private $originalCityName;

    public function __construct($type='all',$city_name = 'mumbai')
    {
        $this->type     = Slugify::slugify($type);
        $this->setDate();
        // echo $this->date['service']['end'];
        $this->city_name = Slugify::slugify($city_name);
        $this->originalCityName = $city_name;


        $this->city_id = City::getCityFromSlug($this->originalCityName);
        if ($this->city_id->parent_id)
        {   
            $this->city_id = $this->city_id->parent_id;
        }
        else   
        {
            $this->city_id = $this->city_id->id;
        } 
            


        if ($this->type == 'all')
        {
            $this->params   = config('feed.params.all');
        }
        elseif ($this->type == 'expecting-a-baby')
        {
            $this->params   = config('feed.params.expecting');
        }
        elseif ($this->type == 'new-parents')
        {
            $this->params   = config('feed.params.new');
        }
        elseif ($this->type == 'toddler')
        {
            $this->params   = config('feed.params.toddler');
        }
        else
            echo "This feature is yet to come, try with one of the usual feeds, try 'all'";
    }


    /**
     * setDate
     *  this function sets the date of start and end
     *  for all newsfeed element types
     *  like service, article, and reviews
     *  Note: later here itself user start and 
     *  end date times can be set as well
     */
    public function setDate ()
    {
        $this->date['service']['start']  = new DateTime();
        $this->date['service']['start']  = $this->date['service']['start']->modify("-".config('feed.timeSpan.service')." day");
        $this->date['service']['start']  = $this->date['service']['start']->format('Y-m-d H:i:s');
        $this->date['service']['end']    = new DateTime();
        $this->date['service']['end']    = $this->date['service']['end']->format('Y-m-d H:i:s');
        // echo $this->date['service']['end'];

        $this->date['article']['start']  = new DateTime();
        $this->date['article']['start']  = $this->date['article']['start']->modify("-".config('feed.timeSpan.article')." day");
        $this->date['article']['start']  = $this->date['article']['start']->format('Y-m-d H:i:s');
        $this->date['article']['end']    = new DateTime();
        $this->date['article']['end']    = $this->date['article']['end']->format('Y-m-d H:i:s');

        $this->date['spReview']['start']  = new DateTime();
        $this->date['spReview']['start']  = $this->date['spReview']['start']->modify("-".config('feed.timeSpan.spReview')." day");
        $this->date['spReview']['start']  = $this->date['spReview']['start']->format('Y-m-d H:i:s');
        $this->date['spReview']['end']    = new DateTime();
        $this->date['spReview']['end']    = $this->date['spReview']['end']->format('Y-m-d H:i:s');

        $this->date['user']['start']    = new DateTime();
        $this->date['user']['start']    = $this->date['user']['start']->modify("-".config('feed.timeSpan.user')." day");
        $this->date['user']['start']    = $this->date['user']['start']->format('Y-m-d H:i:s');
        $this->date['user']['end']      = new DateTime();
        $this->date['user']['end']      = $this->date['user']['end']->format('Y-m-d H:i:s');

        $this->date['event']['start']    = new DateTime();
        $this->date['event']['start']    = $this->date['event']['start']->modify("-".config('feed.timeSpan.event')." day");
        $this->date['event']['start']    = $this->date['event']['start']->format('Y-m-d H:i:s');
        $this->date['event']['end']      = new DateTime();
        $this->date['event']['end']      = $this->date['event']['end']->modify("+20 day");
        $this->date['event']['end']      = $this->date['event']['end']->format('Y-m-d H:i:s');
    }

    //One man's trash is another man's treasure.
    //One function's data is another function's rawdata
    public function getServiceData ()
    {   
        $pushedData     =   Service::with(
                                ['likes'=>function($query)
                                {
                                    $query->with('user');
                                }]
                                ,['images'],
                                ['subcategories'=>function($query)
                                {
                                    $query->with('lifestages');
                                }]
                                ,['reviews'],['integrations'])
                            ->whereIn('id',$this->params['services']['pushIDs'])
                            ->where('area_id','=',$this->city_id)
                            ->groupBy('id')
                            ->orderBy('updated_at','desc')
                            ->get();


        $rawServiceData =   Service::with(
                                ['likes'=>function($query)
                                {
                                    $query->with('user');
                                }]
                                ,['images'],
                                ['subcategories'=>function($query)
                                {
                                     $query->with('lifestages');
                                }]
                                ,['reviews'],['integrations'])
                                ->whereHas('city',function($query)
                                {
                                    $query->whereIn('parent_id',[$this->city_id,0]);
                                })
                            ->where('published','=',1)
                            ->whereBetween('updated_at', [$this->date['service']['start'],$this->date['service']['end']])
                            ->groupBy('id')
                            ->orderBy('updated_at','desc')
                            ->take(config('feed.numOfRawElementsExtracted'))
                            ->get();
        

        // echo $this->date['service']['end'];
        //union doesn't seem to work with ORM objects,
        //Only works with Query Builder (as done in getArticleData)
        $serviceData    = array();
        $redundantIDs   = array();
        $mergePoint     = 0;
        $temp           = array();
        // $rawServiceData = $rawServiceData->toArray();
        // $pushedData     = $pushedData->toArray();
        
        // array merge is giving certain issues, 
        // even on converting to array, certain data loss is there
        // $rawServiceData = array_merge($rawServiceData,$pushedData);

        foreach ($rawServiceData as $value)
        {      
            $likers     = [];
            $online_flag= 0;
            if ($value['online_flag'] ==  1)
            {
                $online_flag    =   1;
                $hdc            =   $value['home_delivery_cities'];
                if ($hdc == '')
                    continue;
                else
                {
                    $hdc    =   explode(',', $hdc);
                    if (!in_array($this->city_id, $hdc))
                        continue;
                }
            }
            $integrations = [];
            $integrationTransformer = new ServiceIntegrationTransformer();
            foreach ($value['integrations'] as $integration)
            {
                //chat, trans, lybrate
                if(isset($integration->integration_code))
                    $integrations[] = $integrationTransformer->transform($integration);
            }

            if (isset($value['likes'][0]))
            {
                foreach ($value['likes'] as $valLike) 
                {   
                    $tempLike   = [];
                    if($valLike['user_id'] != 0 )
                    {
                        if(isset($valLike['user']['name']))
                        {
                            $tempLike['name']   = $valLike['user']['name'];
                            $tempLike['id']     = $valLike['user']['id'];
                            $tempLike['date']   = $valLike['time'];

                            $tempLike['image']  = userimage($valLike['user']['avatar'],'thumb',$valLike['user']['momstars']);
                            $tempLike['momstar']= ($valLike['user']['momstars'] == 'yes') ? true:false;
                            $likers[]           = $tempLike;
                        }                            
                    }                    
                }
            }
            $categoryNames = [];
            $catLifestages = [];
            $LifestageFlag = 0;
            if(isset($value['subcategories'][0]))
            {   
                $categoryNames = [];
                foreach($value['subcategories'] as $catValue)
                {
                    $categoryNames[]   = trim($catValue['category_name']);
                    foreach($catValue['lifestages'] as $catLStage)
                    {
                        if(in_array($catLStage['id'], $this->params['lifestage']))
                        {
                            $LifestageFlag = 1;
                        }
                    }
                }
            }

            //extraction of Reviews
            $reviewsData = [];
            if(isset($value['reviews'][0]))
            {   
                foreach ($value['reviews'] as $valRev) 
                {
                    if($valRev['user_id'] != 0 & $valRev['published'] == 1 )
                    {
                        $reviewsData[] = $valRev;    
                    }                    
                }                
            }

            //since not using union
            //have to see which IDs are redundantly occurring
            if (in_array($value['id'], $this->params['services']['pushIDs']))
            {   
                if (isset($redundantIDs["'".$value['id']."'"]))
                {   
                    continue;
                }
                else 
                    $redundantIDs["'".$value['id']."'"] = 1;
            }


            $temp['categories']          = $categoryNames;//sending sub-cat names.
            $temp['likers']              = $likers;
            $temp['numOfLikes']          = sizeof($likers);
            $temp['id']                  = $value['id'];
            $temp['name']                = $value['name'];
            $temp['keyword']             = $value['keyword'];
            $temp['about']               = $value['about'];
            $temp['images']              = $value['images'];
            $temp['review_count']        = count($reviewsData);
            $temp['contact']             = $value['contact'];
            $temp['mobile_number']       = $value['mobile_number'];
            $temp['city_id']             = $value['city_id'];
            $temp['city_name']           = $value['city_name'];
            $temp['location_id']         = $value['location_id'];
            $temp['location']            = $value['location'];
            $temp['geo_location']['lat'] = $value['latitude'];
            $temp['geo_location']['lon'] = $value['longitude'];
            $temp['business_hours']      = $value['business_hours'];
            $temp['price_range']         = $value['price_range'];
            $temp['website']             = $value['website'];
            $temp['address']             = $value['address1'];
            $temp['facebook_url']        = $value['facebook_url'];
            $temp['exotel_digits']       = $value['exotel_digits'];
            $temp['created_on']          = isset($value['created_on']) ? $value['created_on'] : '';
            $temp['updated_by']          = $value['updated_by'];
            $temp['url']                 = Service::getServiceBabyChakraURL($value);
            $temp['integrations']        = ['data' => $integrations ];
            $temp['verified']            = $value['verified'];
            $temp2['actor']              = iconv("UTF-8","ASCII//TRANSLIT",$value['name']);
            $temp2['verb']               = 'service_provider';
            $temp2['object']             = $temp['id'];
            $temp2['data']               = $temp;
            $temp2['foreign_id']         = 'service_provider-'.$temp['id'];

            if (!$temp2['actor'] )
            {   
                continue;
            }

            if ($LifestageFlag == 1)
            {
                $serviceData[]  = $temp2;
            }
        }

        foreach ($pushedData as $value)
        {   
            $temp   = [];
            $temp2  = [];
            $likers     = [];
            if (isset($value['likes'][0]))
            {
                foreach ($value['likes'] as $valLike) 
                {   
                    $tempLike   = [];
                    if($valLike['user_id'] != 0 )
                    {
                        if(isset($valLike['user']['name']))
                        {
                            $tempLike['name']   = $valLike['user']['name'];
                            $tempLike['id']     = $valLike['user']['id'];
                            $tempLike['date']   = $valLike['time'];

                            $tempLike['momstar']= ($valLike['user']['momstars'] == 'yes') ? true:false;
                            $tempLike['image']  = userimage($valLike['user']['avatar'],'thumb',$valLike['user']['momstars']);
                            $likers[]           = $tempLike;
                        }                            
                    }                    
                }
            }
            $integrations = [];
            $integrationTransformer = new ServiceIntegrationTransformer();
            foreach ($value['integrations'] as $integration)
            {
                //chat, trans, lybrate
                if(isset($integration->integration_code))
                    $integrations[] = $integrationTransformer->transform($integration);
            }
            $categoryNames = [];
            if(isset($value['subcategories'][0]))
            {   
                $categoryNames = [];
                foreach($value['subcategories'] as $catValue)
                {
                    $categoryNames[]   = trim($catValue['category_name']);
                }
            }


            //extraction of Reviews
            $reviewsData = [];
            if(isset($value['reviews'][0]))
            {   
                foreach ($value['reviews'] as $valRev) 
                {
                    if($valRev['user_id'] != 0 & $valRev['published'] == 1 )
                    {
                        $reviewsData[] = $valRev;
                    }                    
                }                
            }

            $temp['categories']          = $categoryNames;
            $temp['likers']               = $likers;
            $temp['numOfLikes']          = sizeof($likers);
            $temp['id']                  = $value['id'];
            $temp['name']                = $value['name'];
            $temp['keyword']             = $value['keyword'];
            $temp['about']               = $value['about'];
            $temp['images']              = $value['images'];
            $temp['review_count']        = count($reviewsData);
            $temp['contact']             = $value['contact'];
            $temp['mobile_number']       = $value['mobile_number'];
            $temp['city_id']             = $value['city_id'];
            $temp['city_name']           = $value['city_name'];
            $temp['location_id']         = $value['location_id'];
            $temp['location']            = $value['location'];
            $temp['geo_location']['lat'] = $value['latitude'];
            $temp['geo_location']['lon'] = $value['longitude'];
            $temp['business_hours']      = $value['business_hours'];
            $temp['price_range']         = $value['price_range'];
            $temp['website']             = $value['website'];
            $temp['address']             = $value['address1'];
            $temp['facebook_url']        = $value['facebook_url'];
            $temp['exotel_digits']       = $value['exotel_digits'];
            $temp['created_on']          = isset($value['created_on']) ? $value['created_on'] : '';
            $temp['updated_by']          = $value['updated_by'];
            $temp['url']                 = Service::getServiceBabyChakraURL($value);
            $temp['integrations']        = ['data' => $integrations ];
            $temp['verified']            = $value['verified'];
            $temp2['actor']              = iconv("UTF-8","ASCII//TRANSLIT",$value['name']);
            $temp2['verb']               = 'service_provider';
            $temp2['object']             = $temp['id'];
            $temp2['data']               = $temp;
            $temp2['foreign_id']         = 'service_provider-'.$temp['id'];
            
            if (!$temp2['actor'])
            {
                continue;
            }
            

            if (! isset($redundantIDs["'".$value['id']."'"]))
            {
                $serviceData[]       = $temp2;
            }               

        }

        //sorting on the likes
        usort($serviceData, function($a, $b)
        {
            return -1*($a['data']["numOfLikes"] - $b['data']["numOfLikes"]);
        });


        //boosting
        //Note that boosting has preference over sorting on likes
        $numOfServices = count($serviceData);
        foreach ($serviceData as $tk => $service) 
        {   
            $y = [];
            if(in_array($service['data']['id'], $this->params['services']['boostIDs']))
            {   
                unset($serviceData[$tk]);
                $x                   = array_slice($serviceData,0,$mergePoint,true);
                $y[]                 = $service;
                $z                   = array_slice($serviceData,$mergePoint,$numOfServices,true);
                $serviceData         = array_merge($x,$y,$z);
                $mergePoint = $mergePoint+config('feed.boostMergeInterval');
            }

        }

        return $serviceData;
    }



    public function getArticleData () 
    {   
        //since the article lifestage table hasn't been constructed in a model,
        //using article queries directly from the DB, so as to use a join.
        //['123','127','2','99','427']
        $pushedData =       DB::table('bc_content')
                            ->leftJoin('bc_users','bc_content.author_id','=','bc_users.id')
                            ->leftJoin('bc_content_city_mapping','bc_content.id','=','bc_content_city_mapping.article_id')
                            ->select('bc_content.id as id','bc_content.title as title','bc_content.images as images','bc_content.modified as modified','bc_content.alias as alias','bc_content.metadesc as desc','bc_users.name as author_name','bc_users.id as author_id','bc_users.avatar as avatar','bc_content.created as created','bc_users.momstars as momstars')
                            ->whereIn('bc_content.id',$this->params['articles']['pushIDs']);


        //Did you know there is a frickin' orWhereNull as well!!
        //Maybe we could add the num of elements returned to config.
        $rawArticleData =   DB::table('bc_content')
                            ->leftJoin('bc_content_lifestages_mapping','bc_content.id','=','bc_content_lifestages_mapping.article_id')
                            ->leftJoin('bc_users','bc_content.author_id','=','bc_users.id')
                            ->leftJoin('bc_content_city_mapping','bc_content.id','=','bc_content_city_mapping.article_id')
                            ->select('bc_content.id as id','bc_content.title as title','bc_content.images as images','bc_content.modified as modified','bc_content.alias as alias','bc_content.metadesc as desc','bc_users.name as author_name','bc_users.id as author_id','bc_users.avatar as avatar','bc_content.created as created','bc_users.momstars as momstars')
                            ->whereIn('bc_content_lifestages_mapping.lifestage_value',$this->params['lifestage'])
                            ->whereBetween('modified', [$this->date['article']['start'],$this->date['article']['end']])
                            ->where('state','=',1)
                            ->groupBy('bc_content.id')
                            ->orderBy('created','desc')
                            ->take(config('feed.numOfRawElementsExtracted'))
                            ->union($pushedData)
                            ->groupBy('bc_content.id')
                            ->get();
        //  if the above query doesnt work refer these two:
        //  $c = DB::table('bc_content')->select('bc_content.id as id','bc_content.title as title','modified')->whereIn('id',config('feed.params.all.articles.pushIDs'));
        //  DB::table('bc_content')->leftJoin('bc_content_lifestages_mapping','bc_content.id','=','bc_content_lifestages_mapping.article_id')->select('bc_content.id as id','bc_content.title as title','modified')->whereIn('bc_content_lifestages_mapping.lifestage_value',[9])->union($c)->groupBy('id')->orderBy('modified','desc')->take(40)->get();
        $temp           = array();
        $temp2          = array();
        $mergePoint     = 0;
        $articleData    = array();
        $authorDefault  = User::where('id',612)->first();

        foreach ($rawArticleData as $value) 
        {   
            $area_flag = 0;
            $festival_flag = 0;
            $details = Article::with('area','festivals')->where('id',$value->id)->first();
            $areas = $details->area;
            $festivals = $details->festivals;
            $today = date('Y-m-d',strToTime('today'));
            foreach ($areas as $area) 
            {
                if ($area->id == $this->city_id)
                {
                    $area_flag = 1;
                    break;
                }   

            }
            if ($area_flag == 0)
                continue;

            foreach ($festivals as $festival)
            {
                if ($today > $festival->start_date and $today < $festival->end_date)
                {
                    $festival_flag = 1;
                    break;
                }
            }
            if (sizeof($festivals) == 0)
                $festival_flag = 1;
            if ($festival_flag == 0)
                continue;

            if (isset($value->desc) and $value->desc != "")
            {
                $temp['description'] = $value->desc;
            }
            else
                $temp['description'] = "";

            if (isset($value->author_name))
            {   
                $temp['author']     = $value->author_name;
                $temp['momstar']    = ($value->momstars == 'yes') ? true:false;
                if ($value->avatar != "")
                {
                    $temp['author_image']   = userimage($value->avatar,'thumb');
                    $temp['author_id']      = $value->author_id;
                }
                else
                {
                    $temp['author_image']   = userimage('','thumb', $value->momstars);
                    $temp['author_id']      = $value->author_id;
                }
            }
            else
            {   
                $author                 = $authorDefault;
                $temp['momstar']        = ($value->momstars == 'yes') ? true:false;
                $temp['author']         = $author['name'];
                $temp['author_image']   = userimage($author['avatar'],'thumb');
                $temp['author_id']      = 612;        
            }
                
            // if ($value->intro != "")
            // {
            //     $introTemp           = explode('</',$value->intro);
            //     $introTemp           = $introTemp[0];
            //     $introTemp           = explode('>',$introTemp);
            //     $introTemp           = $introTemp[sizeof($introTemp)-1];    
            // }
            // else
            //     $introTemp           = "";
            $temp['id']              = $value->id;
            $temp['title']           = $value->title;
            $temp['images']          = $value->images;
            $temp['created']         = $value->created;
            $temp['comments_count']  = 0;//.........................................TODO
            $temp['likes_count']     = 0;//.........................................TODO
            $temp['url']             = join('/',[ config('app.url'),'learn', $value->id.'-'.$value->alias ]);
            $temp2['actor']          = iconv("UTF-8","ASCII//TRANSLIT",$temp['author']);
            $temp2['verb']           = 'article';
            $temp2['object']         = $temp['id'];
            $temp2['data']           = $temp;
            $temp2['foreign_id']     = 'article-'.$temp['id'];

            // echo $value->id;
            // echo "id^\n";

            // echo join('/',[ config('app.url'),'learn', $value->id.'-'.$value->alias ]);

            if (!$temp2['actor'])
                continue;
            // Below is the process of boosting 'boostIDs'
            if(in_array($temp['id'], $this->params['articles']['boostIDs']))
            {   
                $x                   = array_slice($articleData,0,$mergePoint,true);
                $y[]                 = $temp2;
                $z                   = array_slice($articleData,$mergePoint,count($articleData),true);
                $articleData         = array_merge($x,$y,$z);
                $mergePoint = $mergePoint+config('feed.boostMergeInterval');
            }
            else
                $articleData[]       = $temp2;
            $y = [];
            
        }
        return $articleData;
    }

    public function getServiceReviewData ()
    {   
        $serviceReviewData = [];
        //  the lifestage of the review depends on the corresponding service provider
        //  So will get to this once that sorts out
        // Also implement pushing and boosting of reviews if needed
        $rawServiceRevData =    Review::with(['serviceprovider'=>function($query) 
                                    {
                                        $query->with('subcategories','reviews')
                                            ->where('area_id','=',$this->city_id);
                                    }],'user')
                                ->where('published','=',1)
                                ->where('review_type' ,'=', 'serviceprovider')
                                ->whereBetween('time', [$this->date['spReview']['start'],$this->date['spReview']['end']])
                                ->orderBy('time','desc')
                                ->take(config('feed.numOfRawElementsExtracted'))
                                ->get();

        foreach ($rawServiceRevData as $value) 
        {   
            if($value['serviceprovider']['city_id'] == null)
                continue;
            if($value['user_id'] == 0)
                continue;

            $categoryNames = [];
            if(isset($value['serviceprovider']['subcategories'][0]))
            {   
                $categoryNames = [];
                foreach($value['serviceprovider']['subcategories'] as $catValue)
                {
                    $categoryNames[]   = trim($catValue['category_name']);
                }
            }

            $reviewCount = 0;
            if(isset($value['serviceprovider']['reviews'][0]))
            {   
                foreach($value['serviceprovider']['reviews'] as $rev)
                {
                    $reviewCount = $reviewCount + 1;
                }
            }

            $temp['categories']            = $categoryNames;
            $temp['review_count']          = $reviewCount;
            $temp['id']                    = $value['id'];
            $temp['service_id']            = $value['provider_id'];
            $temp['service_name']          = $value['serviceprovider']['name'];
            $temp['service_location']      = $value['serviceprovider']['location'];
            $temp['service_city']          = $value['serviceprovider']['city_name'];
            $temp['service_url']           = Service::getServiceBabyChakraURL($value['serviceprovider']);
            $temp['review_title']          = $value['review_title'];
            $temp['review_text']           = $value['review'];
            $temp['review_time']           = $value['time'];
            $temp['user_id']               = $value['user_id'];
            $temp['user_name']             = $value['user']['name'];
            $temp['verified']              = $value['serviceprovider']['verified'];
            $temp['momstar']               = ($value['user']['momstars'] == 'yes') ? true:false;
            $temp['user_profile_image']    = userimage($value['user']['avatar'],'thumb', $value['user']['momstars']);
            $temp2['actor']                = iconv("UTF-8","ASCII//TRANSLIT",$temp['user_name']);
            $temp2['verb']                 = 'review_sp';
            $temp2['object']               = $value['id'];
            $temp2['data']                 = $temp;
            $temp2['foreign_id']           = 'review_sp-'.$temp['id'];

            if (!$temp2['actor'])
                continue;
            // if($temp2['actor'] == '' or $temp2['actor'] == null)
            // {
            //     echo "\nyeah!";
            //     echo $value['user_id'];
            //     echo " : ";
            //     echo $temp['id'];
            // }


            $serviceReviewData[]           = $temp2;

        }
        return $serviceReviewData;
    }

    //The function returns the aggregates of users recently joined
    //sends an array in every element,
    //each element has 1 main user, n number of extras, and the main user's image
    public function getUserAggregateData()
    {   
        $rawUserData = User::whereBetween('created_at', [$this->date['user']['start'],$this->date['user']['end']])
                            ->orderBy('created_at','desc')
                            ->take(config('feed.numOfRawElementsExtracted'))
                            ->get();

        $counter    = 0;
        $extra      = 0;
        $textMsg    = "";
        $userData   = [];
        foreach ($rawUserData as $value) 
        {   
            if ($counter == 0)
            {
                $counter    = rand(2,config('feed.maxNumOfUsers'));
                $extra      = $counter - 1;
            }

            if($counter > 1)
            {   
                $counter = $counter-1;
                continue;
            }
            elseif ($counter == 1)
            {   
                if ($value['name'] == "")
                    continue;
                $temp2['actor']             = iconv("UTF-8","ASCII//TRANSLIT",$value['name']);
                $temp2['verb']              = 'new_users';
                $temp2['object']            = $value['id'];
                $temp2['data']['name']      = $value['name'];
                $temp2['data']['id']        = $value['id'];
                $temp2['data']['image']     = userimage($value['avatar'],'thumb');
                $temp2['data']['extras']    = $extra;
                $temp2['data']['date']      = $value['created_at'];
                $temp2['foreign_id']        = 'new_users-'.$value['id'];

                if (!$temp2['actor'])
                    continue;

                $userData[]                 = $temp2; 
                $counter                    = $counter-1;
                $textMsg                    = [];
                $temp2                      = [];
            }
        }

        return $userData;
    }

    public function getEventData()
    {   


        $rawEventData   = Event::with('city','location')
                                ->whereBetween('event_start_date', [$this->date['event']['start'],$this->date['event']['end']])
                                ->where('event_area_id',$this->city_id)
                                ->orderBy('event_start_date','desc')
                                ->take(config('feed.numOfRawElementsExtracted'))
                                ->get();
        // print_r($rawEventData);
        $user           = User::where('id',817)->get();
        $eventData      = [];
        foreach ($rawEventData as $value) 
        {    
            // if ($value['id'] == 548)
            // {
            //     echo "yolo!";
            // }
            $city           = City::where('id',$value['city']['id'])->first();
            $area           = City::where('id',$city['id'])->first();
            $area_slug      = $area['city_slug'];

            $created_date = $value['created_date'];
            if ($value['created_date'] == '0000-00-00 00:00:00')
            {
                $created_date    = new DateTime();
                $created_date    = $created_date->format('Y-m-d H:i:s');
            }

            $temp['id']                     =   $value['id'];
            $temp['start_date']             =   $value['event_start_date'];
            $temp['end_date']               =   $value['event_end_date'];
            $temp['time']                   =   $value['event_time'];
            $temp['location']               =   $value['location']['location_name'];
            $temp['city']                   =   $area_slug;
            $temp['address']                =   $value['event_address'];
            $temp['title']                  =   $value['event_name'];
            $temp['image']                  =   eventimage($value['event_image'],'thumb');
            $temp['creation_date']          =   $created_date;
            $temp['user_name']              =   $user[0]['name'];
            $temp['user_image']             =   userimage($user[0]['avatar'],'thumb');
            $temp['user_id']                =   $user[0]['id'];
            $temp['user_url']               =   user_url($user[0]['id']);
            $temp['event_url']              =   $value['seo_url'];
            $temp2['actor']                 =   iconv("UTF-8","ASCII//TRANSLIT",$user[0]['name']);
            $temp2['verb']                  =   'event';
            $temp2['object']                =   $value['id'];
            $temp2['foreign_id']            =   'event-'.$value['id'];
            $temp2['data']                  =   $temp;
            if (!$temp2['actor'])
                continue;
            $eventData[]                    =   $temp2;

        }

        return $eventData;
    }


    /**
     *      getFeed
     *  Mix and jumble
     *  combine different elements according 
     *  to the configuration pre-defined
     */
    public function getFeed ($numOfElements = 70)
    {
        $serviceFeed    = $this->getServiceData();
        $articleFeed    = $this->getArticleData();
        $reviewFeed     = $this->getServiceReviewData();
        $userFeed       = $this->getUserAggregateData();
        $eventFeed      = $this->getEventData();

        $servicePointer = 0;
        $articlePointer = 0;
        $reviewPointer  = 0;
        $userPointer    = 0;
        $eventPointer   = 0;

        $feed           = array();
        $seq            = $this->deterministicSequence();

        $i = 0;
        while ($i<$numOfElements)
        {
            if ($seq[$i%10] == 'service')
            {   
                if (isset($serviceFeed[$servicePointer]))
                {   
                    $feed[] = $serviceFeed[$servicePointer];
                    $servicePointer++;
                }
            }
            elseif ($seq[$i%10] == 'article') 
            {
                if (isset($articleFeed[$articlePointer]))
                {
                    $feed[] = $articleFeed[$articlePointer];
                    $articlePointer++;     
                }               
            }
            elseif ($seq[$i%10] == 'spReview')
            {   
                if (isset($reviewFeed[$reviewPointer]))
                {
                    $feed[] = $reviewFeed[$reviewPointer];
                    $reviewPointer++;
                }
            }
            elseif ($seq[$i%10] == 'user')
            {   
                if (isset($userFeed[$userPointer]))
                {
                    $feed[] = $userFeed[$userPointer];
                    $userPointer++;
                }
            }
            elseif ($seq[$i%10] == 'event')
            {   
                if (isset($eventFeed[$eventPointer]))
                {
                    $feed[] = $eventFeed[$eventPointer];
                    $eventPointer++;
                }
            }   
            $i++;
        }
        // $logger = Log::getMonolog();
        // $logger->pushHandler(new StreamHandler('/var/www/laravel5/storage/logs/vegetaTest.log', Logger::DEBUG));
        // $logger->addInfo(serialize($feed));
        return $feed;
    }

    //sequence can be made better, is fine o/w..............................TODO
    public function deterministicSequence()
    {
        $composition    = config('feed.composition');
        $seq            = array();
        $i              = 0;
        $j              = 0;
        $finishedKeys   = array();
        while($i<10)
        {   
            if($j>=count($composition))
                break;
            foreach ($composition as $key => $value) 
            {   
                if ($j >= count($composition))
                    break;
                if($value>0)
                {   
                    $seq[$i] = $key;
                    $composition[$key]--;
                    $i++;    
                }
                else
                {   
                    if (!in_array($key, $finishedKeys))
                    {
                       $j++; 
                       $finishedKeys[] = $key;
                       continue;
                    }                    
                }
                
            }            
        }
        return $seq;
    }


}
