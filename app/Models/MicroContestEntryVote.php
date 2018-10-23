<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MicroContestEntryVote extends Model
{
    
    public $timestamps = false;
    protected $table = 'micro_contest_entry_votes';

    public function contestentry() {

      return $this->belongsTo('App\Models\MicroContestEntry', 'contest_entry_id');
    }
}
