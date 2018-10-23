<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedEditLog extends Model
{
    protected $table = 'feed_edit_log';
    protected $hidden = [ 'updated_at' ];

    public function saveEditLog($element_id, $element_type, $text, $image, $editTime = null){

    	if($text == ''){
    		return true;
    	}

    	$editLog = new Self;
		$editLog->element_id 	= $element_id;
		$editLog->element_type 	= $element_type;
		$editLog->text 			= $text;
		$editLog->image 		= $image;
		if($editTime){
			$editLog->created_at = $editTime;
		}
		$editLog->save();

		return true;
    }
}
