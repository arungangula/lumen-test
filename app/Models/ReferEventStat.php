<?php
namespace App\Models;

use App\Models\User;
use App\Models\ReferLink;
use Illuminate\Database\Eloquent\Model;
use Excel;

class ReferEventStat extends Model {

    protected $table = 'refer_event_stats';


    //this finds the most referring users in the given month
    public static function referInstalls($date)
    {
      $start  = $date;
      $end    = date("Y-m-d",strtotime($start) + 30*24*60*60);
      $refers  = self::where('event_code','refer_install')->whereBetween('created_at',[$start,$end])->select('tuser_id')->get();
      $data   = [];
      foreach ($refers as $refer)
      {
        $u = User::find($refer->tuser_id);
        // print_r($u);
        if ($u)
          $data[$refer->tuser_id] = ['name'=>$u->name,'num'=>(isset($data[$refer->tuser_id]['num'])?$data[$refer->tuser_id]['num']+1:1)];
      }

      $filename   = 'refer_installs_'.$start;
        Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('1', function($sheet) use ($data) {
                    // Sheet manipulation
                    $sheet->fromArray(json_decode(json_encode($data), true));
                });
        })->store('csv',storage_path("referral"));
      return $data;
    }

    public static function referClicks($date)
    {
      $start  = $date;
      $end    = date("Y-m-d",strtotime($start) + 30*24*60*60);
      $refers  = self::where('event_code','refer_click')->whereBetween('created_at',[$start,$end])->select('tuser_id')->get();
      $data   = [];
      foreach ($refers as $refer)
      {
        $u = User::find($refer->tuser_id);
        // print_r($u);
        if ($u)
          $data[$refer->tuser_id] = ['name'=>$u->name,'num'=>(isset($data[$refer->tuser_id]['num'])?$data[$refer->tuser_id]['num']+1:1)];
      }
      $filename   = 'refer_clicks_'.$start;
        Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('1', function($sheet) use ($data) {
                    // Sheet manipulation
                    $sheet->fromArray(json_decode(json_encode($data), true));
                });
        })->store('csv',storage_path("referral"));
      return $data;
    }

    public static function referringUsers($date)
    {
		$start  = $date;
		$end    = date("Y-m-d",strtotime($start) + 30*24*60*60);
		$referrers  = ReferLink::whereBetween('created_at',[$start,$end])->select('user_id')->get();
		$data 	= [];
		foreach ($referrers as $value)
		{
			$u = User::find($value->user_id);
        // print_r($u);
    	    if ($u)
	          $data[$value->user_id] = ['name'=>$u->name,'num'=>(isset($data[$value->user_id]['num'])?$data[$value->user_id]['num']+1:1)];

		}
		$filename   = 'referring_users_'.$start;
        Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('1', function($sheet) use ($data) {
                    // Sheet manipulation
                    $sheet->fromArray(json_decode(json_encode($data), true));
                });
        })->store('csv',storage_path("referral"));
		return $data;
    }

    public static function referredUsers($date)
    {
		$start  = $date;
		$end    = date("Y-m-d",strtotime($start) + 30*24*60*60);
		$users  = User::whereBetween('created_at',[$start,$end])->where('refer_link','!=','')->get();
		$data 	= [];
		foreach ($users as $user)
		{
			$data[] = ['name'=>$user->name,'lifestage'=>$user->lifestage_id, 'id'=>$user->id];

		}
		$filename   = 'referred_users_'.$start;
        Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('1', function($sheet) use ($data) {
                    // Sheet manipulation
                    $sheet->fromArray(json_decode(json_encode($data), true));
                });
        })->store('csv',storage_path("referral"));
		return $data;
    }
  

}