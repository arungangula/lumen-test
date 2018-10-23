<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChatGroup extends Model {
    protected $table = 'user_chat_groups';

    public function user(){
      return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function group() {
        return $this->hasOne('App\Models\ChatGroup', 'id', 'group_id');
    }
}
