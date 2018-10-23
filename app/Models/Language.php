<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';

    const CODE_ENGLISH = 'en';
    const CODE_HINDI = 'hi';
    const CODE_TAMIL = 'tm';
    const CODE_TELUGU = 'tl';
    const CODE_GUJARATI = 'gj';

	public static function boot()
    {
        parent::boot();

        self::creating(function($model){
        	$model->name = ucfirst($model->name);
        	$model->code = strtolower($model->code);
        });

        self::updating(function($model){
        	$model->name = ucfirst($model->name);
        	$model->code = strtolower($model->code);
        });
    }

    public function entities() {
    	return $this->hasMany('App\Models\LanguageEntityMapping', 'language_id');
    }

    public function feedposts() {
    	return $this->hasMany('App\Models\LanguageEntityMapping', 'language_id')->where('entity_type', 'feedpost');
    }

    public function articles() {
    	return $this->hasMany('App\Models\LanguageEntityMapping', 'language_id')->where('entity_type', 'article');
    }

    public function questions() {
    	return $this->hasMany('App\Models\LanguageEntityMapping', 'language_id')->where('entity_type', 'question');
    }

    public function reviews() {
    	return $this->hasMany('App\Models\LanguageEntityMapping', 'language_id')->where('entity_type', 'review');
    }

    public function comments() {
    	return $this->hasMany('App\Models\LanguageEntityMapping', 'language_id')->where('entity_type', 'comment');
    }
}
