<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionArticleMapping extends Model
{
    protected $table = 'bc_content_collection_mapping';

    public function article(){
      return $this->belongsTo('App\Models\Article','content_id');;
    }

    public function userCollection(){
      return $this->belongsTo('App\Models\UserCollection','collection_id');;
    }
}
