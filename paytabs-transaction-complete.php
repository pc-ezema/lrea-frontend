<?php
    // For debugging paytabs on localhost
    if ($_GET['initpage'] && preg_match('/localhost/i', $_GET['initpage'])) {
        $test_paytabs_api = true;

        // Forward the POST values to localhost only if it is online
        if (!preg_match("/^localhost/", $_SERVER["SERVER_NAME"])) { ?>
            <form id="myForm" action="<?php echo 'http://localhost/'.SITE_NAME_LOWER.'/paytabs-transaction-complete.php?initpage='.rawurlencode($_GET['initpage']); ?>" method="post">
            <?php
                foreach ($_POST as $a => $b) {
                    echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
                }
            ?>
            </form>
            <script type="text/javascript">
                document.getElementById('myForm').submit();
            </script>
            <?php exit();
        }
    }

    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $try_to_verify_the_signature = false;
    $proceed_with_payment = true;
    $it_is_a_subscription_payment = false;

    if ($_POST["signature"]) {
        // Verify the signature
        if ($try_to_verify_the_signature) {
            // Request body include a signature post Form URL encoded field
            // 'signature' (hexadecimal encoding for hmac of sorted post form fields)
            $requestSignature = $_POST["signature"];
            unset($_POST["signature"]);
            $fields = $_POST;

            // Sort form fields 
            ksort($fields);

            // Generate URL-encoded query string of Post fields except signature field.
            $query = http_build_query($fields);
            //$query = rawurldecode($query); // Comment by Tayo: I am using rawurldecode() because without it, the @ symbol in the email address is encoded as %40

            $signature = hash_hmac('sha256', $query, $serverKey);
            if (hash_equals($signature,$requestSignature) === TRUE) {
                // VALID Redirect
                //echo '<p>Valid Hash</p>'; 
            }else{
                // INVALID Redirect
                //echo '<p>Bad Hash</p>'; 
                $proceed_with_payment = false;
            }
        }

        //echo '<pre>'; var_dump($_POST, $signature, $requestSignature, $fields, $query); echo '</pre>'; 

        if ($proceed_with_payment) {
            $refID = $_POST['tranRef'];
            $oID = $_POST['cartId']; // It is called "cartId" by PayTabs not us. Don't let it confuse you.
            $course_id = $_GET['cid'];
            $dis_programme_id = $course_id;

            $data = array(
                "profile_id" => PAYTABS_PROFILE_ID,
                "tran_ref" => $refID,
            );
            
            $payload = json_encode($data);
            
            // Retrieve the details on the payment
            $ch = curl_init('https://secure.paytabs.com/payment/query');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
            // Set HTTP Header for POST request 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    "Authorization: ".PAYTABS_SERVER_KEY
                )
            );
            
            // Submit the POST request
            $result = curl_exec($ch);
            $err = curl_error($ch);
            
            // Close cURL session handle
            curl_close($ch);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $response = json_decode($result);
        
                $order_status = $response->payment_result->response_status;

                //echo '<pre>'; var_dump($response, $order_status, $oID, $_GET); echo '</pre>'; exit(); // For debugging
        
                // If payment was successful
                if (strtolower($order_status) == 'a') {
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
            
                    // Specify which information you want from the transaction details. 
                    $transaction_id = $response->tran_ref;
                    $order_id = $oID;
                    $response_code = $response->payment_result->response_code;
                    $response_message = $response->payment_result->response_message;
                    $customer_email = $response->data->customer_details->email;
                    $customer_name = $row_moreInfo['full_name'];
                    $transaction_amount = $response->cart_amount;
                    $transaction_currency = $response->cart_currency;
                    $trans_date = $response->payment_result->transaction_time;

                    $card_brand = $response->payment_info->card_scheme;
                    $card_number = $response->payment_info->payment_description;
                    $last_4_digits = @end(explode(" ", $card_number));
                    $first_4_digits = @current(explode(" ", $card_number));
            
                    if ($row_moreInfo['current_address']) { // Use the information already available on the student
                        $address_shipping = $row_moreInfo['current_address'];
                        $city_shipping = $row_moreInfo['address_city'];
                        $state_shipping = $row_moreInfo['address_state'];
                        $country_shipping = $row_moreInfo['address_country'];
                        $postal_code_shipping = $row_moreInfo['address_postal_code'];
                    }
                    else { // Use the information returned for the payment
                        $address_shipping = $response->data->customer_details->street1;
                        $city_shipping = $response->data->customer_details->city;
                        $state_shipping = $response->data->customer_details->state;
                        $country_shipping = $response->data->customer_details->country;
                        $postal_code_shipping = $response->data->customer_details->zip;
                    }

                    $transaction_status_info = 'successful';

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
                    require('inc.paytabs-record-response.php');
            
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
                        $_SESSION['userfeedback'] = "Congratulations. <br>You have successfully enrolled for <strong>".ucwords($row_Records['prog_interest2'])."</strong>.";
                        $_SESSION['feedbacktype'] = "success";
                        redirect(ROOT_DIR_PORTAL.'student/course-welcome');
                    }
                }
                else {
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
        }
    }

    redirect(ROOT_DIR);