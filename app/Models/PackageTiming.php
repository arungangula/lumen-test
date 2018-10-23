<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

class PackageTiming extends SleepingOwlModel {

    protected $table = 'package_time_mapping';

    public function package()
    {
        return $this->belongsTo('App\Models\Package','package_id');
    }
}