<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Babies extends Model {

    protected $table = 'babies';

    public $timestamps = true;

    protected $fillable = array('parent_id');

    protected $hidden = array('created_at','updated_at');

    //To get babies of a parent
    public static function getBabiesOfParent($parent_id){
        return Babies::where('parent_id','=',$parent_id)->select('name','birth_date','gender')->get()->toArray();
    }

    public function getBabiesCurrentMonth() {
    	$babies_birth_date = new Carbon($this->birth_date);
    	return $babies_birth_date->diffInMonths(Carbon::now()) + 1;
    }
    
    public function parent(){
      return $this->hasOne('App\Models\User', 'id', 'parent_id');
    }

}
