<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPanel extends Model
{

    protected $table = 'notification_panel';

    const PACKAGE_ENQUIRY = 'package_enquiry';
    const WELCOME_MANAGER = 'type_welcome';
    
    public function actionUser()
    {
        return $this->belongsTo('App\Models\User','action_user_id');
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\User','manager_id');
    }

    public function service()
    {   
        return $this->belongsTo('App\Models\Service','service_id');
    }
}
