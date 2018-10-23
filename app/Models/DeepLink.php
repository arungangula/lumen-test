<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeepLink extends Model
{
    protected $table = 'deeplinks';

    public function getShortLinkAttribute(){
    	return shortUrlReplace($this->dynamic_deeplink);
    }
}
