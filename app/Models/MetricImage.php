<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class MetricImage extends SleepingOwlModel {

    protected $table = 'metric_images';


    //either should have set or image should be there
    public function getNewImagePath($original_filepath=null,$metric_id=null){

        $extension = $original_filepath? pathinfo($original_filepath)['extension'] : 'jpg';
        $name = uniqid('metric_');        
        $_metric_id = $metric_id ? $metric_id : $this->metric_id ;
        $path = join('/',[ $_metric_id, $name.'.'.$extension]);
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
        $unique_id = uniqid('metric_');
        $path = join('/',[ $this->metric_id, $unique_id.'.'.$extension]);
        return $path;
    }
}