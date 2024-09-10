<?php
    // For debugging checkout.com on localhost
    if ($_GET['initpage'] && preg_match('/localhost/i', $_GET['initpage'])) {
        $test_checkoutdotcom_api = true;

        // Forward the POST values to localhost only if it is online
        if (!preg_match("/^localhost/", $_SERVER["SERVER_NAME"])) { 
            header('location: http://localhost/'.SITE_NAME_LOWER.'/checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($_GET['initpage']).'&cid='.$_GET['cid']);
            exit();
        }
    }

    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $real_card_details_demo = $site_version == 'online' ? true : false;
    $it_is_a_subscription_payment = false;

    if (isset($_GET['cko-session-id'])) { // Request to the api was successful. cko-session-id is needed in order to verify the payment.
        if ($site_version == 'online' || $real_card_details_demo) {
            $transaction_id = $_GET['cko-session-id'];
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.checkout.com/payments/".$transaction_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: ".CHECKOUT_GATEWAY_SECRET_KEY
                ),
            ));
            
            $response = curl_exec($curl);
            
            $err = curl_error($curl);
            curl_close($curl);
        } else {
            $response = '{
                "id": "pay_gsdxsysouljujhxgbxectqhqyy",
                "requested_on": "2021-05-24T17:13:35Z",
                "source": {
                    "id": "src_dmbh57vxqnxejglcqwp4rrq5ce",
                    "type": "card",
                    "billing_address": {
                        "country": "GB"
                    },
                    "expiry_month": 4,
                    "expiry_year": 2024,
                    "name": "Asifat Kazeem Adeniyi",
                    "scheme": "Mastercard",
                    "last4": "4318",
                    "fingerprint": "38D203B742E6DAF2DA380088B46B658B1E2BECE71E6B17C489EA4CCB390DFD80",
                    "bin": "539983",
                    "card_type": "Debit",
                    "card_category": "Consumer",
                    "issuer": "GUARANTY TRUST BANK PLC",
                    "issuer_country": "NG",
                    "product_id": "MDU",
                    "product_type": "Debit Unembossed MasterCard® Card",
                    "cvv_check": "Y"
                },
                "amount": 200,
                "currency": "USD",
                "payment_type": "Regular",
                "reference": "1621876346CH8239550",
                "status": "Captured",
                "approved": true,
                "3ds": {
                    "downgraded": false,
                    "enrolled": "Y",
                    "signature_valid": "Y",
                    "authentication_response": "Y",
                    "cryptogram": "jE7A/B/SvMFeCBApaha0AyIAAAA=",
                    "xid": "MDAwMDAwMDAwMDA2NzQyNjcyNzY=",
                    "version": "1.0.2"
                },
                "risk": {
                    "flagged": false
                },
                "customer": {
                    "id": "cus_zkwsgx3b736efoycsx4ckdvhze",
                    "email": "sample@gmail.com",
                    "name": "John Doe"
                },
                "billing_descriptor": {
                    "name": "sample.com",
                    "city": "Dubai"
                },
                "metadata": {
                    "mode": "payment",
                    "cancel_url": "https://sample.com/checkout-dot-com-transaction-complete.php?initpage=https%3A%2F%2Fsample.com%2Fcheckout%2F3dcfc1827f6b5843e7a856b1525952dfa1df3447&cid=8242023",
                    "correlationId": "765e5950-f7c4-4d9d-be3f-e4ebde62197c",
                    "correlation_id": "765e5950-f7c4-4d9d-be3f-e4ebde62197c"
                },
                "actions": [
                    {
                        "id": "act_zj7mrkmzjwgulnbjxs3tgsox6y",
                        "type": "Capture",
                        "response_code": "10000",
                        "response_summary": "Approved"
                    },
                    {
                        "id": "act_gsdxsysouljujhxgbxectqhqyy",
                        "type": "Authorization",
                        "response_code": "10000",
                        "response_summary": "Approved"
                    }
                ],
                "_links": {
                    "self": {
                        "href": "https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy"
                    },
                    "actions": {
                        "href": "https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy/actions"
                    },
                    "refund": {
                        "href": "https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy/refunds"
                    }
                }
            }';
        }

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);

            $refID = $response->reference;
            $course_id = $_GET['cid'];
            $dis_programme_id = $course_id;
        
            // Extract the order id from the reference code
            if ($refID) {
                preg_match('/^\d+[CH]+(\d+)$/i', $refID, $matches);
        
                $oID = $matches[1];
            }

            if ($oID >= 8000000) { // It is a cart payment
                // Locate the checkout details
                mysqli_select_db($config, $database_config);
                $query_thisCheckoutDetails = "SELECT chk.* 
                                                FROM checkout chk 
                                                WHERE chk.checkout_id = '".sql_int($oID)."' 
                                                LIMIT 1";
                $thisCheckoutDetails = mysqli_query($config, $query_thisCheckoutDetails) or die(mysqli_error($config));
                $row_thisCheckoutDetails = mysqli_fetch_assoc($thisCheckoutDetails);
                $totalRows_thisCheckoutDetails = mysqli_num_rows($thisCheckoutDetails);
        
                if ($totalRows_thisCheckoutDetails) {
                    // Get the profile info for the student
                    mysqli_select_db($config, $database_config);
                    $query_moreInfo = "SELECT usi.*, p.*,
                                        lc.countryName address_country
                                    FROM users_students_info usi 
                                    JOIN users_students us ON us.login_id = usi.login_id
                                    JOIN prospects p ON p.email = us.email 
                                    LEFT JOIN loccountries lc ON usi.address_country = lc.countryID 
                                    WHERE us.login_id = ".sql_int($row_thisCheckoutDetails['login_id'])." 
                                    LIMIT 1";
                    $moreInfo = mysqli_query($config, $query_moreInfo) or die(mysqli_error($config));
                    $row_moreInfo = mysqli_fetch_assoc($moreInfo);
                    $totalRows_moreInfo = mysqli_num_rows($moreInfo);
        
                    // If the address doesn't already exist
                    if (!$row_moreInfo['current_address']) {
                        // Use alternative profile info for the student
                        $row_moreInfo['current_address'] = "No delivery address needed for digital product";
                        $row_moreInfo['address_city'] = " - ";
                        $row_moreInfo['address_state'] = " - ";
                        $row_moreInfo['address_country'] = " - ";
                        $row_moreInfo['address_postal_code'] = "No postal code";
                    }
                }
            }
            else {
                if (preg_match('/subscription/i', $refID)) { // If the payment is for subscription
                    $it_is_a_subscription_payment = true;
                }

                if ($it_is_a_subscription_payment) {
                    $exploded_string = explode('_', $refID);
                    $subscription_id = $exploded_string[1];
                    $cannot_proceed_to_record_payment = false;

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
            
                        if ($totalRows_thisSubscriptionDetails) {
                            // Get the profile info for the student
                            mysqli_select_db($config, $database_config);
                            $query_moreInfo = "SELECT usi.*, 
                                                lc.countryName address_country
                                            FROM users_students_info usi 
                                            JOIN users_students us ON us.login_id = usi.login_id 
                                            LEFT JOIN loccountries lc ON usi.address_country = lc.countryID 
                                            WHERE us.login_id = ".sql_int($row_thisSubscriptionDetails['login_id'])." 
                                            LIMIT 1";
                            $moreInfo = mysqli_query($config, $query_moreInfo) or die(mysqli_error($config));
                            $row_moreInfo = mysqli_fetch_assoc($moreInfo);
                            $totalRows_moreInfo = mysqli_num_rows($moreInfo);
            
                            // If the address doesn't already exist
                            if (!$row_moreInfo['current_address']) {
                                // Use alternative profile info for the student
                                $row_moreInfo['current_address'] = "No delivery address needed for digital product";
                                $row_moreInfo['address_city'] = " - ";
                                $row_moreInfo['address_state'] = " - ";
                                $row_moreInfo['address_country'] = " - ";
                                $row_moreInfo['address_postal_code'] = "No postal code";
                            }
                        }
                        else {
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
                else {
                    // Get the profile info for the student
                    mysqli_select_db($config, $database_config);
                    $query_moreInfo = "SELECT usi.*, 
                                        lc.countryName address_country
                                    FROM users_students_info usi 
                                    JOIN users_students us ON us.login_id = usi.login_id 
                                    JOIN prospects p ON p.email = us.email 
                                    LEFT JOIN loccountries lc ON usi.address_country = lc.countryID 
                                    WHERE p.p_id = ".sql_int($oID)." 
                                    LIMIT 1";
                    $moreInfo = mysqli_query($config, $query_moreInfo) or die(mysqli_error($config));
                    $row_moreInfo = mysqli_fetch_assoc($moreInfo);
                    $totalRows_moreInfo = mysqli_num_rows($moreInfo);
            
                    // If the address doesn't already exist
                    if (!$row_moreInfo['current_address']) {
                        // Use alternative profile info for the student
                        mysqli_select_db($config, $database_config);
                        $query_moreInfo = "SELECT p.*, p.curr_address current_address, 
                                            lc.countryName address_country, lc.dialCode address_postal_code, 
                                            branches.branch_name address_state, 
                                            progs.option_value prog_interest2, progs.option_name prog_interest_type, 
                                            status.option_value status2, 
                                            study_centers.countryName as study_center_name
                                        FROM prospects p 
                                        LEFT JOIN loccountries lc ON p.nationality = lc.countryID 
                                        LEFT JOIN loccountries study_centers ON p.study_center = study_centers.countryID 
                                        LEFT JOIN branches ON p.branch = branches.branch_id 
                                        LEFT JOIN options progs ON p.prog_interest = progs.option_id 
                                        LEFT JOIN options status ON p.status = status.option_id 
                                        WHERE p.p_id = ".sql_int($oID)." 
                                        LIMIT 1";
                        $moreInfo = mysqli_query($config, $query_moreInfo) or die(mysqli_error($config));
                        $row_moreInfo = mysqli_fetch_assoc($moreInfo);
                        $totalRows_moreInfo = mysqli_num_rows($moreInfo);
                    }
                }
            }

            // Specify which information you want from the transaction details.
            $transaction_id = $response->id;
            $order_id = $oID;
            $response_message = $response->status;
            $customer_email = $row_moreInfo['email'];
            $customer_name = $row_moreInfo['full_name'];
            $customer_phone = $row_moreInfo['phone'];

            // Convert the amount back to the original value
            if (in_array($response->currency, $checkout_dot_com_currencies_that_accept_full_amount_format)) {
                $transaction_amount = $response->amount;
            } elseif (in_array($response->currency, $checkout_dot_com_1000_format_currencies)) { // The value of the amount whose currencies that fall in this category were multiplied by 1000 when the payment was made.
                $transaction_amount = $response->amount / 1000;
            } else { // The value of the amount whose currencies that fall in this category were multiplied by 100 when the payment was made.
                $transaction_amount = $response->amount / 100;
            }
            
            $transaction_currency = $response->currency;
            $trans_date = $response->requested_on;
            
            $card_brand = $response->source->scheme;
            $last_4_digits = $response->source->last4;

            $address_shipping = $row_moreInfo['current_address'];
            $city_shipping = $row_moreInfo['address_city'];
            $state_shipping = $row_moreInfo['address_state'];
            $country_shipping = $row_moreInfo['address_country'];
            $postal_code_shipping = $row_moreInfo['address_postal_code'];

            $order_status = $response->status;

            if (strtolower($order_status) == 'captured') { // If payment was successful
                if (!$it_is_a_subscription_payment) { // If it is not a subscription payment
                    // Deduct the extra fees from the total amount paid.
                    $separate_extra_fees_from_total_amount = true;
                    $record_extra_fee_payment_info = false;
                    include('inc.extra-fees-full-payment-processing.php');
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
                require('inc.checkout-dot-com-record-response.php');

                if (!$it_is_a_subscription_payment) { // If it is not a subscription payment            
                    // Record the extra fees
                    $separate_extra_fees_from_total_amount = false;
                    $record_extra_fee_payment_info = true;
                    include('inc.extra-fees-full-payment-processing.php');
                    
                    // If INSERT is successful, give access to the student
                    $target_prospect_id = $order_id;

                    // Start recording and applying updates so that student can have access to the course
                    require_once(ROOT_PATH_TO_PORTAL.'inc.student-just-paid.php');

                    // Note the welcome prog_id
                    $_SESSION['track_p_id'] = $target_prospect_id;
                    
                    // Get publisher id
                    $api_Publisher_Id = 0;

                    if (isset($_COOKIE["a_pub_id"])) {
                        $api_Publisher_Id = $_COOKIE["a_pub_id"] - PUBLISHER_INCREMENT;
                    }
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
        
                        // Loop through each of the courses
                        do {
                            // record publisher's earnings
                            if ($row_thisCheckoutDetails['publisher_id']) {
                                record_Api_Publisher_Earnings($row_thisCheckoutDetails['publisher_id'], $row_DisProg['programme_id'], $row_DisProg['discounted_fee'], $row_DisProg['currency']);
                            }
    
                            // check if user has this course in cart and delete it
                            clear_user_cart_details_from_db($row_DisProg['programme_id']);
                        } while($row_DisProg = mysqli_fetch_assoc($DisProg));
                    }
                }
                else {
                    if (!$it_is_a_subscription_payment) { // If it is not a subscription payment
                        // record publisher's earnings
                        if ($api_Publisher_Id) {
                            record_Api_Publisher_Earnings($api_Publisher_Id, $dis_programme_id, $transaction_amount, $transaction_currency);
                        }

                        // check if user has this course in cart and delete it
                        clear_user_cart_details_from_db($dis_programme_id);
                    }
                }

                if ($it_is_a_subscription_payment) { // Take the student back to the subscriptions page.
                    $_SESSION['userfeedback'] = "Congratulations. <br>Your subscription payment was successful. Now, you can enrol in any of the programmes listed in this subscription on the website.";
                    $_SESSION['feedbacktype'] = "success";

                    redirect(ROOT_DIR.'subscription-courses/'.$row_thisSubscriptionDetails['slug']);
                }
                else {
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
            } else {
                // Record the failed attempt
                $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
                $_SESSION['feedbacktype'] = "error";

                if ($_GET['initpage']) {
                    redirect($_GET['initpage']);
                }
                elseif ($course_id && ($prog_slug = get_frontend_prog_slug($course_id))) {
                    redirect(ROOT_DIR.'programme/'.$prog_slug);
                }
                else {
                    redirect(ROOT_DIR);
                }
            }
        }
    } else {
        // Record the failed attempt
        $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
        $_SESSION['feedbacktype'] = "error";

        if ($course_id && ($prog_slug = get_frontend_prog_slug($course_id))) {
            redirect(ROOT_DIR.'programme/'.$prog_slug);
        }
        else {
            redirect(ROOT_DIR.'cart');
        }
    }
?>