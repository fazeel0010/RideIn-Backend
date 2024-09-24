<?php

/**
 * Payment Gateway Controller
 *
 * @package     Cabme
 * @subpackage  Controller
 * @category    Payment Gateway
 * @author      SMR IT Solutions Team
 * @version     2.2.1
 * @link        https://smritsolutions.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    /**
     * Load View and Update Payment Gateway Data
     *
     * @return redirect to payment_gateway
     */
    public function index(Request $request)
    {
        if($request->isMethod('GET')) {
            return view('admin.payment_gateway');
        }

        $paypal_rules = array();
        $pt_rules = array();
        $stripe_rules = array();
        $bt_rules = array();
       $fw_rules = array();



        if($request->paypal_enabled) {
            $paypal_rules = array(
                'paypal_id'         => 'required',
                'paypal_mode'       => 'required',
                'paypal_client'     => 'required',
                'paypal_secret'     => 'required',
                'paypal_access_token'=> 'required',
            );
        }
        if($request->stripe_enabled) {
            $stripe_rules = array(
                'stripe_publish_key'=> 'required',
                'stripe_secret_key' => 'required',
                'stripe_api_version' => 'required',
            );
        }
        if($request->bt_enabled) {
            $bt_rules = array(
                'bt_mode'           => 'required',
                'bt_merchant_id'    => 'required',
                'bt_public_key'     => 'required',
                'bt_private_key'    => 'required',
                'tokenization_key'  => 'required',
            );
        }

        if($request->paytm_enabled) {
            $pt_rules = array(
                'paytm_id'         => 'required',
                'paytm_mode'       => 'required',
                'paytm_key'     => 'required',
                'paytm_website'     => 'required',
                'paytm_channel'=> 'required',
                'paytm_type'=> 'required',
            );
        }
        if($request->flutterwave_enabled) {
            $fw_rules = array(
                'flutterwave_publish_key'=> 'required',
                'flutterwave_secret_key' => 'required',
                'flutterwave_hash_key' => 'required',
            );
        }



        $rules = array_merge($paypal_rules,$stripe_rules,$bt_rules,$pt_rules,$fw_rules);

        // Payment Gateway Validation Custom Names
        $attributes = array(
            'paypal_id'         => 'PayPal ID',
            'paypal_mode'       => 'PayPal Mode',
            'paypal_client'     => 'PayPal Client',
            'paypal_secret'     => 'PayPal Secret',
            'paypal_access_token'=> 'PayPal Access Token',
            'stripe_publish_key'=> 'Stripe Publish Key',
            'stripe_secret_key' => 'Stripe Secret Key',
            'stripe_api_version'=> 'Stripe API Version',
            'bt_mode'           => 'Payment Mode',
            'bt_merchant_id'    => 'Merchant ID',
            'bt_public_key'     => 'Public Key',
            'bt_private_key'    => 'Private Key',
            'paytm_id'         => 'Paytm ID',
            'paytm_mode'       => 'Paytm Mode',
            'paytm_key'     => 'Paytm Merchant Key',
            'paytm_website'     => 'Paytm Merchant Website',
            'paytm_channel'=> 'Paytm Merchant Channel',
            'paytm_type'=> 'Paytm Industry Type',
            'flutterwave_publish_key'=> 'Flutterwave Publish key',
            'flutterwave_secret_key' => 'Flutterwave Secret key',
            'flutterwave_hash_key' => 'Flutterwave Hash key',
        );

        if($request->stripe_enabled && $request->bt_enabled && $request->paytm_enabled ) {
            flashMessage('danger', 'Please Choose either Braintree or Stripe or Paytm for Card Payments');
            return back();
        }

        if($request->stripe_enabled == '0' && $request->bt_enabled == '0' && $request->paypal_enabled == '0'&& $request->paytm_enabled == '0') {
            flashMessage('danger', 'Please enable atleast One Payment Gateway');
            return back();
        }

        if($request->payout_methods == '') {
            flashMessage('danger', 'Atleast One payout method should be enabled.');
            return back();
        }

        $this->validate($request, $rules, [], $attributes);

        $default_payments = array(
            payment_gateway('trip_default','Common'),
        );

        if($request->paypal_enabled == "0" && in_array('paypal',$default_payments)) {
            flashMessage('danger', 'Unable to Disable Paypal. Because this is default payment method');
            return back();
        }

        if(in_array('stripe',$default_payments))
        {
            if($request->stripe_enabled == "0" && $request->bt_enabled == "0" && $request->Paytm_enabled == "0" ) {
                flashMessage('danger', 'Please enable Stripe or Braintree or Paytm. Because card is default payment method');
                return back();
            }
        }
        
        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Paypal'])->update(['value' => $request->paypal_enabled]);
        PaymentGateway::where(['name' => 'paypal_id', 'site' => 'Paypal'])->update(['value' => $request->paypal_id]);
        PaymentGateway::where(['name' => 'mode', 'site' => 'Paypal'])->update(['value' => $request->paypal_mode]);
        PaymentGateway::where(['name' => 'client', 'site' => 'Paypal'])->update(['value' => $request->paypal_client]);
        PaymentGateway::where(['name' => 'secret', 'site' => 'Paypal'])->update(['value' => $request->paypal_secret]);
        PaymentGateway::where(['name' => 'access_token', 'site' => 'Paypal'])->update(['value' => $request->paypal_access_token]);

        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Stripe'])->update(['value' => $request->stripe_enabled]);
        PaymentGateway::where(['name' => 'publish', 'site' => 'Stripe'])->update(['value' => $request->stripe_publish_key]);
        PaymentGateway::where(['name' => 'secret', 'site' => 'Stripe'])->update(['value' => $request->stripe_secret_key]);
        PaymentGateway::where(['name' => 'api_version', 'site' => 'Stripe'])->update(['value' => $request->stripe_api_version]);

        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Flutterwave'])->update(['value' => $request->flutterwave_enabled]);
        PaymentGateway::where(['name' => 'key', 'site' => 'Flutterwave'])->update(['value' => $request->flutterwave_publish_key]);
        PaymentGateway::where(['name' => 'secret', 'site' => 'Flutterwave'])->update(['value' => $request->flutterwave_secret_key]);
        PaymentGateway::where(['name' => 'hash', 'site' => 'Flutterwave'])->update(['value' => $request->flutterwave_hash_key]);

        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Paytm'])->update(['value' => $request->paytm_enabled]);
        PaymentGateway::where(['name' => 'merchant_id', 'site' => 'Paytm'])->update(['value' => $request->paytm_id]);
        PaymentGateway::where(['name' => 'mode', 'site' => 'Paytm'])->update(['value' => $request->paytm_mode]);
        PaymentGateway::where(['name' => 'key', 'site' => 'Paytm'])->update(['value' => $request->paytm_key]);
        PaymentGateway::where(['name' => 'website', 'site' => 'Paytm'])->update(['value' => $request->paytm_website]);
        PaymentGateway::where(['name' => 'channel', 'site' => 'Paytm'])->update(['value' => $request->paytm_channel]);
        PaymentGateway::where(['name' => 'type', 'site' => 'Paytm'])->update(['value' => $request->paytm_type]);

        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Braintree'])->update(['value' => $request->bt_enabled]);
        PaymentGateway::where(['name' => 'mode', 'site' => 'Braintree'])->update(['value' => $request->bt_mode]);
        PaymentGateway::where(['name' => 'merchant_id', 'site' => 'Braintree'])->update(['value' => $request->bt_merchant_id]);
        PaymentGateway::where(['name' => 'public_key', 'site' => 'Braintree'])->update(['value' => $request->bt_public_key]);
        PaymentGateway::where(['name' => 'private_key', 'site' => 'Braintree'])->update(['value' => $request->bt_private_key]);
        PaymentGateway::where(['name' => 'tokenization_key', 'site' => 'Braintree'])->update(['value' => $request->tokenization_key]);
        PaymentGateway::where(['name' => 'merchant_account_id', 'site' => 'Braintree'])->update(['value' => $request->merchant_account_id]);

        $payout_methods = implode(',',$request->payout_methods);

        PaymentGateway::where(['name' => 'payout_methods', 'site' => 'Common'])->update(['value' => $payout_methods]);
        PaymentGateway::where(['name' => 'is_web_payment', 'site' => 'Common'])->update(['value' => $request->is_web_payment]);

        /*if($request->stripe_enabled == "1" && !in_array('stripe',$default_payments)) {
            PaymentGateway::where(['name' => 'trip_default', 'site' => 'Common'])->update(['value' => 'stripe']);
        }

        if($request->bt_enabled == "1" && !in_array('braintree',$default_payments)) {
            PaymentGateway::where(['name' => 'trip_default', 'site' => 'Common'])->update(['value' => 'braintree']);
        }*/

        flashMessage('success', 'Updated Successfully');
    
        return redirect('admin/payment_gateway');
    }
}