<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserBadge extends Model
{
    protected $table = 'user_badges';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function badge() {
        return $this->belongsTo('App\Models\Badge', 'badge_id');
    }
}
