<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandContentElementMapping extends Model
{
    public function brandElement() {
        return $this->belongsTo('App\Models\BrandElement', 'brand_element_id');
    }
}
