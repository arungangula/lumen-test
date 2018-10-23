<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class Citycategory extends Model
{
    protected $table = "city_category_mapping";

    public $timestamps = false;

    public function city()
    {
    	return $this->belongsTo('App\Models\City', 'city_id');
    }

    public function category()
    {
    	return $this->belongsTo('App\Models\Servicecategory', 'category_id');
    }

    public function getCityNameAttribute()
    {
    	if( is_null($this->city) )
    	{
    		return 'Not set';
    	}
    	else
    	{
    		return $this->city->city_name;
    	}
    }

    public function getCategoryNameAttribute()
    {
    	if( is_null($this->category) )
    	{
    		return 'Not set';
    	}
    	else
    	{
    		return $this->category->category_name;
    	}
    }

    public function getCategoryLevelAttribute()
    {
    	if( is_null($this->category) )
    	{
    		return 'Not set';
    	}
    	else
    	{
    		return $this->category->level;
    	}
    }

    public function getServiceCountAttribute()
    {
        if($this->category_level != 0)
        {
            $serviceCount = Service::where('area_id', $this->city_id)->whereHas('category', function($query) {
                $query->where('category_id', $this->category_id);
            })->count();

            return $serviceCount;
        }
        else
        {
            return "Not applicable";
        }
    }
}
