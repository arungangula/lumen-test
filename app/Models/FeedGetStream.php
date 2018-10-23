<?php
//                          ~~Vegeta~~
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Feed;
use GetStream\Stream\Client;
use Slugify;

/**
 *      Feed Model Class 
 *  Will respond to all requests related to extracting data from other models
 *  such as Service, Article, etc., This can be used to get any desired feed
 *  depending on various parameters.
 *  All the default parameters are stored at: ............................................TODO
 */
class FeedGetStream extends Model {

    private $client;
    private $limit;

    public function __construct()
    {
        $this->client   = new Client(config('feed.api_key'), config('feed.api_secret'));
        $this->limit    = config('feed.limit');
    }


    //the name of the user id for feed would be 
    //"u-id"
    //for cities it is
    //"cityName-feedType"
    public function insertBatch($cityName='mumbai',$feedType='all')
    {
        $feedInstance       = new Feed($feedType,$cityName);
        // slugify is used to get rid of spaces and CAPS
        $feedId             = Slugify::slugify($cityName."-".$feedType);
        $feed               = $this->client->feed('user',$feedId);
        try
        {
            $feed->addActivities(array_reverse($feedInstance->getFeed()));
            return ['error'=>false];
        }
        catch(\Exception $e)
        {
            return [
                'error'    =>true,
                'exception'=>$e->getMessage(),
            ];
        }
        
        // $dataReceivedBack   = $feed->getActivities(0,100,[]);
        // return $dataReceivedBack;
    }

    //this won't work for batches.
    public function deleteBatch($foreign_id)
    {
        $all_feed   ->removeActivity($foreign_id,true);   
    }

    //  based on the latestFeedId and oldFeedId values the function below
    //  computes the feed data to be sent
    public function getFeed($feedId='mumbai-all',$latestFeedId='',$oldFeedId='')
    {   
        $feed = $this->client->feed('user',$feedId);
        if ($latestFeedId!=""&$oldFeedId!="")
        {
            //The case when both are sent
            //will not be needed
            $data = [];
        }
        elseif ($latestFeedId!="")
        {
            //just the latestFeedId is sent
            //to get the newer feeds 
            $feed       = $this->client->feed('user',$feedId);
            $data       = $feed->getActivities(0,$this->limit,array('id_gt'=>$latestFeedId));

        }
        elseif ($oldFeedId!="")
        {
            //To get the old feeds on scroll down
            $feed       = $this->client->feed('user',$feedId);
            $data       = $feed->getActivities(0,$this->limit,array('id_lt'=>$oldFeedId));
        }
        else
        {
            //To get the latest feeds as per the limit.
            $data       = $feed->getActivities(0,5,[]);
        }
        return $data;
    }

    //  function to test whether the new feed is available or not
    public function checkNewFeed($feedId,$latestFeedId)
    {   
        $feed           = $this->client->feed('user',$feedId);
        $data           = $feed->getActivities(0,350,array('id_gt'=>$latestFeedId));
        return (sizeof($data["results"])>0) ? sizeof($data["results"]) : 0;
    }
}
