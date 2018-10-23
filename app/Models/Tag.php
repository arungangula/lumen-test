<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    
    public function packages() {

    	return $this->morphedByMany('App\Models\Package', 'taggable');
    }
}
