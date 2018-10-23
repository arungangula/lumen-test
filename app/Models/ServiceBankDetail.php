<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBankDetail extends Model
{

	const PAN_IMAGE_TITLE = 'pancard';
	const CHEQUE_IMAGE_TITLE = 'cheque';
	const OTHER_IMAGE_TITLE = 'other';

    protected $table = 'service_bank_details';

    public function images(){
    	return $this->hasMany('App\Models\ServiceBankDetailImages', 'service_bank_detail_id');
    }

    public function panImage(){
    	return $this->hasMany('App\Models\ServiceBankDetailImages', 'service_bank_detail_id')->where('image_title', ServiceBankDetail::PAN_IMAGE_TITLE)->orderby('id', 'desc')->take(1);
    }

    public function chequeImage(){
    	return $this->hasMany('App\Models\ServiceBankDetailImages', 'service_bank_detail_id')->where('image_title', ServiceBankDetail::CHEQUE_IMAGE_TITLE)->orderby('id', 'desc')->take(1);
    }

    public function otherImage(){
    	return $this->hasMany('App\Models\ServiceBankDetailImages', 'service_bank_detail_id')->where('image_title', ServiceBankDetail::OTHER_IMAGE_TITLE)->orderby('id', 'desc')->take(1);
    }

    public function service()
    {
        return $this->hasOne('App\Models\Service','id','service_id');
    }

    public function getServiceNameAttribute(){
        if(isset($this->service->name)){
            return $this->service->name." - ".$this->service->id;
        }
        else{
            return 'NA';
        }
    }
}
