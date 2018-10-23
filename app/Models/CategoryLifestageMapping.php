<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Models\SleepingOwlModel;

//Model for Service provider categories relationship with service providers

class CategoryLifestageMapping extends SleepingOwlModel {

    protected $table = 'subcategory_lifestage_mapping';

    public $timestamps = false;
}