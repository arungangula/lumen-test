<?php

namespace App\Models;

use App\Jobs\QueueEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ReportedUser extends Model
{
	use DispatchesJobs;

    protected $table = 'reported_users';

    /**
	* Defines the belongsTo relationship
	*
	* @return mixed
	*/
	public function user()
	{
		return $this->belongsTo('App\Models\User','user_id');
	}

	public function userReported()
	{
		return $this->belongsTo('App\Models\User','report_user_id');
	}

	// for admin panel
	public function getUserNameAttribute(){
		if(isset($this->user)){
			return $this->user->name;
		}
		else{
			return '--';
		}
	}

	// for admin panel
	public function getUserReportedsAttribute(){
		if(isset($this->userReported)){
			return $this->userReported->name;	
		}
		else{
			return '--';
		}
		
	}

    public function onUserReported($reportedUser){
    	$reportedUser = Self::where('id', $reportedUser->id)->with('user', 'userReported')->first();

    	$mail = array();
        $mail['to']             = config('mail.reported_user_team');
        $mail['cc']             = config('mail.reported_user_team_cc');
        $mail['subject']        = 'User Reported by someone';
        $mail['blade']          = 'emails.reportUser';
        $mail['bladeObject']    = ['reportedUser' => $reportedUser, ];

        $email = (new QueueEmail($mail));
        $this->dispatch($email);
    }
}
