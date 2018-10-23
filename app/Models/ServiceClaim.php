<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;
use App\Models\ListYourBusiness;

class ServiceClaim extends Model {

    protected $table = 'service_claims';

    /*
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->integer('service_id')->unsigned();
    $table->timestamps();
    */

    public function user(){

        return $this->belongsTo('App\Models\User','user_id');
    }

    public function service(){

        return $this->belongsTo('App\Models\Service','service_id');
    }

    public function getVerifyAttribute(){
        
        if($this->service == null){
            return '0';
        }
        else{
            return $this->service->verified;
        }
	}

	public function getServiceNameAttribute(){
        if($this->service == null){
            $ls = ListYourBusiness::find($this->list_your_business_id);
            if($ls){
                return $ls->name;
            }
            return '';
        }
        else{
            return $this->service->name;
        }
    
	}

/*    public function getServiceIdAttribute(){

        if(isset($this->service_id)){
            return $this->service_id;
        }
        return 'New Service';
    }*/

    public function getIdServiceAttribute(){
        if($this->service_id == 0){
            return 'New Service';
        }

        return $this->service_id;
    }

	public function getUserNameAttribute(){
        if($this->user == null){
            return false;
        }
        else{
            return $this->user->name;
        }	
	}
}