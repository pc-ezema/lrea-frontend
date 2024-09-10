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
    $dis_programme_id = $course_id;
    $it_is_a_subscription_payment = false;

    if (preg_match('/subscription/i', $order_id)) { // If the payment is for subscription
        $it_is_a_subscription_payment = true;

        $exploded_string = explode('_', $order_id);
        $subscription_id = $exploded_string[1];
        $cannot_proceed_to_record_payment = false;
    }
    
    // If payment was successful
    if (strtolower($order_status) == 'completed') {
        global $enable_prog_certificate_extra_fees, $enable_cert_attestation;

        if (!$it_is_a_subscription_payment) { // If it is not a subscription payment.        
            // Deduct the extra fees from the total amount paid.
            $separate_extra_fees_from_total_amount = true;
            $record_extra_fee_payment_info = false;
            include('inc.extra-fees-full-payment-processing.php');
        }

        if ($it_is_a_subscription_payment) {
            if (sql_int($subscription_id)) {
                // Locate the subscription details
                mysqli_select_db($config, $database_config);
                $query_thisSubscriptionDetails = "SELECT u_sub.*, sub.slug, sub.duration  
                                                FROM users_subscriptions u_sub
                                                JOIN subscriptions sub ON u_sub.sub_id = sub.sub_id 
                                                WHERE u_sub.user_sub_id = '".sql_int($subscription_id)."' 
                                                    AND u_sub.sub_status = 'pending' 
                                                LIMIT 1";
                $thisSubscriptionDetails = mysqli_query($config, $query_thisSubscriptionDetails) or die(mysqli_error($config));
                $row_thisSubscriptionDetails = mysqli_fetch_assoc($thisSubscriptionDetails);
                $totalRows_thisSubscriptionDetails = mysqli_num_rows($thisSubscriptionDetails);

                if (!$totalRows_thisSubscriptionDetails) {
                    $cannot_proceed_to_record_payment = true;
                }
            }
            else {
                $cannot_proceed_to_record_payment = true;
            }

            if ($cannot_proceed_to_record_payment) { // Redirect away since the subscription cannot be found
                // Record the failed attempt
                $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
                $_SESSION['feedbacktype'] = "error";
    
                redirect(ROOT_DIR.'subscriptions');
            }
        }

        if ($it_is_a_subscription_payment && $subscription_id) {
            $order_id = 'subs'.$subscription_id;
            $subscription_end_date = date('Y-m-d', strtotime("+".$row_thisSubscriptionDetails['duration']));

            // Mark the subscription as paid
            mysqli_select_db($config, $database_config);
            $updateSQL = "UPDATE users_subscriptions  
                        SET sub_status = 'active',  
                            start_date = '".sql_string($date_now)."',
                            end_date = '".sql_string($subscription_end_date)."' 
                        WHERE user_sub_id = ".sql_int($subscription_id)." 
                        LIMIT 1";
            $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
            $updateStatus = mysqli_affected_rows($config);
        }

        // Record the transaction in the database
        $payment_domain_country = $_GET['dcon'] ? $_GET['dcon'] : "united-kingdom";
        require('inc.paypal-record-response.php');

        if (!$it_is_a_subscription_payment) { // If it is not a subscription payment.           
            // Record the extra fees
            $separate_extra_fees_from_total_amount = false;
            $record_extra_fee_payment_info = true;
            include('inc.extra-fees-full-payment-processing.php');

            if ($insertStatus >= 0) { 
                // If INSERT is successful, give access to the student
                $target_prospect_id = $order_id;

                // Start recording and applying updates so that student can have access to the course
                require_once(ROOT_PATH_TO_PORTAL.'inc.student-just-paid.php');

                // Note the welcome prog_id
                $_SESSION['track_p_id'] = $target_prospect_id;
            }

            // Get publisher id
            $api_Publisher_Id = 0;

            $thisPublisherIDInfo = $_COOKIE["a_pub_id"];

            if ($thisPublisherIDInfo) {
                $api_Publisher_Id = $thisPublisherIDInfo - PUBLISHER_INCREMENT;
            }

            // If it is a cart payment, use the first programme id in the cart
            if ($target_prospect_id >= 8000000) { 
                // Get all programmes in the checkout
                $query_DisProg = "SELECT np.*, oprel.option_two, op.option_value  
                                FROM option_relationships oprel 
                                JOIN options op ON oprel.option_two = op.option_id 
                                JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                WHERE oprel.relationship_name = 'programme type' 
                                    AND np.programme_status = 'active' 
                                    AND np.programme_id IN (
                                        SELECT cc.prog_id
                                        FROM checkout_courses cc 
                                        JOIN checkout chk ON cc.checkout_id = chk.checkout_id 
                                        WHERE chk.checkout_id = '".sql_int($target_prospect_id)."' 
                                    )
                                    AND np.deleted = '0'"; 
                $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                $row_DisProg = mysqli_fetch_assoc($DisProg);
                $totalRows_DisProg = mysqli_num_rows($DisProg);

                if ($totalRows_DisProg) {
                    // Initially unset the programme id that might exist
                    $dis_programme_id = 0;

                    // Get all programmes in the checkout
                    $query_Publisher_Id = "SELECT publisher_id
                                        FROM checkout
                                        WHERE checkout_id = '".sql_int($target_prospect_id)."'
                                        LIMIT 1"; 
                    $Publisher_Id = mysqli_query($config, $query_Publisher_Id) or die(mysqli_error($config));
                    $row_Publisher_Id = mysqli_fetch_assoc($Publisher_Id);
                    $totalRows_Publisher_Id = mysqli_num_rows($Publisher_Id);

                    // Loop through each of the courses
                    do {
                        // Set the first programme id available in the cart to use in publisher earning
                        /*
                            if (!$dis_programme_id) {
                                $dis_programme_id = $row_DisProg['programme_id'];
                            }
                        */
                        
                        // record publisher's earnings
                        if ($totalRows_Publisher_Id && $row_Publisher_Id['publisher_id']) {
                            record_Api_Publisher_Earnings($row_Publisher_Id['publisher_id'], $row_DisProg['programme_id'], $row_DisProg['discounted_fee'], $row_DisProg['currency']);
                        }

                        // check if user has this course in cart and delete it
                        clear_user_cart_details_from_db($row_DisProg['programme_id']);
                    } while($row_DisProg = mysqli_fetch_assoc($DisProg));
                }
            }
            else {
                // record publisher's earnings
                if ($api_Publisher_Id) {
                    record_Api_Publisher_Earnings($api_Publisher_Id, $dis_programme_id, $transaction_amount, $transaction_currency);
                }

                // check if user has this course in cart and delete it
                clear_user_cart_details_from_db($dis_programme_id);
            }

            // Take the student to the student portal dashboard
            if ($row_Records['prog_interest2']) {
                $_SESSION['userfeedback'] = "Congratulations. <br>You have successfully enrolled for <strong>".ucwords($row_Records['prog_interest2'])."</strong>.";
            }
            else {
                $_SESSION['userfeedback'] = "Congratulations. <br>Your enrolment was successful.";
            }
            $_SESSION['feedbacktype'] = "success";
            redirect(ROOT_DIR_PORTAL.'student/course-welcome');
        }
        elseif ($it_is_a_subscription_payment) { // Take the student back to the subscriptions page.
            $_SESSION['userfeedback'] = "Congratulations. <br>Your subscription payment was successful. Now, you can enrol in any of the programmes listed in this subscription on the website.";
            $_SESSION['feedbacktype'] = "success";

            redirect(ROOT_DIR.'subscription-courses/'.$row_thisSubscriptionDetails['slug']);
        }
    }
    else {
        // Record the failed attempt
        $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
        $_SESSION['feedbacktype'] = "error";

        if ($course_id && ($prog_slug = get_frontend_prog_slug($course_id))) {
            redirect(ROOT_DIR.'programme/'.$prog_slug);
        }
        else {
            redirect(ROOT_DIR);
        }
    }
		
	exit();
  }
}

if (!count(debug_backtrace()))
{
  GetOrder::getOrder($oID, true);
}
