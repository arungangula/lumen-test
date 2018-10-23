<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $table = 'enquiries';

    const PACKAGE_ENQUIRY = "package_enquiry";
    const PACKAGE_ENQUIRY_RESPONSE = "package_enquiry_response";

    public function replies()
    {

    	return $this->hasMany('App\Models\Enquiry', 'enquiry_id');
    }

    public function getOwnerAttribute()
    {
    	return ($this->service_manager_id == $this->user_id);
    }

    public function getRepliedAttribute()
    {
        return ($this->replies->count() > 0);
    }

    public function isRepliesLoaded() {

        return isset($this->relations['replies']);
    }
}
