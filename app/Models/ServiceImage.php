<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class ServiceImage extends SleepingOwlModel {

    protected $table = 'service_providers_images_new';

    public $timestamps = false;

    //either should have set or image should be there
    public function getNewImagePath($original_filepath=null,$service_provider_id=null){

        $extension = $original_filepath? pathinfo($original_filepath)['extension'] : 'jpg';
            
        $name = uniqid('service_');
        
        $s_id = $service_provider_id?$service_provider_id:$this->service_provider_id;
        
        $path = join('/',[ $s_id, $name.'.'.$extension]);

        return $path;

    }

    //if there is already an image
    public function getUniqueImagePath($original_filepath=null){

        if(!$original_filepath){
            if($this->image_url){
                $pInfo = pathinfo($this->image_url);
            }
        } else {
            $pInfo = pathinfo($original_filepath);
        }
        if(isset($pInfo)){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('service_');
        $path = join('/',[ $this->service_provider_id, $unique_id.'.'.$extension]);
        return $path;

    }


}