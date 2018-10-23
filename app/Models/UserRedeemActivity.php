<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class UserRedeemActivity extends SleepingOwlModel {

    protected $table = 'points_redeems';

    /*
    *   $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('reward_item_id')->unsigned();
            $table->integer('points_redeemed')->unsigned();
            $table->string('reward_item_name');
            $table->string('reward_item_image');
            $table->timestamps();
    *
    */


    public function user() {

      return $this->belongsTo('App\Models\User', 'user_id');
    }
}