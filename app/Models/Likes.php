<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model {

    protected $table = 'bc_services_likes';

    public $timestamps = false;

    public function user()
    {
      return $this->belongsTo('App\Models\User', 'user_id');
    }

}