<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class NewsLetterUser extends Model {

	const SUBSCRIBED_FOR_IOS_APP = 'ios_app';

    protected $table = 'bc_users_signupuser';

    public $timestamps = true;

    protected $fillable = ['name', 'email', 'mob_no', 'subscribed_for'];

    public function SignUpUser($signUpData) {

		$newsLetterUser = new NewsLetterUser;
		$newsLetterUser->email = $signUpData['email'];
		$newsLetterUser->birth_day = $signUpData['birth_day'];
		$newsLetterUser->birth_month = $signUpData['birth_month'];
		$newsLetterUser->birth_year = $signUpData['birth_year'];
		$newsLetterUser->city_id = $signUpData['city_id'];
		$newsLetterUser->subscribed_for = $signUpData['type'];
		$newsLetterUser->source_url = $signUpData['source_url'];
		$newsLetterUser->mob_no = $signUpData['mob_no'];
        $newsLetterUser->name   = $signUpData['name'];

		if($newsLetterUser->save())
		{
			return true;
		}

		return false;
    }

    public function SignUpForMobileApp($signUpData) {

		$newsLetterUser = new NewsLetterUser;
		$newsLetterUser->mob_no = $signUpData['mob_no'];
		$newsLetterUser->city_id = $signUpData['city_id'];
		$newsLetterUser->subscribed_for = $signUpData['type'];
		$newsLetterUser->source_url = $signUpData['source_url'];

		if($newsLetterUser->save())
		{
			return true;
		}

		return false;
    }

}
