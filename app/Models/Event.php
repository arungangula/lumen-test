<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use \DateTime;
use App\Interfaces\INotifiable as Notifiable;

class Event extends Model implements Notifiable{

    protected $table = 'events_master';

    const AMA = "ama";
    const CONTEST = "contest";
    const OTHER = 'other';

    public $timestamps = false;

    protected $hidden = [  ];

    public function serviceProvider(){//Consider this for Camel Casing

      return $this->belongsTo('App\Models\Service','service_provider_id');
    }

    public function city(){

        return $this->belongsTo('App\Models\City','event_city');
    }

    public function location(){

        return $this->belongsTo('App\Models\Location','event_location');
    }

    public function registered_users(){

        return $this->belongsToMany('App\Models\User', 'event_registration', 'event_id', 'user_id');
        
    }

    public function registration(){
        return $this->hasMany('App\Models\Registration','event_id','id');
    }

    public function registrationCount(){
        return $this->registration()->selectRaw('event_id, count(*) as aggregate')->groupBy('event_id');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', FeedPost::EVENT);
    }

    public function likeCounts(){

        return $this->likes()->selectRaw('element_id, count(*) as aggregate')->groupBy('element_id');
    }

    //Notifiable Functions

    public function isChannelSupported($channel){
        return true;
    }

    public function isChannelAllowed($channel){
        return true;
    }

    public function getEmailAddress(){
        return 'lead@babychakra.com';
    }

    public function getMobileNumber() {
        return null;
    }

    public function getDevice($device_type){
        return null;
    }

    //End of notifiable functions

    public function getUniqueImagePath($original_filepath=null){

        if(!$original_filepath){
            $pInfo = pathinfo($this->event_image);
        } else {
            $pInfo = pathinfo($original_filepath);
        }
        $extension = isset($pInfo['extension'])? $pInfo['extension']:'jpg';
        $unique_id = uniqid('event_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }


    //vegeta
    public static function getIndexableEvents()
    {   
        $date  = new DateTime();
        $date  = $date->modify("-".config('elasticsearch.event_oldest_days')." day");
        $date  = $date->format('Y-m-d');

        $data = Event::with('serviceProvider')
                    ->where('event_end_date','>=',$date);
        return $data;
    }


}