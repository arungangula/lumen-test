<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

//Model for Service provider categories relationship with service providers

class Service_category extends SleepingOwlModel {

    protected $table = 'service_provider_category_mapping_new';

    public $timestamps = false;

    public function lifestages()
    {
        return $this->hasMany('App\Models\CategoryLifestageMapping','subcategory_id','category_id');
    }

    public function subCategory(){
    	return $this->belongsTo('App\Models\ServiceCategory','category_id');
    }

    public static function brands($categoryIds) {
    	
    	return self::whereIn('category_id', $categoryIds)
			->join('bc_services_providers_new', 'service_provider_category_mapping_new.service_provider_id', '=', 'bc_services_providers_new.id')
			->distinct('service_provider_category_mapping_new.service_provider_id')
			->where('bc_services_providers_new.published', 1)
            ->orderby('bc_services_providers_new.name', 'asc')
			->lists('bc_services_providers_new.id', 'bc_services_providers_new.name');
    }

    public static function brandsFromCategoryPackages($categoryIds) {

        return CategoryPackage::whereIn('category_packages.subcategory_id', $categoryIds)
            ->join('bc_services_providers_new', 'category_packages.service_id', '=', 'bc_services_providers_new.id')
            ->distinct('category_packages.service_id')
            ->where('bc_services_providers_new.published', 1)
            ->groupBy('category_packages.service_id')
            ->orderBy('bc_services_providers_new.name', 'asc')
            ->lists('bc_services_providers_new.id', 'bc_services_providers_new.name');
    }

    public static function brandsForFilters($categoryIds) {

    	$brands = self::brandsFromCategoryPackages($categoryIds);
    	return array_map(function($key, $value) {
            return ['label' => $key, 'value' => (string)$value];
        }, array_keys($brands->all()), array_values($brands->all()));
    }
}