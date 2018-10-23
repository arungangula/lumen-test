<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageItemProperty extends Model
{

    public function package()
    {
        return $this->belongsTo('App\Models\Package');
    }
}
