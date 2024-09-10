<?php
    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $refID = $_GET['oid'];
    $oID = $_GET['oid'];
    $course_id = $_GET['cid'];

    // Extract the order id from the reference code
    if ($refID) {
        preg_match('/^\d+[S|M](\d+)$/i', $refID, $matches);

        $oID = $matches[1];
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
            require("inc.extra-fee-payment-processing.php");

            // Check if student is making a part payment to prevent delivery email being sent multiple times
            require_once("inc.check-student-part-payment-status-for-delivery-email.php");
            
            // Record the transaction in the database
            $payment_domain_country = $_GET['dcon'] ? $_GET['dcon'] : "united-kingdom";
            require('inc.paystack-record-response.php');

            if ($insertStatus >= 0) { 
                // If INSERT is successful, give access to the student
                $target_prospect_id = $order_id;

                // Set this so that it can be used in the part payment processing
                $total_amount_paid = $transaction_amount;

                $active_payment_plan = $_GET['payment_plan'] ? $_GET['payment_plan'] : 9; // Kinda like a failsafe if the $_GET value is not available inside the class

                // Start processing the payment whether full or part
                require(ROOT_PATH_TO_PORTAL.'inc.part-payment-processing.php');

                // Note the welcome prog_id
                $_SESSION['track_p_id'] = $target_prospect_id;
            }

            $thisPublisherIDInfo = $_COOKIE["a_pub_id"];

            // track publisher's earnings
            if ($thisPublisherIDInfo) {
                $api_Publisher_Id = $thisPublisherIDInfo - PUBLISHER_INCREMENT;

                record_Api_Publisher_Earnings($api_Publisher_Id, $course_id, $transaction_amount, $transaction_currency);
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
                redirect(ROOT_DIR);
            }
        }
            
        exit();
    }

    redirect(ROOT_DIR);