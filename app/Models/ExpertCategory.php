<?php

namespace App\Models;

use Cache;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ExpertCategory extends Model
{
    protected $table = 'question_category';


    public function lifestages(){

    	return $this->belongsToMany('App\Models\Lifestage', 'question_category_lifestage_mapping', 'experts_category_id', 'lifestage_id');
    	
    }

    public static function getByLifestage(User $user = null){
        $lifestage_id = null;
        if($user){
            $lifestage_id = $user->lifestage_id;
        }

        if(!in_array($lifestage_id, [4,5,6])){
            $lifestage_id = 6;
        }
       

        return Cache::remember("expert_category_by_lifestage_id_{$lifestage_id}", 1440, function() use ($lifestage_id){
            $cats = QuestionCategoryServiceCategoryMapping::where('lifestage_id', $lifestage_id)->distinct()->lists('question_category_id')->toArray();
            if(env('APP_ENV') == "development") {
                return ExpertCategory::get();
            }
            return ExpertCategory::whereIn('id', $cats)->orderBy('id', 'desc')->get();
        });
        return collect();
    }
}
