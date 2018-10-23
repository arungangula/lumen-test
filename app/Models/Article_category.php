<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article_category extends Model {
	//Model to establish Article model - category relationship
    protected $table = 'bc_categories';

    public $timestamps = false;

    public function parent(){

      return $this->belongsTo('App\Models\Article_category','parent_id');
    }

}
