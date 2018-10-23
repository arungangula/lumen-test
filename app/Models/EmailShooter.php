<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailShooter extends Model
{
    protected $table = 'email_shooters';
    const STATUS_CREATED = 'created';
    const STATUS_SENT = 'sent';
}
