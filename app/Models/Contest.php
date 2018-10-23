<?php

namespace App\Models;

use App\Models\ContestEntry;

use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    protected $table = 'contest_master';

    public $timestamps = false;

    public function contestEntry(){

    	return $this->hasMany('App\Models\ContestEntry', 'contest_id');
    }

    public function contestentries(){

        return $this->hasMany('App\Models\ContestEntry', 'contest_id');
    }

    public function getUniqueImagePath($original_filepath=null){
		$pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('contest_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;

    }

    public static function currentContest($id = null){
        $currentContest = Contest::whereRaw('DATE(start_time) <= date(now())')->whereRaw('DATE(end_time) >= date(now())');
        if($id) {
            $currentContest->where('id', $id);
        }
        $currentContest = $currentContest->first();
        if($currentContest){
            return $currentContest->toArray();
        }
        else{
            return false;
        }
    }

    public static function activeContests(){
        return Contest::whereRaw('DATE(start_time) <= date(now())')->whereRaw('DATE(end_time) >= date(now())')->get();
    }

    public static function getLatestContest(){
        $latestContest = Contest::orderBy('id', 'desc')->take(1)->get()->toArray()[0];
        return $latestContest;
    }

    public function hashTags(){
        
        return $this->morphToMany('App\Models\HashTag', 'hash_taggable');
    }

}
