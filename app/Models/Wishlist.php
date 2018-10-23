<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Users wishlist(service providers)

class Wishlist extends Model {

    protected $table = 'bc_services_wishlist';

    public function article(){

      return $this->belongsTo('App\Models\Article', 'article_id');

    }

    public function service(){

      return $this->belongsTo('App\Models\Service', 'service_provider_id');

    }


}
