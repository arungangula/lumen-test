<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContestEntryVotes extends Model
{
    //
    public $timestamps = false;
    protected $table = 'contest_entries_votes';

    public function contestentry() {

      return $this->belongsTo('App\Models\ContestEntry', 'contest_entry_id');
    }

}
