<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class Invoice extends SleepingOwlModel {

    protected $table = 'sp_reviews';

    protected $hidden = [ 'created_at','updated_at' ];

    public function user() {

      return $this->belongsTo('App\Models\User', 'user_id');
    }

}