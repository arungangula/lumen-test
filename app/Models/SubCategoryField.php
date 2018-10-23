<?php

namespace App\Models;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;

class SubCategoryField extends Model
{
    protected $table = 'sub_category_fields';

    public function fieldTypes(){
        return $this->belongsTo('App\Models\SubCategoryFieldType','field_id');
    }

    public static function filldata(){
    	$subcats = ServiceCategory::where('parent_id', '!=', 0)->get();

    	$fields_arr = [14, 15, 16, 17];
    	foreach ($subcats as $cat) {
    		$add_fields = array();
    		for ($i=0; $i < 3; $i++) { 
    			$add_fields[] = rand(0, 13); 
    		}
    		$add_fields = array_merge($fields_arr, $add_fields);
    		

 			foreach ($add_fields as $field_id) {
 				if( ! SubCategoryField::where('sub_category_id', $cat->id)->where('field_id', $field_id)->exists() ){
 					$field = new SubCategoryField();
 					$field->sub_category_id = $cat->id;
 					$field->field_id = $field_id;
 					$field->save();
 				}	
 			}
    	}
    }

}
