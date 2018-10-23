<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class UserFollows extends Model
{
    
    protected $table = "user_follows";
    public $timestamps = false;

    public static function userFollowersCount($user_id){
        $followers = Cache::remember('user_followers_'.$user_id, 1440, function() use ($user_id){
            return self::where('following_user_id', $user_id)->count();
        });
        return $followers;
    }
    public static function userFollowingCount($user_id){
        $following = Cache::remember('user_following_'.$user_id, 1440, function() use ($user_id){
            return self::where('user_id', $user_id)->count();
        });
        return $following;
    }

    public static function getUserFollowers($user_id){
        $followers = Cache::remember('user_followers_ids_'.$user_id, 1440, function() use ($user_id){
            return self::where('following_user_id', $user_id)->lists('user_id')->toArray();
        });
        return $followers;
    }

    public static function getUserFollowing($user_id){
        $following = Cache::remember('user_following_ids_'.$user_id, 1440, function() use ($user_id){
            return self::where('user_id', $user_id)->lists('following_user_id')->toArray();
        });
        return $following;
    }
}
