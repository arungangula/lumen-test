<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceWarehouse extends Model
{
	const STATUS_ACTIVE    = 'active';
	const STATUS_INACTIVE = 'inactive';

    protected $table = 'service_warehouses';
}
