<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SkuBulkUpload extends Model {
    const STATUS_CREATED = 'created';
    const STATUS_UPLOADED = 'uploaded';
}
