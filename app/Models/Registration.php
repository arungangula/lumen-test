<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model {

    protected $table = 'event_registration';

    public $timestamps = false;

    const CUSTOM_EVENT_TYPE = "custom_event";
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event','event_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

}
