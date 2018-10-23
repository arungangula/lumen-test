<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
    protected $table = "question_category";

    public function lifestages(){

      return $this->belongsToMany('App\Models\Lifestage', 'question_category_lifestage_mapping', 'experts_category_id', 'lifestage_id');
    }
}
