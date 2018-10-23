<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotedFeed extends Model
{
    //
    protected $table    = "promoted_feeds";

    const PROMOTED_POST = "promo";     // HASH_MAP name for promoted posts in redis
    const USER          = "user";      // KEY name for user who receives promoted posts
}
