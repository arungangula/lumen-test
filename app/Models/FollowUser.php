<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class FollowUser extends Model
{

    protected $table = "bc_follow_user";

    public function user() {
      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function lifestage(){
        return $this->belongsTo('App\Models\Lifestage', 'lifestage_id');
    }

    public function createdUser() {
    	return $this->belongsTo('App\Models\User', 'created_by');
    }
}
