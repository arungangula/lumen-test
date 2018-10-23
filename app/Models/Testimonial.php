<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    
    public function user() {
    	return $this->belongsTo('App\Models\User');
    }

    public function city() {
    	return $this->belongsTo('App\Models\City');
    }
}
