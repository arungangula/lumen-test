<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBankDetailImages extends Model
{
    protected $table = 'service_bank_detail_images';

    public function getUniqueImagePath($extension=null){

		if(!$extension){
			$extension = 'jpg';
		}
        $unique_id = uniqid('bank_detail_images_');
        $path = $unique_id.'.'.$extension;
        return $path;

    }
}
