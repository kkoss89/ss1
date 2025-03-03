<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once('stripe_ver9/autoload.php');


use Stripe\Stripe;
use Stripe\Customer as StripeCustomer;
use Stripe\Charge as StripeCharge;

/**
 * 
 */
class stripe3ds_api{
    
    public function __construct($stripe_secret_key = null, $stripe_publishable_key = null, $mode = "") {
        \Stripe\Stripe::setApiKey($stripe_secret_key);
    }

    /**
     *
     * Block comment
     *
     */
    public function customer_create($data_buyer = ""){
        if (is_array($data_buyer)) {
            $result = \Stripe\Customer::create($data_buyer);
        }
        return $result;
    }

    /**
     *
     * Define Payment && Create payment.
     *
     */
    public function create_payment($data_charge = ""){
        $result = array();
        
        if (is_array($data_charge)) {
            try {
                $product = \Stripe\Product::create([
                    'name' => $data_charge['name'],
                    'description' => $data_charge['description'],
                    // 'images' => ['https://example.com/t-shirt.png'],
                ]);
                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => $data_charge['amount'],
                    'currency' => $data_charge['currency'],
                ]);
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => $data_charge['payment_method_types'],
                    'line_items' => [[
                      'quantity' => 1,
                      'price' => $price->id,
                    ]],
                    'mode' => 'payment',
                    'success_url' => $data_charge['success_url'],
                    'cancel_url' => $data_charge['cancel_url'],
                ]);
                $result = (object)array(
                    "status"      => "success",
                    "session"     => $session,
                );
                return $result;
            } catch(Stripe_CardError $e) {
                  $error1 = $e->getMessage();
                  $result = (object)array(
                    "status"      => "error",
                    "message"    => $error1,
                );
                return $result;
            } catch (Stripe_InvalidRequestError $e) {
                  // Invalid parameters were supplied to Stripe's API
                  $error2 = $e->getMessage();
                  $result = (object)array(
                    "status"      => "error",
                    "message"    => $error2,
                );
                return $result;
            } catch (Stripe_AuthenticationError $e) {
                  // Authentication with Stripe's API failed
                  $error3 = $e->getMessage();
                  $result = (object)array(
                    "status"      => "error",
                    "message"    => $error3,
                );
                return $result;
            } catch (Stripe_ApiConnectionError $e) {
                  // Network communication with Stripe failed
                  $error4 = $e->getMessage();
                  $result = (object)array(
                    "status"      => "error",
                    "message"    => $error4,
                );
                return $result;
            } catch (Stripe_Error $e) {
                  // Display a very generic error to the user, and maybe send
                  // yourself an email
                  $error5 = $e->getMessage();
                  $result = (object)array(
                    "status"      => "error",
                    "message"    => $error5,
                );
                return $result;
            } catch (Exception $e) {
                  // Something else happened, completely unrelated to Stripe
                  $error6 = $e->getMessage();
                  $result = (object)array(
                    "status"      => "error",
                    "message"    => $error6,
                );
                return $result;
            }
            
        }else{
            redirect(cn());
        }
    }

    /**
     * Retrieve Stripe data by session Id
     *
     * @param string $sessionId
     *
     * request to Stripe checkout
     *---------------------------------------------------------------- */
    public function retrieveStripeData($sessionId)
    {
        try {

            $sessionData = \Stripe\Checkout\Session::retrieve($sessionId);

            if (empty($sessionData)) {
                throw new Exception("Session data does not exist.");                
            }

            $paymentIntentData = \Stripe\PaymentIntent::retrieve($sessionData->payment_intent);

            return $paymentIntentData;

        } catch (\Stripe\Error\InvalidRequest $err) {
            //set error message if payment failed
            $errorMessage['errorMessage'] = $err->getMessage();

            //return error message array
            return (array) $errorMessage;

        } catch (\Stripe\Error\Card $err) {
            //set error message if payment failed
            $errorMessage['errorMessage'] = $err->getMessage();
            
            //return error message array
            return (array) $errorMessage;
        }
    }

    /**
     * Calculate Stripe Amount
     *
     * @param number $amount - Stripe Amount
     *
     * request to Stripe checkout
     *---------------------------------------------------------------- */
    protected function calculateStripeAmount($amount)
    {
        return $amount * 100;
    }
}


