<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model {

    //Depricated use Brand Referral in marketing group
    protected $table = 'bc_user_referral';

    public function user() {
    	$this->belongsTo("App\Models\User", "user_id");
    }

    public function getUniqueImagePath($original_filepath=null) {
        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('user_referral_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }

}