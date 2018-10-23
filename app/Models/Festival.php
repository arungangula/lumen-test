<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    protected $table = "bc_festival";

    public function articles() {

        return $this->belongsToMany('App\Models\Article', 'bc_festival_article_mapping', 'festival_id', 'article_id')->where('state',1)->orderBy('created','desc');
    }
}
