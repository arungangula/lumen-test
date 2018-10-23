<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = "newsletters";

    public function articles() {

    	return $this->belongsToMany('App\Models\Article', 'newsletter_mapping', 'newsletter_id', 'element_id')->where('element_type', 'article');
    }

    public function recipes() {

        return $this->belongsToMany('App\Models\Article', 'newsletter_mapping', 'newsletter_id', 'element_id')->where('element_type', 'recipe');
    }

    public function momstars() {

        return $this->belongsToMany('App\Models\User', 'newsletter_mapping', 'newsletter_id', 'element_id')->where('element_type', 'momstar');
    }

    public function services() {

    	return $this->belongsToMany('App\Models\Service', 'newsletter_mapping', 'newsletter_id', 'element_id')->where('element_type', 'service');	
    }

    public function events() {

    	return $this->belongsToMany('App\Models\Event', 'newsletter_mapping', 'newsletter_id', 'element_id')->where('element_type', 'event');	
    }

    public function reviews() {

        return $this->belongsToMany('App\Models\Review', 'newsletter_mapping', 'newsletter_id', 'element_id')->where('element_type', 'review');   
    }

    public function getUniqueImagePath($original_filepath=null){

        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('newsletter_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;

    }
}
