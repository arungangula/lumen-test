<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTip extends Model
{
    //
    protected $table = 'daily_tips';

    public function article(){

    	// dd($this->hasOne('App\Models\Article', 'article_id')->get()->toArray());
        return $this->hasOne('App\Models\Article', 'id', 'article_id');
    }


    public function collection(){

        return $this->hasOne('App\Models\Collection', 'id','collection_id');
    }


    public function serviceprovider(){

        return $this->hasOne('App\Models\Service', 'id','service_provider_id');
    }


    public function subcategory(){

        return $this->hasOne('App\Models\ServiceCategory', 'id', 'subcategory_id');
    }
}
