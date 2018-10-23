<?php

namespace App\Models;
use App\Models\Contest;

use Illuminate\Database\Eloquent\Model;

class ContestEntry extends Model
{

    const IMAGE = "image";
    const VIDEO = "video";

    public $timestamps = false;
    protected $table = 'contest_entries';

    public function contest() {

      return $this->belongsTo('App\Models\Contest', 'contest_id');
    }

    public function votes() {

        return $this->hasMany('App\Models\ContestEntryVotes','contest_entry_id');
    }

    public function votedusers(){

        return $this->belongsToMany('App\Models\User','contest_entries_votes','contest_entry_id','user_id');
    }

    public function getContestnameAttribute(){
    	return $this->contest['contest_name'];
    }

    public function user() {

      return $this->belongsTo('App\Models\User', 'user_id');
    }


    public function getUsernameAttribute(){

      return $this->user['name'];
    }

    public function getUniqueImagePath($contest_id){
        $extension = 'jpg';
        $unique_id = uniqid('entry/'.$contest_id.'/contest_entry_');
        $path = join('/',[$unique_id.'.'.$extension]);
        return $path;
    }

    public function hashTags(){
        return $this->morphToMany('App\Models\HashTag', 'hash_taggable');
    }

}
