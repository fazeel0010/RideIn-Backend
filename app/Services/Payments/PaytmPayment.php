<?php

/**
 * Paypal Payment Service
 *
 * @package     Cabme
 * @subpackage  Services\Payments
 * @category    Paypal
 * @author      SMR IT Solutions Team
 * @version     2.2.1
 * @link        https://smritsolutions.com
*/

namespace App\Services\Payments;

use App\Contracts\PaymentInterface;
use App\Services\PaymentMethods\PaytmPayment as PaytmGateway;

class PaytmPayment extends PaytmGateway implements PaymentInterface 
{


    public function view($user)
	{

        $payment_data = session()->get('payment_data');
        $data['response'] =  $this->makePayment($payment_data,request()->pay_key);
        return array('view' => 'payment.paytm','data' => $data);
    }


    public function Payment($paymentData,$authCode)
    {   
        $responce = $this->makePayment($paymentData,$authCode);
        return $responce;

    }

    public function Verify($requestData){
        $responce = $this->payment_verify($requestData);
        return  $this->returnResponce($responce);
    }

    
    public function returnResponce($responce)
    {
      $responce->status_code =  $responce->status ? "1":"0";
      return $responce;
    }

}