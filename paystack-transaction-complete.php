<?php
    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $refID = $_GET['oid'];
    $oID = $_GET['oid'];
    $course_id = $_GET['cid'];
    $dis_programme_id = $course_id;
    $it_is_a_subscription_payment = false;

    // Extract the order id from the reference code
    if ($refID) {
        preg_match('/^\d+[S|M](\d+)$/i', $refID, $matches);

        $oID = $matches[1];
    }

    if (preg_match('/subscription/i', $refID)) { // If the payment is for subscription
        $it_is_a_subscription_payment = true;
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$refID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ".PAYSTACK_SECRET_KEY,
            "Cache-Control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $response = json_decode($response);

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
        else { // it is not charity or subscription payment
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
        $transaction_id = $response->data->reference;
        $order_id = $oID;
        $response_message = $response->data->status;
        $customer_email = $response->data->customer->email;
        $customer_name = $row_moreInfo['full_name'];
        $transaction_amount = ((int) $response->data->amount) / 100;
        $transaction_currency = $response->data->currency;
        $trans_date = $response->data->transaction_date;

        $address_shipping = $row_moreInfo['current_address'];
        $city_shipping = $row_moreInfo['address_city'];
        $state_shipping = $row_moreInfo['address_state'];
        $country_shipping = $row_moreInfo['address_country'];
        $postal_code_shipping = $row_moreInfo['address_postal_code'];

        $order_status = $response->data->status;

        // If payment was successful
        if (strtolower($order_status) == 'success') {
            if (!$it_is_a_subscription_payment) { // If it is not a subscription payment
                // Deduct the extra fees from the total amount paid.
                $separate_extra_fees_from_total_amount = true;
                $record_extra_fee_payment_info = false;
                include('inc.extra-fees-full-payment-processing.php');
            }
            elseif ($it_is_a_subscription_payment && $subscription_id) {
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
            require('inc.paystack-record-response.php');

            if (!$it_is_a_subscription_payment) { // If it is not a subscription payment            
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
        
                        // Loop through each of the courses
                        do {
                            // Set the first programme id available in the cart to use in publisher earning
                            /*
                                if (!$dis_programme_id) {
                                    $dis_programme_id = $row_DisProg['programme_id'];
                                }
                            */
                            
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
                
                if (!$do_not_redirect) {
                    redirect(ROOT_DIR_PORTAL.'student/course-welcome');
                }
                else {
                    $courseAssignedSuccessfully = true;
                }
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

            if ($it_is_a_subscription_payment) { // Take the student back to the subscriptions page.
                redirect(ROOT_DIR.'subscription-courses/'.$row_thisSubscriptionDetails['slug']);
            }
            else {
                if ($course_id && ($prog_slug = get_frontend_prog_slug($course_id))) {
                    if (!$do_not_redirect) {
                        redirect(ROOT_DIR.'programme/'.$prog_slug);
                    }
                }
                else {
                    if (!$do_not_redirect) {
                        redirect(ROOT_DIR);
                    }
                }
            }
        }
        
        if (!$do_not_redirect) {
            exit();
        }
    }

    if (!$do_not_redirect) {
        redirect(ROOT_DIR);
    }