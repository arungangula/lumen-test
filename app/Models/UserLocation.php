<?php
namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $table = 'users_location';

    public function location(){
    	
    	return $this->belongsTo('App\Models\Location','location_id','id');
    }
}