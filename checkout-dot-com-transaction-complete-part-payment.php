<?php
    // For debugging checkout.com on localhost
    if ($_GET['initpage'] && preg_match('/localhost/i', $_GET['initpage'])) {
        $test_checkoutdotcom_api = true;

        // Forward the POST values to localhost only if it is online
        if (!preg_match("/^localhost/", $_SERVER["SERVER_NAME"])) { 
            if ($_GET['ch']) {
                $da_url_append = "&ch=".$_GET['ch'];
            }

            header('location: http://localhost/'.SITE_NAME_LOWER.'/checkout-dot-com-transaction-complete-part-payment.php?initpage='.rawurlencode($_GET['initpage']).'&cid='.$_GET['cid'].'&cko-session-id='.$_GET['cko-session-id']).$da_url_append;
            exit();
        }
    }

    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $real_card_details_demo = $site_version == 'online' ? false : false;

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
            $ch_ref = $_GET['ch'] ? $_GET['ch'] : 68;
            $mod_ref = mt_rand(123456, 999999);

            $response = '{
                "id": "pay_gsdxsysouljujhxgbxec'.$mod_ref.'",
                "requested_on": "2021-05-24T17:13:35Z",
                "source": {
                    "id": "src_dmbh57vxqnxejglcqwp4'.$mod_ref.'",
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
                "reference": "1622736637CH'.$ch_ref.'",
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
                    "name": "www.sample.org.uk",
                    "city": "Dubai"
                },
                "metadata": {
                    "mode": "payment",
                    "cancel_url": "https://sample.org.uk/checkout-dot-com-transaction-complete.php?initpage=https%3A%2F%sample.org.uk%2Fcheckout%2F3dcfc1827f6b5843e7a856b1525952dfa1df3447&cid=8242023",
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
            $payment_plan = $_GET['payment_plan'];
        
            // Extract the order id from the reference code
            if ($refID) {
                preg_match('/^\d+[CH]+(\d+)$/i', $refID, $matches);
        
                $oID = $matches[1];
            }

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

            if (strtolower($order_status) == 'captured' && $order_id) { // If payment was successful
                require("inc.extra-fee-payment-processing.php");
                
                // Check if student is making a part payment to prevent delivery email being sent multiple times
                require_once("inc.check-student-part-payment-status-for-delivery-email.php");
                
                // Record the transaction in the database
                $payment_domain_country = $_GET['dcon'] ? $_GET['dcon'] : "united-kingdom";
                require('inc.checkout-dot-com-record-response.php');

                // If INSERT is successful, give access to the student
                $target_prospect_id = $order_id;

                // Set this so that it can be used in the part payment processing
                $total_amount_paid = $transaction_amount;

                $active_payment_plan = $payment_plan ? $payment_plan : 9; // Kinda like a failsafe if the $_GET value is not available inside the class

                $payment_made_using_checkout_dot_com = true;

                // Start processing the payment whether full or part
                require(ROOT_PATH_TO_PORTAL.'inc.part-payment-processing.php');

                // Note the welcome prog_id
                $_SESSION['track_p_id'] = $target_prospect_id;

                // track publisher's earnings
                if (isset($_COOKIE["a_pub_id"])) {
                    $api_Publisher_Id = $_COOKIE["a_pub_id"] - PUBLISHER_INCREMENT;

                    record_Api_Publisher_Earnings($api_Publisher_Id, $course_id, $transaction_amount, $transaction_currency);
                }
            
                // Take the student to the student portal dashboard
                $_SESSION['userfeedback'] = "Congratulations. <br>Your payment was successful.";
                $_SESSION['feedbacktype'] = "success";
        
                redirect(ROOT_DIR_PORTAL.'student/dashboard');
            } else {
                // Record the failed attempt
                $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
                $_SESSION['feedbacktype'] = "error";
                
                if ($_GET['initpage']) {
                    redirect($_GET['initpage']);
                }
                elseif ($course_id && ($prog_slug = get_frontend_prog_slug($course_id))) {
                    redirect(ROOT_DIR_PORTAL.'student/dashboard');
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
?>