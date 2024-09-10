<?php
//1. Import the PayPal SDK client
namespace Sample;

require __DIR__ . '/vendor/autoload.php';
use Sample\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

    require_once('connections/config.php');
    require_once('components/misc.inc.php');
    
if (PAYPAL_VERSION == 'sandbox') {
    require 'paypal-client-sandbox-init.php';
}
else {
    require 'paypal-client-production-init.php';
}

$oID = $_GET['oid'];

class GetOrder
{

  // 2. Set up your server to receive a call from the client
  public static function getOrder($oID)
  {
    // 3. Call PayPal to get the transaction details
    $client = PayPalClient::client();
    $response = $client->execute(new OrdersGetRequest($oID));
	
	// 4. Specify which information you want from the transaction details. 
    $transaction_id = $response->result->id;
	$order_id = $response->result->purchase_units[0]->reference_id;
	$response_message = $response->result->status;
	$customer_name = $response->result->purchase_units[0]->shipping->name->full_name;
	$customer_email = $response->result->payer->email_address;
	$transaction_amount = $response->result->purchase_units[0]->amount->value;
	$transaction_currency = $response->result->purchase_units[0]->amount->currency_code;
	$trans_date = $response->result->create_time;
	$address_shipping = $response->result->purchase_units[0]->shipping->address->address_line_1;
	$city_shipping = $response->result->purchase_units[0]->shipping->address->admin_area_2;
	$state_shipping = $response->result->purchase_units[0]->shipping->address->admin_area_1;
	$country_shipping = $response->result->purchase_units[0]->shipping->address->country_code;
	$postal_code_shipping = $response->result->purchase_units[0]->shipping->address->postal_code;
	$order_status = $response->result->status;
    
    // Add this here so that my configuration variables get passed to all functions being called (without it database functions fail)
    include('connections/config.php');
    include('components/misc.inc.php');
    
    $course_id = $_GET['cid'];
    
    // If payment was successful
    if (strtolower($order_status) == 'completed') {
        require("inc.extra-fee-payment-processing.php");

        // Check if student is making a part payment to prevent delivery email being sent multiple times
        require_once("inc.check-student-part-payment-status-for-delivery-email.php");
        
        // Record the transaction in the database
        $payment_domain_country = $_GET['dcon'] ? $_GET['dcon'] : "united-kingdom";
        require('inc.paypal-record-response.php');

        if ($insertStatus >= 0) { 
            // If INSERT is successful, give access to the student
            $target_prospect_id = $order_id;

            // Set this so that it can be used in the part payment processing
            $total_amount_paid = $transaction_amount;

            $active_payment_plan = $_GET['payment_plan'] ? $_GET['payment_plan'] : 9; // Kinda like a failsafe if the $_GET value is not available inside the class

            if ($site_version == 'localhost') {
                /*echo '<pre>total_amount_paid = '; var_dump($total_amount_paid); echo '</pre>';
                echo '<pre>active_payment_plan = '; var_dump($active_payment_plan); echo '</pre>';*/
            }

            // Start processing the payment whether full or part
            require(ROOT_PATH_TO_PORTAL.'inc.part-payment-processing.php');

            // Note the welcome prog_id
            $_SESSION['track_p_id'] = $target_prospect_id;
        }
        
        // Take the student to the student portal dashboard
        $_SESSION['userfeedback'] = "Congratulations. <br>Your payment was successful.";
        $_SESSION['feedbacktype'] = "success";

        redirect(ROOT_DIR_PORTAL.'student/dashboard');
    }
    else {
        // Record the failed attempt
        $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
        $_SESSION['feedbacktype'] = "error";

        if ($course_id && ($prog_slug = get_frontend_prog_slug($course_id))) {
            redirect(ROOT_DIR_PORTAL.'student/dashboard');
        }
        else {
            redirect(ROOT_DIR_PORTAL);
        }
    }
		
	exit();
  }
}

if (!count(debug_backtrace()))
{
  GetOrder::getOrder($oID, true);
}
