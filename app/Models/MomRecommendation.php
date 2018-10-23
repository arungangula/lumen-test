<?php
namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class MomRecommendation extends Model {
    
    protected $table = 'moms_recommendations';

     public $timestamps = false;

    public function getUniqueImagePath($original_filepath=null){

        if(!$original_filepath){
            $extension = '.jpg';
        } else {
            $pInfo = pathinfo($original_filepath);
            $extension = $pInfo['extension'];
        }
        $unique_id = uniqid('review_');
        $path = join('/',[ 'new', $unique_id.'.'.$extension]);
        return $path;
    }
    
    public function user() {

      return $this->belongsTo('App\Models\User', 'user_id');
    }


    public function getUsernameAttribute(){

      //dd($this->user['name']);
      return $this->user['name'];
    }

    public function getEmailAttribute(){

        return $this->user['email'];
    }

    public function getMobileAttribute(){

        return $this->user['mobile_number'];
    }

    public function getCreatedAttribute(){
        //dd(date("d-m-y H:i:s", $this->created_at));

        return date("d-m-y H:i:s", $this->created_at);
    }

    
}


