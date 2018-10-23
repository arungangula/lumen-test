<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

use DB;

class ServiceOfficeHours extends SleepingOwlModel {

    protected $table = "services_office_hours";

    public $timestamps = false;

    public function serviceProvider()
    {
        return $this->belongsTo('\App\Models\Service','service_provider_id');
    }
}