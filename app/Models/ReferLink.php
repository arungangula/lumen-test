<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\base62;

class ReferLink extends Model {

    protected $table = 'refer_links';

    const MOM_INVITE = 'mom_invite';

    const REFER_INVITE = 'Share';

    public function getParamsAttribute($value){

        return json_decode($value);
    }


    public function setParamsAttribute($params){

        $this->attributes['params'] = json_encode($params,JSON_FORCE_OBJECT);

    }

    public static function getLink($link){
      if(strlen($link)<15){
        return ReferLink::find(base62::decode($link));
      } else {
        return null;
      }
    }


    public static function generateNewLink($user_id,$params){


          $refer_link = new ReferLink;
          $refer_link->lng_url = isset($params['lng_url'])?$params['lng_url']:config('referral.long_url');
          $refer_link->feature = isset($params['feature'])?$params['feature']:null;
          $refer_link->channel = isset($params['channel'])?$params['channel']:'unknown';
          $refer_link->device = isset($params['device'])?$params['device']:'android';
          $refer_link->utm_source = isset($params['utm_source'])?$params['utm_source']:null;
          $refer_link->utm_campaign = isset($params['utm_campaign'])?$params['utm_campaign']:null;
          $refer_link->user_id = $user_id;
          $params = array_diff_key($params, array_flip(['lng_url','feature','channel','device','utm_campaign' ,'utm_source']));
          $refer_link->params = $params;
          $refer_link->save();
          $refer_link->short_url =  base62::encode($refer_link->id);
          $refer_link->save();
          return $refer_link->short_url;
    }

}