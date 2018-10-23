<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model {

    protected $table = 'keywords';

    public function bracket()
    {
        //any keyword with bracket_id -1 is a bracket and not a keyword
        return $this->belongsTo('App\Models\Keyword','bracket_id');
    }
}