<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GetStream\Stream\Client;

//GetStream model to work with the feedAPI
//getCategoryActivity() function is called in feedAPI calls

class GetStream extends Model {


	private static $instance;

	const API_KEY='zq3htgrtuepg';
	const API_SECRET='7xuqughzkfp84ssvjcqpuar2epvf28duj2e3ud9qhmmnf78b643pwr38mt87pfh5';

	const EXPECTING_BABY='EXPECTING_BABY';
	const BRAND_NEW_PARENT='BRAND_NEW_PARENT';
	const TODDLER_PARENT='TODDLER_PARENT';
	const ALL_FEED='ALL_FEED';

	const USER_MESSAGE='USER_MESSAGE';
	const SERVICE_PROVIDER='SERVICE_PROVIDER';
	const SERVICE_PROVIDER_REVIEW='SERVICE_PROVIDER_REVIEW';
	const ARTICLE='ARTICLE';
	const ARTICLE_REVIEW='ARTICLE_REVIEW';
	const EVENT='EVENT';
	const CONTEST='CONTEST';

	private $client;
	private $categories;

	public $offset;
	public $limit;

	const OFFSET=0;
	const LIMIT=4;

	public $logFile;

	public function __construct(){
		$this->client = new Client(self::API_KEY, self::API_SECRET);
		$this->categories=array(
							1 => self::EXPECTING_BABY,
							2 => self::BRAND_NEW_PARENT,
							3 => self::TODDLER_PARENT,
							);

	}

	public function instantiate(){
		//Just instantiate four feeds
		foreach ($this->categories as $catId => $catName) {
			$feed=$this->client->feed('user', $catName);
		}
		$feed=$this->client->feed('user', self::ALL_FEED);
	}


	public static function getInstance() {
		
		if (!self::$instance instanceof self) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function addUserActivity($userId, $activity, $batch=false){
		$feed=$this->client->feed('user', $userId);
		if($batch)
			$feed->addActivities($activity);
		else
			$feed->addActivity($activity);
	}

	public function addActivityToCategory($catName, $activity, $cityId='', $batch=false){
		// echo "Adding to " . $catName . "/" . $cityId . "\n";
		if(empty($cityId))
			$feed=$this->client->feed('user', $catName);
		else{
			// echo "Checking\n";
			$feed=$this->client->feed('user', $catName . "_" .$cityId);
			$result=$feed->followers();
			// print_r($result);
			if(sizeof($result['results'])==0){
				//First Time feed
				// echo "First time feed\n";
				$catFeed=$this->client->feed('user', $catName);
				$catFeed->followFeed('user' , $catName . "_" .$cityId);
			}
		}
		// echo "Adding\n";
		if($batch)
			return $feed->addActivities($activity);
		else
			return $feed->addActivity($activity);
	}

	public function removeUserActivity($userId, $activityId){
		$feed=$this->client->feed('user', $userId);
		$feed->removeActivity($activityId);
	}

	public function removeCategoryActivity($catName, $activityId, $cityId='', $foreign_id=false){

		$ext = empty($cityId) ? '' : ('_' . $cityId); 
		
		$feed=$this->client->feed('user', $catName . $ext);

		$feed->removeActivity($activityId, $foreign_id);

	}
	public function removeCategoryActivityWrapper($catName, $activityId, $cityId='', $foreign_id=false, $wait=1, $print=false){
		try{
			if($print){
				echo "Trying to remove from " . $catName;

				if(!empty($cityId))
					echo "_" . $cityId;

				echo " (" . $wait . "): ";
			}
			$this->removeCategoryActivity($catName, $activityId, $cityId, $foreign_id);
			if($print)
				echo "Success \n";
		}
		catch(Exception $e){
			if($print){
				echo "Falling to fail safe\n";
				if($this->logFile){
					fwrite($this->logFile, "Trying to remove from " . $catName .  ( empty($cityId) ? '' : ('_' . $cityId) ) );
					fwrite($this->logFile, $e);
				}

			}
			sleep($wait);
			$this->removeCategoryActivityWrapper($catName, $activityId, $cityId, $foreign_id, $wait+5, $print);

		}

	}

	public function getUserActivity($userId ,$offset=self::OFFSET, $limit = self::LIMIT, $options=[]){
		// $this->client=new Client(self::API_KEY , self::API_SECRET);
		$feed=$this->client->feed('user', $userId);
		return $feed->getActivities($offset, $limit , $options);


	}

	public function getCategoryActivity( $catId, $cityId='' ,$offset=self::OFFSET, $limit = self::LIMIT, $options=[]){
		if(empty($cityId))
			$ext='';
		else
			$ext="_" . $cityId;

		// echo "<br>GET<br>catId=$catId<br>cityId=$cityId<br>";

		if($catId == 'All')
			$feed=$this->client->feed('user', self::ALL_FEED . $ext);
		else
			$feed=$this->client->feed('user', $this->categories[$catId] . $ext);

		return $feed->getActivities($offset, $limit , $options);

	}

	public function getCategoryActivityWrapper($catId, $cityId='' ,$offset=self::OFFSET, $limit = self::LIMIT, $options=[], $wait=1){

		try{
			return $this->getCategoryActivity($catId, $cityId ,$offset, $limit, $options);
		}
		catch(Exception $e){
			sleep($wait);
			return $this->getCategoryActivityWrapper($catId, $cityId ,$offset, $limit, $options, $wait+5);
		}

	}

	public function getCategoryActivityFromName( $catName ,$offset=self::OFFSET, $limit = self::LIMIT, $options=[]){
		$feed=$this->client->feed('user', $catName);
		return $feed->getActivities($offset, $limit , $options);

	}

	public function deleteUserFeed($userId){
		$feed=$this->client->feed('user', $userId);
		$feed->delete();
	}

	public function getFollowing($feed, $wait){
		try{
			return $feed->following();
		}
		catch(Exception $e){
			sleep($wait);
			return $this->getFollowing($feed, $wait+5);
		}
	}

	public function unFollow($feed, $name, $wait){

		try{
			$feed->unfollowFeed('user', $name);
		}
		catch(Exception $e){
			sleep($wait);
			$this->unFollow($feed, $name, $wait+5);
		}

	}

	public function del($feed, $wait){
		try{
			$feed->delete();
		}
		catch(Exception $e){
			sleep($wait);
			$this->del($feed, $wait+5);
		}
	}

	public function deleteCategoryFeed($catName, $tab=""){
		echo $tab . "Delete Process " . $catName . "\n";
		$feed=$this->client->feed('user', $catName);

		echo $tab . "Getting to followers\n";
		$result=$this->getFollowing($feed, 1);
		// print_r($result);

		foreach ($result['results'] as $key => $value) {
			$name=explode(":", $value['target_id'])[1];
			$following=$this->client->feed('user', $name);
			$this->unFollow($feed, $name, 1);
			$this->deleteCategoryFeed($name, $tab . "\t");
		}

		echo $tab . "Deleting " . $catName . "\n\n";
		$this->del($feed, 1);
	}


	public function deleteAllFeeds(){
		foreach ($this->categories as $catId => $catName) {
			$this->deleteCategoryFeed($catName);
		}
		$this->deleteCategoryFeed(self::ALL_FEED);

	}
	public function clean($string){
		$string =str_replace(' ', '-', $string);
		$string=preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		$string = str_replace('-', ' ', $string);
		return $string;
	}

	public function addInfo($value){
		$value->time=gmdate("Y-m-d\Th:m:s", $value->action_date);
		// $value->time=str_replace(' ', 'T', gmdate("Y-m-d h:m:s", time()));
		// echo ":" .  $value->time . ":"; 
		$value->object_id_t=$value->object_id;
        switch($value->action_type && $value->object_type){
            case($value->action_type=="status_message" && $value->object_type=="user_message"):
                $value->actor=$this->clean($value->detail_data->user_data->f_name);
                $value->verb='status message';
                $value->object=$this->clean($value->action_id);
                $value->foreign_id=self::USER_MESSAGE . $value->object_id;
                break;

            case($value->action_type=="new_content" && $value->object_type=="service_provider"):
                $value->actor='NONE';
                $value->verb='service provider';
                $value->object=$this->clean($value->detail_data->name);
                $value->foreign_id=self::SERVICE_PROVIDER . $value->object_id;
                break;

            case($value->action_type=="review" && $value->object_type=="service_provider"):
            	$value->actor=$this->clean($value->detail_data->user->f_name);
            	$value->verb='review';
            	$value->object=$this->clean($value->detail_data->sp->name);
                $value->foreign_id=self::SERVICE_PROVIDER_REVIEW . $value->object_id;
                break;

            case($value->action_type=="new_content" && $value->object_type=="article"):
            	$value->actor='NONE';
            	$value->verb='article';
            	$value->object=$this->clean($value->detail_data->title);
                $value->foreign_id=self::ARTICLE . $value->object_id;
                break;

            case($value->action_type=="review" && $value->object_type=="article"):
            	$value->actor=$this->clean($value->detail_data->user->f_name);
            	$value->verb='review';
            	$value->object=$this->clean($value->detail_data->article_data->article_title);
                $value->foreign_id=self::ARTICLE_REVIEW . $value->object_id;
                break;

            case($value->action_type=="new_content" && $value->object_type=="event"):
            	$value->actor='NONE';
            	$value->verb='event';
            	$value->object=$this->clean($value->detail_data->event_name);
                $value->foreign_id=self::EVENT . $value->object_id;
                break;

            case($value->action_type=="object_vote" && $value->object_type=="contest"):
            	$value->actor=$this->clean($value->detail_data->user->f_name);
            	$value->verb='contest';
            	$value->object=$this->clean($value->detail_data->contest_name);
                $value->foreign_id=self::CONTEST . $value->object_id;
                break;

        }
        return $value;
	}

	public function addActivityToCategoryWrapper($catName , $activity, $wait, $cityId='', $batch=false, $print=true){

		try{
				if($print){
					echo "Trying to add to " . $catName;
					if(!empty($cityId))
						echo "_" . $cityId;
					if($batch)
						echo " [" . sizeof($activity) . "]" ;
					echo " (" . $wait . "): ";
				}
				$this->addActivityToCategory($catName, $activity, $cityId, $batch);
				if($print)
					echo "Success \n";
		}
		catch(Exception $e){
				if($print){
					echo "Falling to fail safe\n";
					if($this->logFile){
						fwrite($this->logFile, print_r($activity, true));
						fwrite($this->logFile, $e);
					}
				}
				sleep($wait);

				$this->addActivityToCategoryWrapper($catName, $activity, $wait + 5, $cityId, $batch);
		}

	}

	public function updateFeed($object_id, $object_type, $action_type, $isActionId=false){
		$newsfeedObj= new NewsFeed();
		$newsfeedObj->limit=100;
		$newsfeedObj->offset=0;

		$this->logFile=fopen('FeedLog' , 'w');

		// echo $object_id . " | " . $object_type . " | " .  $action_type . "\n";

		$data=$newsfeedObj->getDataByType($object_id, $object_type, $action_type, $isActionId);

		if(!$data)
			return;

		// print_r($data);
		$print=false;

		foreach ($data as $key => $value) {
			$value=$this->addInfo($value);

			if($value->parent_cat_id){
				$catIds=explode(",", $value->parent_cat_id);

				foreach ($catIds as  $catId) {
					$this->removeCategoryActivityWrapper($this->categories[$catId], $value->foreign_id, $value->city_id, true, 1 , $print);
					$this->addActivityToCategoryWrapper( $this->categories[$catId], $value, 1 , $value->city_id, false, $print);
				}
			}
			$this->removeCategoryActivityWrapper(self::ALL_FEED, $value->foreign_id, $value->city_id, true, 1, $print);
			$this->addActivityToCategoryWrapper( self::ALL_FEED , $value, 1, $value->city_id, false, $print);
			

		}
		fclose($this->logFile);
	}

	public function getReviewTypeByReviewId($reviewId){

		$sql="SELECT review_type FROM sp_reviews WHERE id=$reviewId";

		$dbObj= DBConn::getInstance('READ');
		$sqlStmt= $dbObj->prepare($sql);
		$flag= $sqlStmt->execute();
		$data= $sqlStmt->fetchAll(PDO::FETCH_ASSOC);

		// print_r($data);
		// exit;

		$review_type=$data[0]['review_type'];

		if($review_type=='serviceprovider')
			$review_type='service_provider';

		return $review_type;
	}

	public function updateReviewInFeedByID($reviewId){


		$this->updateFeed($reviewId, $this->getReviewTypeByReviewId($reviewId), 'review', true);
	}

	public function getReviewIdByReplyId($replyId){
		$sql="SELECT sp_review_id FROM sp_replytoreview WHERE id=$replyId";

		$dbObj= DBConn::getInstance('READ');
		$sqlStmt= $dbObj->prepare($sql);
		$flag= $sqlStmt->execute();
		$data= $sqlStmt->fetchAll(PDO::FETCH_ASSOC);

		// echo "Review ID=" . $data[0]['sp_review_id'] . "\n";

		return $data[0]['sp_review_id'];

	}

	public function updateReviewInFeedByReplyId($replyId){

		$this->updateReviewInFeedByID($this->getReviewIdByReplyId($replyId));
	}

	public function updateReviewedObjectInFeedById($id, $reviewId=''){
		$review_type='service_provider';
		if(!empty($reviewId))
			$review_type=$this->getReviewTypeByReviewId($reviewId);
		$this->updateFeed($id, $review_type ,'new_content' );
	}

	//Functions for script
	public function copyData($batch=100){
		$this->logFile=fopen('GetStreamLog', 'w');
		$news=new NewsFeed();
		$count=$news->getCount('All' ,'' , '');
		echo "Count=" . $count . "\n";

		$offset=0;
		$limit=$batch;
		$i=1;
		$news->limit=$limit;
		while(true)
		{
			if($offset >= $count)
				break;

			$news->offset=$offset;
			$data=$news->getData('All' , '');



			foreach ($data as $key => $value) {

				echo $i . "/" . $count  . "\n";
	
				$value = $this->addInfo($value);


				if($value->parent_cat_id){
					$catIds=explode(",", $value->parent_cat_id);
					foreach ($catIds as  $catId) {
						$this->addActivityToCategoryWrapper($this->categories[$catId] ,$value, 1, $value->city_id);
						// sleep(1);
					}
				}
				$this->addActivityToCategoryWrapper(self::ALL_FEED, $value, 1, $value->city_id);
				// sleep(1);

				$i++;

			}
			$offset += $batch;
		}

	}

	public function copyDataBatch($batch=100, $id=''){
		$news=new NewsFeed();
		$this->logFile=fopen('GetStreamLog', 'w');
		$count=$news->getCount('All' ,'' , $id);
		echo "Count=" . $count . "\n";
		$runs=ceil($count/$batch);
		echo "Runs=" . $runs . "\n";

		$offset=0;
		$limit=$batch;
		$i=1;
		$news->limit=$limit;
		while(true)
		{
			if($offset >= $count)
				break;


			echo "\n\n" . $i . "/" . $runs  . "\n";

			$news->offset=$offset;
			if(empty($id))
				$data=$news->getData('All' , '');
			else
				$data=$news->getDataById($id, '>');

			$arr=array();


			foreach ($data as $key => $value) {

				$value = $this->addInfo($value);

				// $val=json_decode(json_encode($value) , true);

				if($value->parent_cat_id){
					$catIds=explode(",", $value->parent_cat_id);
					foreach ($catIds as  $catId) {
						$arr[$catId][$value->city_id][] = $value;
					}
				}
				$arr['All'][$value->city_id][]=$value;

			}

			foreach ($arr as $catId => $catResults) {
				foreach ($catResults as $cityId => $cityResult) {
					$this->addActivityToCategoryWrapper(($catId=='All') ? self::ALL_FEED : $this->categories[$catId] , $cityResult, 1, $cityId, true);
				}
			}

			$offset += $batch;
			$i++;

		}

	}

}