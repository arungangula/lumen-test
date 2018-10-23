<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class ServiceIntegration extends Model {

    protected $table = 'service_integrations';

     //Set Integration Params with Hash Attribute
    public function setIntegrationParamsAttribute($object){

        $type = gettype($object);
        if($type == 'object' || $type == 'array'){
          $this->attributes['integration_params'] = json_encode($object,JSON_FORCE_OBJECT);
        }

    }

     //Set Integration Params with Hash Attribute
    public function getIntegrationParamsAttribute($jsondata){

        return json_decode($jsondata, true);

    }

    public function service() {

      return $this->belongsTo('App\Models\Service','service_id');

    }

}