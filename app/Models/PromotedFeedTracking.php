<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotedFeedTracking extends Model {

    protected $table = 'promoted_feed_tracking';

    public function user()
    {
      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function promotion()
    {
      return $this->belongsTo('App\Models\PromotedFeed', 'promotion_id');
    }

}