<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model {

    protected $table = 'services_city_zone';

    public $timestamps = false;

    protected $hidden = [ 'created_by','updated_by','created_at','updated_at','parent_id','state_id','country_id' ];

    public function locations() {

         return $this->hasMany('App\Models\Location','zone_id');
    }

    public function city() {

        return $this->belongsTo('App\Models\City','city_id');
    }


}