<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class UserContacts extends Model
{
	protected $table = 'bc_user_contacts';

	const CONTACTS_UPLOADED = 1;
	const CONTACTS_PARSED 	= 2;

	public function users() {
         return $this->belongsTo('App\Models\User', 'user_id');
    }
}