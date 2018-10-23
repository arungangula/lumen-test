<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledPost extends Model
{
    protected $table = "post_content_scheduled";

    public function user()
    {
        return $this->belongsTo('App\Models\User','post_by_user_id');
    }

    public function getNameAttribute(){
        if(isset($this->user)){
            return $this->user->name;
        }
        else{
            return '';
        }
    }
}
