<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingPopup extends Model
{
    protected $table = "marketingpopups";

    protected $fillable = [
      "title",
      "image",
      "description",
      "cta_text",
      "cta_deeplink",
      "version",
      "min_view_count",
      "platform",
      "platform_version",
      "valid_from",
      "valid_till"
    ];

    public function popupUser() {

    	return $this->hasMany('App\Models\PopupUser', 'popup_id');
    }
}
