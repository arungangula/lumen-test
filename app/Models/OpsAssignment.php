<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpsAssignment extends Model
{
    protected $table = "ops_service_assignments";

    public function service() 
    {
    	return $this->belongsTo('App\Models\Service', "service_id");
    }
}
