<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoryFieldType extends Model
{
    protected $table = 'sub_category_field_types';

    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_TEXT = 'text';
    const TYPE_DROPDOWN = 'dropdown';

}
