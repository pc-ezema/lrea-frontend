<?php
    // For debugging paytabs on localhost
    if ($_GET['initpage'] && preg_match('/localhost/i', $_GET['initpage'])) {
        $test_flutterwave_api = true;

        // Forward the POST values to localhost only if it is online
        if (!preg_match("/^localhost/", $_SERVER["SERVER_NAME"])) { 
            // header('location: http://localhost/'.SITE_NAME_LOWER.'/flutterwave-transaction-complete.php?initpage='.rawurlencode($_GET['initpage']).'&status='.rawurlencode($_GET['status']).'&tx_ref='.rawurlencode($_GET['tx_ref']).'&transaction_id='.rawurlencode($_GET['transaction_id']));
            // exit();
        }
    }

    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $debug_flutterwave_payment_process = true;
            
    /* Debugging initialization starts here */
    if ($debug_flutterwave_payment_process) {
        $debug_file_name = 'flutterwave-debug-'.$_GET['oid'].'.txt';
    }

    if ($debug_flutterwave_payment_process) {
        my_log_to_file(var_dump_ret($_GET), $debug_file_name);
        my_log_to_file(var_dump_ret($_POST), $debug_file_name);
    }

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

            if ($debug_flutterwave_payment_process) {
                my_log_to_file("Checkout ID: ".$oID, $debug_file_name);
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
                    $query_moreInfo = "SELECT usi.*, 
                                        lc.countryName address_country
                                    FROM users_students_info usi 
                                    JOIN users_students us ON us.login_id = usi.login_id 
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

                if ($debug_flutterwave_payment_process) {
                    my_log_to_file(var_dump_ret($query_thisCheckoutDetails), $debug_file_name);
                }
            }
            else {
                if (preg_match('/subscription/i', $oID)) { // If the payment is for subscription
                    $it_is_a_subscription_payment = true;
                }

                if ($it_is_a_subscription_payment) {
                    $exploded_string = explode('_', $oID);
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

            if (!$use_live_flutterwave) {
                //echo '<pre>'; var_dump($response, $row_moreInfo); echo '</pre>'; exit();
            }

            if (strtolower($order_status) == 'successful') { // If payment was successful
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
                require('inc.flutterwave-record-response.php');

                if ($debug_flutterwave_payment_process) {
                    my_log_to_file(var_dump_ret($insertSQL), $debug_file_name);
                }

                if (!$it_is_a_subscription_payment) { // If it is not a subscription payment            
                    // Record the extra fees
                    $separate_extra_fees_from_total_amount = false;
                    $record_extra_fee_payment_info = true;
                    include('inc.extra-fees-full-payment-processing.php');
                    
                    // If INSERT is successful, give access to the student
                    $target_prospect_id = $order_id;

                    // Start recording and applying updates so that student can have access to the course
                    require_once(ROOT_PATH_TO_PORTAL.'inc.student-just-paid.php');

                    if ($debug_flutterwave_payment_process) {
                        my_log_to_file("Course assigned successfully: ".$course_assigned_successfully, $debug_file_name);
                    }

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
                        if ($debug_flutterwave_payment_process) {
                            my_log_to_file(var_dump_ret($query_DisProg), $debug_file_name);
                        }

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

                if ($debug_flutterwave_payment_process) {
                    my_log_to_file("Finished assigning the course and about to redirect to the course welcome page on portal.", $debug_file_name);
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
                    redirect(ROOT_DIR.'programme/'.$prog_slug);
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
            redirect(ROOT_DIR.'programme/'.$prog_slug);
        }
        else {
            redirect(ROOT_DIR);
        }
    }

    redirect(ROOT_DIR);
?>