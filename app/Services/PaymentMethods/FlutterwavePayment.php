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
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class FlutterwavePayment implements PaymentInterface
{

    /**
     * Intialize Stripe with Secret key
     *
     */
    public function __construct()
    {
        Config::set('flutterwave.publicKey', payment_gateway('key', 'Flutterwave'));
        Config::set('flutterwave.secretKey', payment_gateway('secret', 'Flutterwave'));
        Config::set('flutterwave.secretHash', payment_gateway('hash', 'Flutterwave'));
    }

    public function makePayment($payment_details, $indent_id = '')
    {
        try{

         
            //This generates a payment reference
            $reference = Flutterwave::generateReference();

     //     dd($payment_details);
            // Enter the details of the payment
            $data = [
                'payment_options' => 'card,banktransfer',
                'amount' => (int) $payment_details['amount'],
                'email' => $payment_details['user_email'],
                'tx_ref' => $reference,
                'currency' => $payment_details['currency_code'],//"NGN",//$payment_details['currency_code']
                'redirect_url' => route('payment.verify',['payment_type'=> 'Flutterwave']),
                'customer' => [
                    'email' => $payment_details['user_email'],
                    "phone_number" => $payment_details['user_phone'],
                    "name" => $payment_details['user_name'],
                ],

                // "customizations" => [
                //     "title" => 'Movie Ticket',
                //     "description" => "20th October"
                // ]
            ];

            $payment = Flutterwave::initializePayment($data);

            if ($payment['status'] !== 'success') {
                // notify something went wrong
                return arrayToObject(array(
                    'status'         => false,
                    'status_message' => 'payment failed.',
                ));
            }

            $return_data['status'] = true;
            $return_data['status_message'] = 'payment successful';
            $return_data['payment_link'] = $payment['data']['link'];

            return arrayToObject($return_data);

        }catch(\Exception $e) {

            \Log::error(json_encode($e));
            return arrayToObject(array(
                'status'         => false,
                'status_message' => $e->getMessage(),
            ));
        }
    }


    public function get_user_deatils($token)
    {
     
        if (session()->has('payment_data.user_token') || $token) {
            if (request()->token) {
                return \JWTAuth::parseToken()->authenticate();
            }
            $set_token = \JWTAuth::setToken(session()->get('payment_data.user_token'));
            return  $set_token->toUser();
        } else
            return auth()->guard('web')->user();
    }


    
    public function payment_verify($requestData)
    {
        $return_data['status']  = false;
        $return_data['status_message']  = "Payment Failed!";
        $status = $requestData->status;

        //if payment is successful
        if ($status ==  'successful') {
        
        $transactionID = Flutterwave::getTransactionIDFromCallback();
        $data = Flutterwave::verifyTransaction($transactionID);

        $return_data['status'] = true;
        $return_data['transaction_id'] = $requestData->transaction_id;
        $return_data['status_message']  = $data['message'];
        return arrayToObject($return_data);
        }
        elseif ($status ==  'cancelled'){
            //Put desired action/code after transaction has been cancelled here
            return arrayToObject($return_data);
        }
        else{
            //Put desired action/code after transaction has failed here
            return arrayToObject($return_data);
        }
    }

}