<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageGroup extends Model
{
    public function packages() {
    	return $this->belongsToMany('App\Models\Package', 'package_group_mapping', 'package_group_id', 'package_id');
    }
}
