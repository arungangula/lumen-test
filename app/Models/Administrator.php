<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\Models\Role;

//Administrator model to handle sleeping owl admin panel accounts

class Administrator extends Model {

	use EntrustUserTrait;

	protected $table = 'administrators';

    public $timestamps = false;

    //Many many relationship with entrust roles (for admin groups)
     public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

}

?>