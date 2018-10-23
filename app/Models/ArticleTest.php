<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use SleepingOwl\Models\SleepingOwlModel; 
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class ArticleTest extends SleepingOwlModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    // public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bc_content';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = ['title'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = ['password', 'remember_token'];

    // public function getImageFields()
    // {
    //     return [
    //         'image' => 'tempImages/',
    //         ];
    // }

    // public function setImage($field, $image)
    // {
    //     parent::setImage($field, $image);
    //     $file = $this->$field;
    //     if ( ! $file->exists()) return;
    //     $path = $file->getFullPath();

    //     hackImages($image);

    //     // you can use Intervention Image package features to change uploaded image
    //     Image::make($path)->resize(10, 10)->save();
    // }

    // public function hackImages ($image)
    // {
    //     return;
    // }

}
