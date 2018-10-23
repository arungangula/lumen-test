<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $table = 'reactions';

    const REACTION = 'reaction';

    const DISLIKE = 'DISLIKE';
    const FINE = 'FINE';
    const HELPFUL = 'HELPFUL';
    const LOVE = 'LOVE';
    const WORRIED = 'WORRIED';
}
