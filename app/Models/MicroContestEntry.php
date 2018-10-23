<?php

namespace App\Models;
use App\Models\Contest;

use Illuminate\Database\Eloquent\Model;

class MicroContestEntry extends Model
{
    protected $table = 'micro_contest';

    public function contest() 
    {

      return $this->belongsTo('App\Models\Contest', 'contest_id');
    }

    public function votes() 
    {

        return $this->hasMany('App\Models\MicroContestEntryVote','contest_entry_id');
    }

    public function getUniqueImagePath($contest_id){
        $extension = 'jpg';
        $unique_id = uniqid('entry/'.$contest_id.'/contest_entry_');
        $path = join('/',[$unique_id.'.'.$extension]);
        return $path;
    }
}
