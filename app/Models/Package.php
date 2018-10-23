<?php
namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use App\Feed\FeedCache;
use SleepingOwl\Models\SleepingOwlModel;
use App\Models\FeedPost;

use DB;

class Package extends SleepingOwlModel {

    protected $table = 'packages';

    const PACKAGE_TYPE_SERVICE = 'service';
    const PACKAGE_TYPE_PRODUCT = 'product';

    protected static function boot() {
	    parent::boot();
	    static::addGlobalScope(new OrderScope('sort_order', 'asc'));
	}

    public function service()
    {
      return $this->belongsTo('App\Models\Service','service_id');
    }

    public function packagegroups()
    {
      return $this->belongsToMany('App\Models\PackageGroup','package_group_mapping', 'package_id', 'package_group_id');
    }

    public function serviceCategories()
    {
        return $this->belongsToMany('App\Models\ServiceCategory', 'package_category_mapping', 'package_id', 'category_id');
	}

	public function timings()
    {
        return $this->hasMany('App\Models\PackageTiming', 'package_id');
	}

	public function getpackageStatusAttribute() {


		return ($this->status) ? config('admin.packageStatus')[$this->status] : 'Not Specified';
	}

	public function getserviceNameAttribute() {

		return ($this->service) ? "{$this->service->name}" : 'no service tagged';
	}

    public function packageItems() {

      return $this->hasMany('App\Models\PackageItem')->where('status', 'active');
    }

    public function packageItemProperties() {

      return $this->hasMany('App\Models\PackageItemProperty');
    }

    public function images(){

      return $this->hasMany('App\Models\PackageImage', 'package_id');
    }

    public function tags(){

      return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    public function getUniqueImagePath($extension=null)
    {
    	if(!$extension)
    		$extension = 'jpg';
    	$unique_id = uniqid('package_');
    	$path = $unique_id.'.'.$extension;
    	return $path;
    }

    public function getDisplayName() {
        $advance_percent = $this->service->advance_percent;
        if($advance_percent < 100.0) {
          return $this->name." - Advance ($advance_percent%)";
        }
        return $this->name;
    }

    public function getShippingCharge() {
        if($this->override_service_shipping_charge == 1) {
          $shipping_charge = doubleval($this->shipping_charge);
        } else {
          $shipping_charge = doubleval($this->service->shipping_charge);
        }
        return $shipping_charge;
    }
    public function supportsAdvancePayment() {
        return $this->service->advance_percent < 100.0;
    }
    public function getPackagePrice() {
        return $this->price * ($this->service->advance_percent/100);
    }

    public function getCommissionPercentage() {
        $price = $this->price;
        if($this->service->override_system_commission_percent == 1) {
          if($price > $this->service->commission_cutoff) {
            $percent = ($this->service->commission_percent_above_cutoff/100);
          } else {
            $percent = ($this->service->commission_percent/100);
          }
        } else {
          if($price < 9999) {
              $percent = 0.1;
          } else {
              $percent = 0.15;
          }
        }
        return $percent;
    }

    public function getCommissionAmount() {
    	return  $this->price * $this->getCommissionPercentage();
    }

    public function getProperties($id = null) {
        if($this->id){
          $id = $this->id;
    }

    $package_cached_data = FeedCache::fetchByPostId('package:'.$id);
        if(isset($package_cached_data['package_properties'])){
          return json_decode($package_cached_data['package_properties'], true);
        }
        return [];
    }

    public function getSisterPackages() {
        $package_cached_data = FeedCache::fetchByPostId('package:'.$this->id);
        if(isset($package_cached_data['sister_packages'])){
            return json_decode($package_cached_data['sister_packages'], true);
        }
        return [];
    }

    public static function maxShippingCharge($package_ids, $service_ids) {
        $package_charges = self::whereIn('id', $package_ids)
          ->whereIn('service_id', $service_ids)
          ->where('override_service_shipping_charge', 1)
          ->lists('shipping_charge')->all();

        // When all the shipping charge are overridden
        if(count($package_charges) < count($package_ids)) {
          $service_charges = Service::whereIn('id', $service_ids)->lists('shipping_charge')->all();
        } else {
          $service_charges = [];
        }

        return max(array_merge([0], $package_charges, $service_charges));
    }

    public function syncCategoryPackage() {
        CategoryPackage::where('package_id', $this->id)->delete();
        if($this->status != 2 || $this->service_id == 0 || !$this->serviceCategories) {
          return;
        }
        $dataToInsert = [];
        $rating = 0;
        $cityId = 0;
        $status = 0;
        if($this->service) {
            if($this->service->default_rating){
                $rating = $this->service->default_rating;
            }
            else{
                $rating = (isset($this->service->service_rating) && $this->service->service_rating) ? format_rating($this->service->service_rating->rating) : 0;
            }
                
            $rating = number_format((float)$rating, 1, '.', '');

            if($this->service->online_flag == 0) {
                $cityId = $this->service->city_id;
            }

            if(($this->status == 2) && $this->service->published) {
                $status = 1;
            }
        }

        // $price  = ($this->display_price > 0) ? money_format("%i", $this->display_price) : money_format('%i', $this->price);
        $discount = ($this->display_price > 0 && $this->display_price > $this->price) ? calcutatePercentage($this->display_price, $this->price) : 0.0;

        $thisData = [
          'package_id' => $this->id, 
          'service_id' => $this->service_id, 
          'price'  => money_format('%i', $this->price), 
          'rating' => $rating, 
          'instant_booking' => $this->instant_booking, 
          'package_type'    => $this->package_type, 
          'city_id'  => $cityId, 
          'status'   => $status,
          'discount' => $discount,
          'assured'  => $this->assured
        ]; 

        foreach ($this->serviceCategories as $category) {
            $categoryData = [
              'parent_category_id' => $category->parent_id, 
              'subcategory_id' => $category->id
            ];
            $dataToInsert[] = array_merge($thisData, $categoryData);
        }

        foreach ($dataToInsert as $data) {
            $categoryPackage = CategoryPackage::where('package_id', $data['package_id'])
                ->where('service_id', $data['service_id'])
                ->where('parent_category_id', $data['parent_category_id'])
                ->where('subcategory_id', $data['subcategory_id'])
                ->first();
            if($categoryPackage) {
                foreach($data as $key => $value) {
                    $categoryPackage[$key] = $value;
                }
                $categoryPackage->save();
            } else {
                CategoryPackage::insert($data);
            }
        }
    }

    public static function syncCategoryTable() {
        Package::chunk(100, function($packages) {
            foreach ($packages as $package) {
                $package->syncCategoryPackage();
                print "package {$package->id} seeded\n";
            }
        });
    }

    /* 
    @params
        $offset: specifies from which row should we start to get data,
        $limit: limiting the query results.  
    */

    public static function getProductsForLifestage($lifestage=4, $offset=1, $limit= 10) {

        $lifestageCategoryIds = config('admin.category_ids');

        // default category ids
        $categoryIds = $lifestageCategoryIds['4'];

        if(isset($lifestageCategoryIds[$lifestage])) {
            $categoryIds = $lifestageCategoryIds[$lifestage];
        }

        $query = "SELECT 
                distinct package_category_mapping.package_id,
                MIN(package_category_mapping.package_id) as package_id
            FROM 
                package_category_mapping
            LEFT JOIN 
                packages 
            ON
                packages.id = package_category_mapping.package_id
            LEFT JOIN
                service_category
            ON
                service_category.id = package_category_mapping.category_id
            WHERE
                packages.show_on_homepage = 1
            AND 
                packages.price < 800 
            AND
                service_category.category_type = 'product'
            AND
                packages.status = 2
            AND
                packages.instant_booking = 1
            AND
                service_category.parent_id IN ({$categoryIds})
            GROUP BY 
                package_category_mapping.package_id
            ORDER BY
                package_id desc
            LIMIT {$offset},{$limit}";
            

        return DB::select($query);
    }

    public static function getPackagesForLifestage($lifestage=4, $take=6) {

        // $lifestageCategoryIds = config('admin.category_ids');

        // default category ids
        // $categoryIds = $lifestageCategoryIds['4'];

        // if(isset($lifestageCategoryIds[$lifestage])) {
        //     $categoryIds = $lifestageCategoryIds[$lifestage];
        // }

        $query = "SELECT 
                distinct package_category_mapping.package_id,
                RAND() as rand_id, MIN(package_category_mapping.package_id) as package_id
            FROM 
                package_category_mapping
            LEFT JOIN 
                packages
            ON
                packages.id = package_category_mapping.package_id
            LEFT JOIN
                service_category
            ON
                service_category.id = package_category_mapping.category_id
            WHERE
                packages.show_on_homepage = 1
            AND
                service_category.category_type = 'service'
            AND
                packages.status = 2
            AND
                packages.instant_booking = 1
            GROUP BY 
                package_category_mapping.package_id
            ORDER BY
                rand_id
            LIMIT {$take}";
            
        return DB::select($query);
    }
}

