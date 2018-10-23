<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;
use App\Models\Article;
use App\Models\Review;
use View;
use Input;
use Validator;
use stdClass;
use JWTAuth;
use DB;

//NewsFeed Model to work with the Feed API (Similar to the website NewsFeed Model)
//This class is required to run the getstream cron when data is fetched from newsfeed 
//table to push to getstream
//code taken from website NewsFeed model

class NewsFeed extends Model {

    public $id;
    const MAX_NEWS_DATA_PAGE_SIZE =50;
    const PAGE_BREAK=3;
    public $limit;
    public $offset;
    public $news_feed_id;
    public $user_id;
    public $object_type;
    public $object_id;
    public $action_type;
    public $action_id;
    public $parent_action_id;
    public $weight;
    public $city_id;
    public $cat_id;
    public $parent_cat_id;
    public $action_date;
    public $insert_date;
    public $object_type_arr=array();
    public $action_type_arr=array();
    public $weight_type_arr=array();
  
    public function columns() {
    		return $this->columns;
    }

    public function getNewsFeedColumnsName(){
    		$colstr = implode(', ', $this->columns);
    		return $colstr;
    }
    public function getObjectTypeArray(){
       return $this->object_type_arr=unserialize(OBJECT_ARR);
    }
    public function getActionTypeArray(){
        return $this->action_type_arr=unserialize(ACTION_TYPE);
    }
    public function getWeightArray(){
        return $this->weight_type_arr=unserialize(WEIGHT);
    }


    public function getCount($catId,$cityId,$lastId='',$condition='',$user_id=''){

        $sql ="SELECT  count( * ) AS total_records  FROM news_feed where 1";

        if($catId!='All'){
            $sql .= " AND find_in_set('".$catId."' , parent_cat_id )";
        }
        if($lastId!='' && $condition==''){
            $sql.= " AND news_feed_id > '".base64_decode($lastId)."'";
        }
        if($lastId!='' && $condition!=''){
            $sql.= " AND news_feed_id <= '".base64_decode($lastId)."'";
        }
        if(isset($cityId)&& !empty($cityId)){
            $sql.=" AND city_id IN (".$cityId.",0)";

        }

        if(isset($user_id)&& !empty($user_id)){
            $sql.=" AND user_id='".$user_id."'";
        }


         $sql .= " ORDER BY  news_feed_id DESC";

            $count = DB::select(DB::raw($sql));
            $count=(array)$count[0];

            return $count['total_records'];
    }

    public function getData($id,$cityId,$lastfeed='',$user_id=''){
        $sql ="SELECT * FROM news_feed WHERE 1";

        if($id!='All'){

            $sql .= " AND find_in_set('".$id."' , parent_cat_id )";
        }
        if(isset($cityId)&& !empty($cityId)){
            $sql.=" AND city_id IN (".$cityId.",0)";

        }
        if($lastfeed!=''){
            $sql.= " AND news_feed_id <= '".base64_decode($lastfeed)."'";
        }
        if(isset($user_id)&& !empty($user_id)){
            $sql.=" AND user_id='".$user_id."'";
        }

        // $sql .= " ORDER BY action_date DESC ,weight ASC";
        $sql .= " ORDER BY action_date DESC";

       // $sql .= " Limit {$this->offset},{$this->limit}";
        $sql .= " Limit 5";
        $sqlUpdateStmt=DB::select(DB::raw($sql));
        
        $objArray=new stdClass();
        $i=0;
        while (isset($sqlUpdateStmt[$i++])&&!empty($sqlUpdateStmt[$i])){
                 $row = $sqlUpdateStmt[$i];
                 $data[$row->news_feed_id]=$row;
                 if(empty($sqlUpdateStmt[$i])) break;

        }

        if(isset($data)&&!empty($data)) $objArray=$this->getObjectArrayData($data);
        else $objArray=array();

        return $objArray;

     }

    public function getObjectArrayData($data,$catID='',$cityId=''){
        $objArray=array();
        //_d($data);die;
        foreach($data AS $key=>$value){
            switch($value->action_type && $value->object_type){
                case($value->action_type=="status_message" && $value->object_type=="user_message"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailsJsonDecodeForMessage($value);
                    break;
                case($value->action_type=="new_content" && $value->object_type=="service_provider"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailJsonDecodeForSp($value);
                    break;
                case($value->action_type=="review" && $value->object_type=="service_provider"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailJsonDecodeForSpReview($value);
                    break;
                case($value->action_type=="new_content" && $value->object_type=="article"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailJsonDecodeForArticle($value);
                    break;
                case($value->action_type=="review" && $value->object_type=="article"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailJsonDecodeForArticleReview($value);
                    break;
                case($value->action_type=="new_content" && $value->object_type=="event"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailJsonDecodeForEvent($value);
                    break;
                case($value->action_type=="object_vote" && $value->object_type=="contest"):
                    $objArray[$value->news_feed_id]=$this->FetchDetailJsonDecodeForContest($value);
                    break;

            }
           }
//echo $html;exit;

        return $objArray;

    }
        public function FetchDetailJsonDecodeForSp($value){

        $data=$this->getWholeObjOfSp($value);
        return $data;
    }
    public function FetchDetailsJsonDecodeForMessage($value){
        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }
        $reviews=new Review();
        $spObj=new Service();
        $value->comment_count=$reviews->getReviewCountRe($value->object_id,'status_message');
        $value->like_count=$spObj->getRecommandationCount($value->object_id,'status_message');
        if($value->comment_count > 0){
            $value->comments= $reviews->getReviews($value->object_id,3,'status_message');
        }

        return $value;

    }

    public function FetchDetailJsonDecodeForSpReview($value){
        $data=$this->getWholeObjOfSpReview($value);
        return $data;

    }
    public function FetchDetailJsonDecodeForArticle($value){
        $data=$this->getWholeObjOfArticle($value);
        return $data;

    }
    public function FetchDetailJsonDecodeForArticleReview($value){
        $data=$this->getWholeObjOfArticleReview($value);
        return $data;

    }
    public function FetchDetailJsonDecodeForEvent($value){
        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }

        return $value;

    }
    public function FetchDetailJsonDecodeForContest($value){
        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }

        return $value;

    }
    public function getWholeObjOfSp($value){
        $spObj=new Service();
        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }
        $reviews=new Review();
        $value->review_count=$reviews->getReviewCountRe($value->object_id,'serviceprovider');
        $value->recommends=$spObj->getRecommandationCount($value->object_id);

        if($value->review_count > 0){
            $value->review= $reviews->getReviews($value->object_id);
        }

        return $value;

    }
    public function getWholeObjOfSpReview($value){

        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }
        $reviews=new Review();
        $value->comment_count=$reviews->getReviewCommentcount($value->action_id);
        $value->share_count=$reviews->share_count($value->action_id);
        $value->like_count=$reviews->getReviewLikecount($value->action_id);
        if($value->comment_count >0){
            $value->comments= $reviews->getObjectReplytoReviewsByReviewId($value->action_id,'news_feed');

        }


        return $value;

    }
    public function getWholeObjOfArticle($value){
        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }
        $reviews=new Review();
        $value->review_count=$reviews->getReviewCountRe($value->object_id,'article');

        if($value->review_count > 0){
            $value->review= $reviews->getReviews($value->object_id,3,'article');
        }
        return $value;
    }
    
    public function getWholeObjOfArticleReview($value){

        if(isset($value->detail_data)){
            $value->detail_data=json_decode($value->detail_data);
        }
        $reviews=new Review();
        $value->comment_count=$reviews->getReviewCommentcount($value->action_id);
        $value->share_count=$reviews->share_count($value->action_id);
        $value->like_count=$reviews->getReviewLikecount($value->action_id);

        if($value->comment_count > 0){
            $value->comment= $reviews->getObjectReplytoReviewsByReviewId($value->action_id,'news_feed','','3');
        }
        return $value;


    }

    public function getLastId($catId,$cityId){
        
        $sql="SELECT news_feed_id FROM news_feed WHERE 1 ";
        if($catId!='All'){
            $sql .= " AND find_in_set('".$catId."' , parent_cat_id )";
        }
        if(isset($cityId)&& !empty($cityId)){
            $sql.=" AND city_id IN(".$cityId.",0)";

        }
        $sql .= " ORDER BY  news_feed_id DESC LIMIT 1";

        $data=DB::select(DB::raw($sql));
        return $data[0]->news_feed_id;

    }

    public function getDataById($id, $op='='){

        $sql ="SELECT news_feed_id,user_id,object_type,object_id,action_type,action_id,parent_action_id,weight,city_id,cat_id,parent_cat_id,detail_data,action_date,insert_date
        FROM news_feed WHERE news_feed_id $op $id";

        $sql .= " ORDER BY action_date DESC ,weight ASC";


        $sql .= " Limit {$this->offset},{$this->limit}";

        $sqlUpdateStmt=DB::select(DB::raw($sql));
        
        $objArray=new stdClass();
        $i=0;
        while (isset($sqlUpdateStmt[$i++])&&!empty($sqlUpdateStmt[$i])){
                $row = $sqlUpdateStmt[$i];
                $row->detail_data=str_replace("babychakra.com", "babychakratest.com", $row->detail_data);
                 $data[$row->news_feed_id]=$row;
        }

        if(isset($data)&&!empty($data)) $objArray=$this->getObjectArrayData($data);
        else $objArray=array();

        return $objArray;

     }
    
}

?>
