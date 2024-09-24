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

namespace App\Services\PaymentMethods;

use App\Contracts\PaymentInterface;
use Illuminate\Support\Facades\Config;
use PaytmWallet;
use Illuminate\Support\Str;

class PaytmPayment implements PaymentInterface
{


    /**
     * Intialize Stripe with Secret key
     *
     */
    public function __construct()
    {
        Config::set('services.paytm-wallet.env', payment_gateway('mode', 'Paytm'));
        Config::set('services.paytm-wallet.merchant_id', payment_gateway('merchant_id', 'Paytm'));
        Config::set('services.paytm-wallet.merchant_key', payment_gateway('key', 'Paytm'));
        Config::set('services.paytm-wallet.merchant_website', payment_gateway('website', 'Paytm'));
        Config::set('services.paytm-wallet.channel', payment_gateway('channel', 'Paytm'));
        Config::set('services.paytm-wallet.industry_type', payment_gateway('type', 'Paytm'));
    }

    public function makePayment($payment_details, $indent_id = '')
    {
        try {

     
            $Paytmpayment = PaytmWallet::with('receive');
            $Paytmpayment->prepare([
                'order' => 'order_'.Str::random(10),
                'user' => (string) $payment_details['user_id'],
                'mobile_number' => $payment_details['user_phone'],
                'email' => $payment_details['user_email'],
                'amount' => $payment_details['amount'], // se amount in float
                'callback_url' => route('payment.verify'),
            ]);
            
         //   dd($Paytmpayment->receive());
           return $Paytmpayment->receive();
              
        } catch (\Exception $e) {

            \Log::error(json_encode($e));
            return arrayToObject(array(
                'status'         => false,
                'status_message' => $e->getMessage(),
            ));
        }
    }


        
    public function payment_verify($requestData)
    {
        $return_data['status']  = false;
        $return_data['status_message']  = "Payment Failed!";

        $transaction = PaytmWallet::with('receive');
 
        $response = $transaction->response();
       
        //if payment is successful
        if($transaction->isSuccessful()){
            $return_data['status'] = true;
            $return_data['transaction_id'] = $response['TXNID'];
            $return_data['status_message']  = $response['STATUS'];
            return arrayToObject($return_data);
        }else if($transaction->isFailed()){
            return arrayToObject($return_data);
        }
    }
}
