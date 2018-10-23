<?php

namespace App\Models;

use DB;
use Cache;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wishlist;
use App\Scopes\ReviewTypeScope;

class PackageReview extends Review {


    protected static function boot() {
        parent::boot();
        if(get_called_class() == 'App\Models\PackageReview') {
            static::addGlobalScope(new ReviewTypeScope(Review::TYPE_PACKAGE));    
        }
    }

}