<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Businessregistration extends Model
{
    protected $table = 'registration';

    public function getStatAttribute()
    {
    	
    	return config('admin.businessRegistrationStatus')[$this->status];
    }
}
