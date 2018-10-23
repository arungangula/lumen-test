<?php
namespace App\Models;

use Validator;

use Illuminate\Database\Eloquent\Model;

class ReferEvent extends Model {

    protected $table = 'refer_event';

    public $primaryKey = 'event_code';


    public static function createNewEvent($event_code,$params){

      if(self::validateEventCode($event_code)){

          $re = new ReferEvent;
          $re->event_code = $event_code;
          $re->referrer_points = isset($params['referrer_points'])?$params['referrer_points']:0;
          $re->friend_points = isset($params['friend_points'])?$params['friend_points']:0;
          $re->frequency = isset($params['frequency'])?$params['frequency']:0;
          $re->save();

          return $re;
      } 

      return null;


    }

    public static function validateEventCode($event_code){

          $v = Validator::make(['event_code' => $event_code ], [
                            'event_code' => 'alpha_dash|required|between:5,30',
            ]);

          return !$v->fails();

    }
}