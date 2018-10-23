<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class ChatGroup extends Model {
    protected $table = 'chat_groups';

    const GROUP_TYPE_LANGUAGE = "language_specific";
    const GROUP_TYPE_DAD = "group_type_dad";
    const GROUP_TYPE_DAD_HINDI = "group_type_dad_hindi";
    const GROUP_TYPE_LOCATION = 'location_specific';
    const GROUP_TYPE_MONTH = 'month_specific';
    
    public $languageGroupNames = [
    	"Telugu (తెలుగు) అమ్మలు",
    	"Tamil (தமிழ்) அம்மாக்கள்",
    	"Malayalam (മലയാളം) അമ്മമാർ",
    	"Hindi (हिन्दी) माँ समुदाय",
    	"Gujarati (ગુજરાતી) માતાઓનુ ગ્રુપ",
    ];

    public function getMonthSpecificGroupName($baby) {

    	$babiesBirthDate = new Carbon($baby->birth_date);
    	return "{$babiesBirthDate->format('M')} {$babiesBirthDate->format('Y')} Birth Club";
    }

    public function members() {
        return $this->hasMany('App\Models\UserChatGroup', 'group_id');
    }
}
