<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorAPI;

class PaymentOld extends Payment {
    protected $table = 'payments';

    public static function savePayment($params)
    {
        $api        = new RazorAPI(config('services.razorpay.app_key'), config('services.razorpay.app_secret'));
        $payDetails = $api->payment->fetch($params['payID']);
        $payment    = new Payment;

        $payment->user_id       = $params['userID'];
        $payment->service_id    = $params['serviceID'];
        $payment->amount        = $params['amount'];
        $payment->fee           = $payDetails->fee;
        $payment->service_tax   = $payDetails->service_tax;
        $payment->payment_id    = $params['payID'];
        $payment->status        = $payDetails->status;
        $payment->razor_timestamp   = $payDetails->created_at;
        if(isset($params['description'])){
            $payment->description = $params['description'];
        }
        $payment->save();

        return $payment;
    }
}
