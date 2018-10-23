<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GetStream\Stream\Client;
use App\Models\User;
use Facebook;
use DB;
use GuzzleHttp;
use Guzzle;

//Model to handle facebook and google login

class SocialUserProfile extends Model {

	protected $table = 'user_social_profile_providers';

    public $timestamps = false;

    public function user(){

      return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function getGoogleFriends($access_token,$user_id){

        $gClient = new GuzzleHttp\Client();
        try {
            $response = $gClient->get('https://www.googleapis.com/plus/v1/people/me/people/visible', [
                            'query' => ['access_token' => $access_token ]
                        ]);

         } catch(GuzzleHttp\Exception\ClientException $e){
            return null;
         } catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
           return null;
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            return null;
        }  catch(\Exception $e){
            return null;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }


    public static function getGoogleDetailsFromAccessToken($access_token){

        $gClient = new GuzzleHttp\Client();
        try {
            $response = $gClient->get('https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
                            'query' => ['access_token' => $access_token ]
                        ]);

         } catch(GuzzleHttp\Exception\ClientException $e){
            return null;
         } catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
           return null;
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            return null;
        }  catch(\Exception $e){
            return null;
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }

    //Facebook Login Function
    //Take user info from facebook access token
    public function executefacebook($access_token){
        Facebook::setDefaultAccessToken($access_token);
	    $user = Facebook::get('/me?fields=id,first_name,last_name,gender,link,email')->getGraphUser();
        return $user;
    }

    //Check whether the user is a new user
    public function checkExistingUser($user){
        $email = $user['email'];
       	$item = User::where('email','=',$email)->first();
       	return $item;
    }

     //Check whether the returning user is a facebook user
     public function checkfacebookUser($id){
        //existing user
        $socialProvider = DB::table('user_social_profile_providers')->where('user_id','=',$id)->get();
        $isFacebookUser = false;

        foreach ($socialProvider as $provider) {
            if($provider->provider == 'facebook'){
                $isFacebookUser = true;
                break;
            }
        }
        return $isFacebookUser;
     }

     //Insert in DB if the user is a new user
    public function insertNewUserFacebook($user,$accesstoken){

    	$tuser=new User;

        $name=$tuser->name  = $user['first_name'].' '.$user['last_name'];
        $tuser->f_name =  $user['first_name'];
        $tuser->l_name = $user['last_name'];

        if($user['gender'] == 'male'){
            $tuser->gender = '1';
        }
        else{
            $tuser->gender = '2';
        }

        $tuser->social_profile_link = $user['link'];
        $tuser->avatar = '';
        $tuser->email = $user['email'];
        $tuser->activation = 1;
        $tuser->lifestage = '';
        $tuser->momstars = 'no';

        // insert into bc_user
        $tuser->save();

        DB::table('user_social_profile_providers')->insert(array('id'=>'','user_id'=>$tuser->id,'provider'=>'facebook','social_profile_uid'=>$user['id'],'access_token'=>$accesstoken,'access_secret'=>'','created_date'=>$date));

        return $tuser;
    }

    //Google Login Functions

    //Take user info from google access token
    public function executegoogle($access_token){
       	  $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$access_token;
          $request = json_decode(file_get_contents($url));

        return $request;
    }

    //Check whether the user is a new user
    public function checkExistingUserGoogle($user){
        $user=(array)$user;
        $email = $user['email'];
       	$item=User::where('email','=',$email)->first();
       	return $item;
    }

    //Check whether the returning user is a google user
    public function checkgoogleUser($id){
        //existing user
        $socialProvider = DB::table('user_social_profile_providers')->where('user_id','=',$id)->get();
        $isGoogleUser = false;

        foreach ($socialProvider as $provider) {
            if($provider->provider == 'google'){
                $isGoogleUser = true;
                break;
            }
        }
        return $isGoogleUser;
     }

     //Insert in DB if the user is a new user
    public function insertNewUserGoogle($user,$accesstoken){
    	$user=(array) $user;
    	$tuser=new User;

        $name=$tuser->name  = $user['name'];
        $tuser->f_name =  $user['given_name'];
        $tuser->l_name = $user['family_name'];
        $tuser->username = $name;

        if($user['gender'] == 'male'){
            $tuser->gender = '1';
        }
        else{
            $tuser->gender = '2';
        }

        $tuser->social_profile_link = $user['link'];
        $tuser->avatar = '';
        $tuser->email = $user['email'];
        $tuser->facebook_accesstoken = $accesstoken;
        $tuser->activation = 1;
        $tuser->lifestage='';
        $tuser->momstars='no';

        // insert into bc_user
        $tuser->save();

        DB::table('user_social_profile_providers')->insert(array('id'=>'','user_id'=>$tuser->id,'provider'=>'google','social_profile_uid'=>$user['id'],'access_token'=>$accesstoken,'access_secret'=>'','created_date'=>$date));

        return $tuser;
    }

}

?>
