<?php
    // For debugging paytabs on localhost
    if ($_GET['initpage'] && preg_match('/localhost/i', $_GET['initpage'])) {
        $test_flutterwave_api = true;

        // Forward the POST values to localhost only if it is online
        if (!preg_match("/^localhost/", $_SERVER["SERVER_NAME"])) { 
            // header('location: http://localhost/'.SITE_NAME_LOWER.'/flutterwave-transaction-complete-part-payment.php?initpage='.rawurlencode($_GET['initpage']).'&status='.rawurlencode($_GET['status']).'&tx_ref='.rawurlencode($_GET['tx_ref']).'&transaction_id='.rawurlencode($_GET['transaction_id']));
            // exit();
        }
    }

    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    if (in_array($_GET['status'], array('successful', 'completed')) && isset($_GET['transaction_id'])) { // Request to the api was successful. Transaction id is needed in order to verify the payment.
        $transaction_id = $_GET['transaction_id'];
        $transaction_reference = $_GET['tx_ref'];
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/".$transaction_id."/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer ".FLUTTERWAVE_SECRET_KEY
            ),
        ));
        
        $response = curl_exec($curl);
        
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);

            $oID = $response->data->meta->oID;
            $course_id = $response->data->meta->cID;
            $dis_programme_id = $course_id;
            $payment_plan = $response->data->meta->p_plan;

            /* Debugging initialization starts here */
            if ($debug_flutterwave_payment_process) {
                $debug_file_name = 'flutterwave-debug-'.$oID.'.txt';
            }

            if ($debug_flutterwave_payment_process) {
                my_log_to_file("PID: ".$oID, $debug_file_name);
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

            if ($debug_flutterwave_payment_process) {
                my_log_to_file(var_dump_ret($query_moreInfo), $debug_file_name);
            }

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

                if ($debug_flutterwave_payment_process) {
                    my_log_to_file(var_dump_ret($query_moreInfo), $debug_file_name);
                }
            }

            if ($debug_flutterwave_payment_process) {
                my_log_to_file(var_dump_ret($response), $debug_file_name);
            }

            // Specify which information you want from the transaction details.
            $transaction_id = $response->data->id;
            $order_id = $oID;
            $response_message = $response->data->status;
            $customer_email = $response->data->customer->email;
            $customer_name = $row_moreInfo['full_name'];
            $customer_phone = $response->data->customer->phone_number;
            $transaction_amount = $response->data->amount;
            $transaction_currency = $response->data->currency;
            $trans_date = $response->data->created_at;

            $card_brand = $response->data->card->type;
            $last_4_digits = $response->data->card->last_4digits;
            $first_4_digits = $response->data->card->first_6digits;

            $address_shipping = $row_moreInfo['current_address'];
            $city_shipping = $row_moreInfo['address_city'];
            $state_shipping = $row_moreInfo['address_state'];
            $country_shipping = $row_moreInfo['address_country'];
            $postal_code_shipping = $row_moreInfo['address_postal_code'];

            $order_status = $response->data->status;

            if (strtolower($order_status) == 'successful') { // If payment was successful
                require("inc.extra-fee-payment-processing.php");

                // Check if student is making a part payment to prevent delivery email being sent multiple times
                require_once("inc.check-student-part-payment-status-for-delivery-email.php");
                
                // Record the transaction in the database
                $payment_domain_country = $_GET['dcon'] ? $_GET['dcon'] : "united-kingdom";
                require('inc.flutterwave-record-response.php');

                if ($debug_flutterwave_payment_process) {
                    my_log_to_file(var_dump_ret($insertSQL), $debug_file_name);
                }

                if ($insertStatus >= 0) { 
                    // If INSERT is successful, give access to the student
                    $target_prospect_id = $order_id;
    
                    // Set this so that it can be used in the part payment processing
                    $total_amount_paid = $transaction_amount;
    
                    $active_payment_plan = $payment_plan ? $payment_plan : 9; // Kinda like a failsafe if the $_GET value is not available inside the class
    
                    // Start processing the payment whether full or part
                    require(ROOT_PATH_TO_PORTAL.'inc.part-payment-processing.php');

                    if ($debug_flutterwave_payment_process) {
                        my_log_to_file("Course assigned successfully: ".$course_assigned_successfully, $debug_file_name);
                    }
    
                    // Note the welcome prog_id
                    $_SESSION['track_p_id'] = $target_prospect_id;
                }

                // track publisher's earnings
                if (isset($_COOKIE["a_pub_id"])) {
                    $api_Publisher_Id = $_COOKIE["a_pub_id"] - PUBLISHER_INCREMENT;

                    record_Api_Publisher_Earnings($api_Publisher_Id, $course_id, $transaction_amount, $transaction_currency);
                }

                if ($debug_flutterwave_payment_process) {
                    my_log_to_file("Finished recording payment and about to redirect to student dashboard.", $debug_file_name);
                }
            
                // Take the student to the student portal dashboard
                $_SESSION['userfeedback'] = "Congratulations. <br>Your payment was successful.";
                $_SESSION['feedbacktype'] = "success";
        
                redirect(ROOT_DIR_PORTAL.'student/dashboard');
            } else {
                if ($debug_flutterwave_payment_process) {
                    my_log_to_file("Payment was not successful.", $debug_file_name);
                }

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

            exit();
        }
    } else { // The status could return 'cancelled'. For example, if the user clicks the cancel icon on the flutterwave popup. tx_ref will always get sent back. 
        // Try to extract the programme id from the transaction reference so as to redirect user back to the enrol page with a message.
        $refID = $_GET['tx_ref'];
        
        if ($refID) {
            preg_match('/^\d+[F](\d+)$/i', $refID, $matches);

            $course_id = $matches[1];
        }

        // Record the failed attempt
        if ($_GET['status'] == 'cancelled') {
            $_SESSION['userfeedback'] = "You cancelled your payment. Please initiate the payment process again to complete your enrolment.";
        } else {
            $_SESSION['userfeedback'] = "Your payment was not successful. Please try again later.";
        }
        
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

    redirect(ROOT_DIR);
?>