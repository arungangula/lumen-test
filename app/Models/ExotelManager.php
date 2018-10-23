<?php
//                          ~~Vegeta~~
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Service;
use Slugify;
use App\Models\User;

use App\Feed\Cache\ServiceCache;

/**
 *      Exotel Manager Clas
 *
 */
class ExotelManager extends Model {

    protected $table = 'exotel_call_details';
    public $timestamps = false;

    // public $id  = "";
    // public $CallSidTemp  = "";
    // public $CallFrom  = "";
    // public $CallTo  = "";
    // public $CallStatus  = "";
    // public $Direction  = "";
    // public $ForwardedFrom  = "";
    // public $Created  = "";
    // public $DialCallDuration  = "";
    // public $StartTime  = "";
    // public $EndTime  = "";
    // public $CallType  = "";
    // public $DialWhomNumber  = "";
    // public $flow_id  = "";
    // public $tenant_id  = "";
    // public $digits  = "";
    // public $exotelFrom  = "";
    // public $exotelTo  = "";
    // public $query_string  = "";
    // public $city_name = "";
    // public $created_date  = "";
    // public $mobileNumber = "";
    public $sp = "";

    public function __construct()
    {
        // $this->CallSid          = $request->input('CallSid', '');
        // $this->CallFrom         = $request->input('CallFrom', '');
        // $this->CallTo           = $request->input('CallTo', '');
        // $this->CallStatus       = $request->input('CallStatus', '');
        // $this->Direction        = $request->input('Direction', '');
        // $this->ForwardedFrom    = $request->input('ForwardedFrom', '');
        // $this->Created          = $request->input('Created', '');
        // $this->DialCallDuration = $request->input('DialCallDuration', '');
        // $this->StartTime        = $request->input('StartTime', '');
        // $this->EndTime          = $request->input('EndTime', '');
        // $this->CallType         = $request->input('CallType', '');
        // $this->DialWhomNumber   = $request->input('DialWhomNumber', '');
        // $this->flow_id          = $request->input('flow_id', '');
        // $this->tenant_id        = $request->input('tenant_id', '');
        // $this->digits           = $request->input('digits', '');
        // $this->exotelFrom       = $request->input('From', '');
        // $this->exotelTo         = $request->input('To', '');
        // $this->query_string     = $request->input('query_string', '');
        // $this->created_date     = time();
    }


    public function user(){

        return $this->hasOne('App\Models\User',DB::raw('RIGHT(`mobile_number`,10)'), DB::raw('RIGHT(`exotelFrom`,10)'));
    }

    public static function getCallsWithUsers($service_id){


        $calls = ExotelManager::where('service_id',$service_id)->select('exotel_call_details.*','bc_users.name as user_name','bc_users.mobile_number as user_mobile_number','bc_users.id as user_id','bc_users.avatar as user_image')

        ->leftJoin('bc_users', DB::raw('RIGHT(bc_users.mobile_number,10)'), '=', DB::raw('RIGHT(exotel_call_details.CallFrom,10)'))
        ->where('callFrom','!=','')->where('callFrom','!=','0')
        ->orderBy('StartTime','desc')
        ->take(20)
        ->get();

        $call_ids = [];
        $calls_unique = [];
        foreach($calls as $call){
           if(!in_array($call->id, $call_ids)){
                $calls_unique[] = $call;
                $call_ids[]= $call->id;
           }
        }
        return $calls_unique;

    }

    private function setRequestParameters ($request)
    {

        $this->CallSid          = $request->input('CallSid', '');
        $this->CallFrom         = $request->input('CallFrom', '');
        $this->CallTo           = $request->input('CallTo', '');
        $this->CallStatus       = $request->input('DialCallStatus', '');
        $this->Direction        = $request->input('Direction', '');
        $this->ForwardedFrom    = $request->input('ForwardedFrom', '');
        $this->Created          = $request->input('Created', '');
        $this->DialCallDuration = $request->input('DialCallDuration', '');
        $this->StartTime        = $request->input('StartTime', '');
        $this->EndTime          = $request->input('EndTime', '');
        $this->CallType         = $request->input('CallType', '');
        $this->DialWhomNumber   = $request->input('DialWhomNumber', '');
        $this->flow_id          = $request->input('flow_id', '');
        $this->tenant_id        = $request->input('tenant_id', '');
        $this->digits           = $request->input('digits', '');
        $this->exotelFrom       = $request->input('From', '');
        $this->exotelTo         = $request->input('To', '');
        $this->query_string     = $request->input('query_string', '');
        $this->created_date     = time();

    }

    public function collectResponse($request)
    {
        $this->CallSid          = $request->input('CallSid', '');
        $this->CallFrom         = $request->input('CallFrom', '');
        $this->CallTo           = $request->input('CallTo', '');
        $this->CallStatus       = $request->input('DialCallStatus', '');
        $this->Direction        = $request->input('Direction', '');
        $this->ForwardedFrom    = $request->input('ForwardedFrom', '');
        $this->Created          = $request->input('Created', '');
        $this->DialCallDuration = $request->input('DialCallDuration', '');
        $this->StartTime        = $request->input('StartTime', '');
        $this->EndTime          = $request->input('EndTime', '');
        $this->CallType         = $request->input('CallType', '');
        $this->DialWhomNumber   = $request->input('DialWhomNumber', '');
        $this->flow_id          = $request->input('flow_id', '');
        $this->tenant_id        = $request->input('tenant_id', '');
        $this->exotelFrom       = $request->input('From', '');
        $this->exotelTo         = $request->input('To', '');
        $this->created_date     = time();
        return true;
    }

    public function verifyUserExtension ($request)
    {

        $this->setRequestParameters ($request);
        // echo $this->CallSidTemp;
        $arrExotelNumber    = config('exotel.numbers');
        $city_name          = "";
        $city_id            = 1;

        if (!empty($this->CallSid))
        {
            $digits         = trim($this->digits,'"');
            $this->digits   = $digits;
            foreach ($arrExotelNumber as $k => $v)
            {
                if ($this->exotelTo == $v)
                {
                    $keys = explode('_',$k);
                    $city_id    =   $keys[0];
                    $city_name  =   $keys[1];
                }
            }
            $this->city_name    = $city_name;
        }

        if(!empty($this->CallFrom)){
            $user = User::where('mobile_number',format_number($this->CallFrom))->first();
            if($user){
                $this->user_id = $user->id;
            }
        }

        if (!empty ($this->digits))
        {
            $digits = trim($this->digits,'"');
            $arrObject = Service::where('exotel_digits',$digits)
                                ->where('area_id','=',$city_id)
                                ->where('published',1)
                                ->first();

            // if empty check service with online flag
            if(!$arrObject){
                $arrObject = Service::where('exotel_digits',$digits)
                                ->where('online_flag', 1)
                                ->where('published',1)
                                ->first();
            }

            // if still empty check service with homedelivery_flag
            if(!$arrObject){
                $arrObject = Service::where('exotel_digits',$digits)
                                ->where('home_delivery_flag', 1)
                                ->where('published',1)
                                ->first();
            }

            if (isset($arrObject) && isset($arrObject['id']) && !empty($arrObject['id'])) {
                $this->service_id = $arrObject['id'];
                return true;
            }
            else {
                $full_number = "{$this->exotelTo},{$this->digits}";
                $premium_numbers = config('exotel.premium_numbers_by_service_id');
                if( in_array($full_number, array_values($premium_numbers)) ) {
                    $service_id = "";
                    foreach($premium_numbers as $_service_id => $number){
                        if($number == $full_number) {
                            $service_id = $_service_id;
                        }
                    }
                    $this->service_id = $service_id;
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }


    public function getExotelNumber ($request, $sms_flag = false)
    {
        $arrExotelNumber    = config('exotel.numbers');
        $contactNumber      = "";
        $mobileNumber       = "";

        $this->CallSid      = $request->input('CallSid', '');
        if($sms_flag == true)
            $this->CallFrom = $request->input('CallFrom', '');



        if (!empty($this->CallSid))
        {
            $arrExotelLog = ExotelManager::where('CallSid','=',$this->CallSid)->first();



            if (isset($arrExotelLog) && isset($arrExotelLog->id) && !empty($arrExotelLog->id))
            {
                $city_name = "";

                foreach ($arrExotelNumber as $k => $v)
                {
                    if ($arrExotelLog->exotelTo == $v)
                    {
                        $keys = explode('_',$k);
                        $city_id        =   $keys[0];
                        $city_name_temp =   $keys[1];
                    }
                }

                if ( empty($arrExotelLog->city_name) && isset($city_name_temp))
                {
                    $city_name = $city_name_temp;
                }
                else
                {
                    $city_name = $arrExotelLog->city_name;
                }

                // Premium Number Injection
                $full_number = "{$arrExotelLog->exotelTo},{$arrExotelLog->digits}";
                $premium_numbers = config('exotel.premium_numbers_by_service_id');

                if( in_array($full_number, array_values($premium_numbers)) ) {
                    $service_id = "";
                    foreach($premium_numbers as $_service_id => $number){
                        if($number == $full_number) {
                            $service_id = $_service_id;
                        }
                    }
                    $arrObject = Service::find($service_id);

                }
                elseif (!empty ($city_name))
                {
                    $digits = trim($arrExotelLog->digits,'"');
                    $arrObject = Service::where('exotel_digits','=',$digits)->where('published',1)->first();

                    // if empty check service with online flag
                    if(!$arrObject){
                        $arrObject = Service::where('exotel_digits',$digits)
                                        ->where('online_flag', 1)
                                        ->where('published',1)
                                        ->first();
                    }

                    // if still empty check service with homedelivery_flag
                    if(!$arrObject){
                        $arrObject = Service::where('exotel_digits',$digits)
                                        ->where('home_delivery_flag', 1)
                                        ->where('published',1)
                                        ->first();
                    }
                }

                if (isset($arrObject) && isset($arrObject['id']) && !empty($arrObject['id']))
                {
                    if($sms_flag == true){
                        $this->sp = $arrObject;
                    }

                    $service_manager_contact = '';
                    if($arrObject->manager_id){
                        $service_manager_contact = User::find($arrObject->manager_id)->mobile_number;
                    }

                    $mobile_number = $arrObject['mobile_number'];
                    $landline = $arrObject['contact'];
                    $babychakra_representative = format_number(config('exotel.customer_care_number')); //shrikant's number

                    $phone_numbers = [];
                    $phone_numbers = array_merge($phone_numbers, $this->numbersToArray($mobile_number));
                    $phone_numbers = array_merge($phone_numbers, $this->numbersToArray($landline));
                    $phone_numbers = array_merge($phone_numbers, $this->numbersToArray($service_manager_contact));

                    $serviceCache = new ServiceCache();
                    $service      = $serviceCache->fetch($arrObject->id);
                    
                    if(isset($service['categories']) 
                        && isset($service['city_id']) 
                        && in_array($service['city_id'], explode(',', env('REVENUE_CITY_ID')))
                        && !empty(array_intersect(explode(',', env('REVENUE_CATEGORY_ID')), explode(',', $service['categories'])))) {
            
                        $phone_numbers = array_merge($phone_numbers, $this->numbersToArray($babychakra_representative));
                    }
                    
                    $contactNumber = join(',', $phone_numbers);


                    // if(!empty($arrObject['mobile_number']))
                    // {
                    //     $this->mobileNumber  =  str_replace(' ', '', $arrObject['mobile_number']);
                    //     $contactNumber =  str_replace(' ', '', $arrObject['mobile_number']);
                    // }
                    // elseif(!empty($arrObject['contact']))
                    //     $contactNumber = str_replace(' ', '', $arrObject['contact']);
                    // else
                    //     $contactNumber = '9833809832';
                    // //Shrikant's number

                    // dd($arrObject);
                }

            }
            //insert else log here
            echo $contactNumber;

        }

        //logging commented out below, to be done later.
        // $reuestLog = $this->ArrayToString ($_REQUEST);
        // $serverParameterLog = $this->ArrayToString ($_SERVER);


        // $string = "\n===================New Request===========\n". "Request parameters: ".$reuestLog." \n" . "Server Parameters: ".$serverParameterLog."\n";

        // $flag= file_put_contents("/var/www/html/babychakra/logs/exotel_log.txt", $string, FILE_APPEND);
    }

    public function numbersToArray($numbers){
        $phone_number = [];
        foreach (explode(',', $numbers) as $number) {
            $number = preg_replace("/[^0-9]/", "", trim($number));
            $phone_number[] = format_number($number);
        }
        return array_filter($phone_number);
    }

    public function sms_exotelapi($request)
    {
        $sms_flag = true;
        $this->getExotelNumber($request, $sms_flag);

        if($this->mobileNumber!=null)
        {
            $a          = Service::getServiceBabyChakraURL($this->sp);
            if(isset($this->sp))
                $serviceName = $this->sp->name;
            else
                $serviceName = '';
            // $post_data  = array
            //     (
            //     // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
            //     // For promotional, this will be ignored by the SMS gateway
            //     'From'   => '',//$this->CallTo,
            //     'To'    => $this->mobileNumber,
            //     'Body'  => "Hi, ".$this->CallFrom." found you on BabyChakra.com & was trying to reach you. Have you checked out your profile yet? ".config('app.url').$a
            //     );

            $post_data  = array
                (
                // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
                // For promotional, this will be ignored by the SMS gateway
                'From'   => '',//$this->CallTo,
                'To'    => $this->mobileNumber,
                'Body'  => "Hi ".$serviceName.", ".$this->CallFrom." found you on BabyChakra.com & was trying to reach you. Sign up for the BabyChakra Business App here http://www.babychakra.com/sign-up. Cheers, Team BabyChakra"
                );

            $url    = "https://".config('exotel.exotel_sid').":".config('exotel.exotel_token')."@twilix.exotel.in/v1/Accounts/".config('exotel.exotel_sid')."/Sms/send";
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

            $http_result = curl_exec($ch);
            $error = curl_error($ch);
            $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);

            curl_close($ch);
            print "Response = ".print_r($http_result);

        }
    }

    public function sendSMS($mobileNumber, $text, $priority='normal'){

        $post_data  = array(
                // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
                // For promotional, this will be ignored by the SMS gateway
                'From'   => '',//$this->CallTo,
                'To'    => $mobileNumber,
                'Body'  => $text,
                'priority' => $priority, //send high for only OTPs
                'StatusCallback' => url('/exotel/sms/callback'),
            );

        $url = "https://".config('exotel.exotel_sid').":".config('exotel.exotel_token')."@twilix.exotel.in/v1/Accounts/".config('exotel.exotel_sid')."/Sms/send";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

        $http_result = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);

        curl_close($ch);
        $result = simplexml_load_string($http_result);
        $resultArray = json_encode($result);
        $response = json_decode($resultArray);
        return $response;
    }

    public function thanku_sms_exotelapi($request)
    {
        $sms_flag   = true;
        $this->getExotelNumber($request,$sms_flag);


        if($this->mobileNumber!=null)
        {
            //$serviceProvider = new ServiceProvider();
            //$a = $serviceProvider->service_prrovider_name." H9BCMSpIdN".$serviceProvider->id;
            $a          = Service::getServiceBabyChakraURL($this->sp);
            if(isset($this->sp))
                $serviceName = $this->sp->name;
            else
                $serviceName = '';
            //$this->mobileNumber='8451820350';
            // $post_data  = array(
            //     // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
            //     // For promotional, this will be ignored by the SMS gateway
            //     'From'   => '',//$this->CallTo,
            //     'To'    => $this->mobileNumber,
            //     'Body'  => "Hi, you just spoke to ".$this->CallFrom." who found you on babychakra.com. Have you checked out your profile yet? ".config('app.url').$a
            // );

            $post_data  = array(
                // 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
                // For promotional, this will be ignored by the SMS gateway
                'From'   => '',//$this->CallTo,
                'To'    => $this->mobileNumber,
                'Body'  => "Hi ".$serviceName.", you just spoke to ".$this->CallFrom." who found you on BabyChakra.com. Sign up for the BabyChakra Business App here http://www.babychakra.com/sign-up. Cheers, Team BabyChakra"
            );

            $url = "https://".config('exotel.exotel_sid').":".config('exotel.exotel_token')."@twilix.exotel.in/v1/Accounts/".config('exotel.exotel_sid')."/Sms/send";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

            $http_result = curl_exec($ch);
            $error = curl_error($ch);
            $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);

            curl_close($ch);
            $result=simplexml_load_string($http_result);
            $resultArray=json_encode($result);
            $response=json_decode($resultArray);
            print "Response = ".print_r($http_result);



        }
    }




}
