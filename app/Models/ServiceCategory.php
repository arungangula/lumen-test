<?php
namespace App\Models;

use App\Models\City;

use Illuminate\Database\Eloquent\Model;
use DB, Redis;
use App\Models\FeedPost;

class ServiceCategory extends Model {

    protected $table = "service_category";

    public $timestamps = false;

    public function lifestages(){

        return $this->belongsToMany('App\Models\Lifestage', 'subcategory_lifestage_mapping', 'subcategory_id', 'lifestage_id');
    }

    //Vegeta
    public function parentCategory()
    {
        return $this->hasOne('App\Models\ServiceCategory','id', 'parent_id');
    }

    public function interestTags()
    {
        return $this->belongsToMany('App\Models\InterestTag', 'interest_tags_subcategory_mapping', 'sub_category_id', 'tag_id');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Models\Area', 'city_category_mapping', 'category_id', 'city_id');
    }

    public function dailyTip()
    {
        return $this->hasMany('App\Models\DailyTip', 'subcategory_id');
    }

    public function subcategories(){

        return $this->hasMany('App\Models\ServiceCategory','parent_id');
    }

    public function subcategoriesWithMapping() {

        return $this->hasMany('App\Models\ServiceCategory','parent_id')->with('categoryLifestageMapping');
    }

    public function categoryLifestageMapping(){

        return $this->hasMany('App\Models\CategoryLifestageMapping', 'subcategory_id');
    }

    public function googleProductTaxonomy()
    {
        return $this->hasOne('App\Models\GoogleProductTaxonomy','id', 'product_taxonomy_id');
    }

    //Vegeta
    public static function getIndexableServiceCategories()
    {
		$data = ServiceCategory::where('id','>',6);
		return $data;
    }

    public static function getList ()
    {
        return ServiceCategory::where('category_name','=','Wellness')->take(5)->get();
    }

    public function sortSubcategoriesByLifestage($lifestage_id){

        $expecting = [];
        $new_parents = [];
        $toddler = [];
        foreach($this->subcategories as $subcategory){
            //basically pregnant services will be different
            $bucket_id = $subcategory->lifestages->reduce(function($result_id, $lifestage_obj) use ($lifestage_id) {

                                                                                if($result_id == $lifestage_id) { return $result_id; }
                                                                                else {  return $lifestage_obj->id > $result_id ? $lifestage_obj->id:$result_id;  }

                                                                            }, 0);
            switch($bucket_id){

                case 4: $toddler[] = $subcategory;
                        break;

                case 5: $new_parents[] = $subcategory;
                        break;

                case 6: $expecting[] = $subcategory;
                        break;

                default:
                        break;
            }
        }

        //if expecting aappend in order
        switch($lifestage_id){

            case 6: $this->subcategories = array_merge($expecting, $new_parents, $toddler);
                    break;
            case 4: $this->subcategories = array_merge($new_parents, $toddler, $expecting);
                    break;
            case 5: $this->subcategories =  array_merge($toddler, $new_parents, $expecting);
                    break;

            default: break;
        }

    }

    public function getUniqueImagePath($original_filepath=null){

        if(!$original_filepath){
            if($this->image_url){
                $pInfo = pathinfo($this->getCoverImageUrl());
            }
        } else {
            $pInfo = pathinfo($original_filepath);
        }
        if(isset($pInfo)){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('category');
        $path = join('/',[ $unique_id.'.'.$extension]);
        return $path;
    }

    public function cacheBust() {

        $cityIds = City::cityIds();

        $cacheKeys = ['shop_categories_4', 'shop_categories_5', 'shop_categories_6', 'shop_categories_7', 'shop_categories_8'];
        foreach ($cityIds as $cityId) {
            
            $cacheKeys[] = "book_categories_v2_{$cityId}";
        }
        bustCache($cacheKeys);
        Redis::command("del",["category:{$serviceCategory->id}"]);
    }

    public function syncCategoryPackage() {
        CategoryPackage::where('subcategory_id', $this->id)->chunk(100, function($categoryPackages) {
            foreach ($categoryPackages as $categoryPackage) {
                $categoryPackage->parent_category_id = $this->parent_id;
                $categoryPackage->save();
                print "package {$categoryPackage->id} seeded\n";
            }
        });
    }
}
