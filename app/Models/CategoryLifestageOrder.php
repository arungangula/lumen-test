<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryLifestageOrder extends Model
{
    
    public function lifestage() {
    	return $this->belongsTo('App\Models\Lifestage');
    }

    public function category() {
    	return $this->belongsTo('App\Models\ServiceCategory');
    }
}
