<?php 
    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $disable_email_verification = true;

    $is_cart_checkout = false;
    $direct_enrol_for_free_course = false;
    $option_count = 0;
    $option_value = array(
        1 => "A",
        2 => "B",
        3 => "C",
        4 => "D",
        5 => "E",
        6 => "F",
        7 => "G",
    );

    $part_payment_allowed = 0;
    $active_payment_plan = 0;
    $dis_is_instalment_payment = false;
    $redirect_to_payments_page_on_portal = false;
    $execute_for_dataHash_checkout_event_only = false;

    if ($_GET['utm_ref'] == 'cart_dropdown') { // This is used to to set the session needed to prepare the code for Facebook and Tiktok intiate checkout events.
        $_SESSION['initiate_checkout_event'] = true;

        redirect(ROOT_DIR.'checkout');
    }
    
    if ($coresite_page_segment_2_slug && in_array($coresite_page_segment_1_slug, array('enrol', 'instalment', 'subscription-enrol'))) {
        if ($coresite_curr_country_slug) {
            $sql_course_country = is_country($coresite_curr_country_slug, "countryID");
        }
        else {
            $sql_course_country = "";
        }

        if ($sql_course_country) {
            $country_max_no_of_instalments_join_additional = " LEFT JOIN content_info_to_country citc_install ON citc_install.cic_title = np.programme_id AND citc_install.cic_country_id = '".$sql_course_country."' AND citc_install.cic_type = 'max-instalments' AND citc_install.cic_status = 'active' ";
            $country_max_no_of_instalments_column = ", citc_install.cic_body as country_max_instalment ";
        }

        // Check for the programme
        mysqli_select_db($config, $database_config);
        $query_DisProg = "SELECT np.*, oprel.option_two, op.option_value ".$country_max_no_of_instalments_column." 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                        ".$country_max_no_of_instalments_join_additional."
                        WHERE oprel.relationship_name = 'programme type' 
                            AND np.programme_status = 'active' 
                            AND np.url_name = '".sql_string($coresite_page_segment_2_slug)."' 
                            AND np.deleted = '0'
                        LIMIT 1";
        $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
        $row_DisProg = mysqli_fetch_assoc($DisProg);
        $totalRows_DisProg = mysqli_num_rows($DisProg);
    
        //echo '<pre>'; var_dump($row_DisProg); echo '</pre>';
    
        if (!$totalRows_DisProg) {
            include_once('404.php');
            exit();
        }

        if ($coresite_page_segment_1_slug == 'instalment' && $enable_part_payment_in_cart) {
            $dis_is_instalment_payment = true;
        }
    }
    elseif ($coresite_page_segment_1_slug == 'checkout') {
        $is_cart_checkout = true;

        $checkout_uniq_id = $coresite_page_segment_2_slug;
        $coresite_page_segment_slug_combined = $coresite_page_segment_1_slug; // So that country switching also changes the currency
        unset($coresite_page_segment_2_slug);
    }
    else {
        include_once('404.php');
        exit();
    }   

    $feature_testers_email = array(
        'demo.user@example.com', 
        'tester2@site.com',
        'tester3@site.com', 
    );

    if (is_array($feature_testers_email) && in_array($_SESSION['email'], $feature_testers_email)) {
        $demo_flutterwave_payment = false;       
        $demo_checkout_dot_com_payment = false; 

        if ($date_now <= '2021-08-06') {
            $demo_stripe_payment = true; 
        }
    }

    if (!$is_cart_checkout) {
        // Get the details of the current course
        require('inc.programme-variables.php');

        $dont_allow_enrol_feature = false;

        // Check to see if programme is allowed to be enrolled into automatically
        if ($dis_programme_category == 'training') {
            // Don't allow if it is a Training
            $dont_allow_enrol_feature = true;
        }
        elseif ($dis_programme_type == 'free online course') {
            $direct_enrol_for_free_course = true;
        }
        elseif ($dis_programme_type != 'short course') { 
            // Don't allow if it is NOT a Short Course (But later we will just funnel them to the Profile Edit page)
            //$dont_allow_enrol_feature = true;

            $send_to_profile_for_completion = true;

            //foreach ($our_unique_site_domains as $uniqueDomainRoot) {
            //  setcookie('enr', $dis_prog_id, time() + (24*60*60*3), "/", $uniqueDomainRoot);
            //}
        }
        elseif (!can_register_when($dis_programme_id, 'anytime')) { 
            // If it can't be registered for in this month
            if (!can_register_when($dis_programme_id, date("F"))) {
                $dont_allow_enrol_feature = true;
            }
        }

        // If enrolment is disabled
        if ($dont_allow_enrol_feature) { 
            include_once('404.php');
            exit();
        }

        // Don't collect money for contentious branches
        if (in_array($coresite_curr_state_slug, $states_to_avoid_payment)) { 
            if ($dis_programme_type == 'short course') { // Do not collect money for short courses
                $do_not_collect_money = true;
            }
        }
        
        if ($dis_is_instalment_payment && $_GET['plan']) { // Validate the course eligibily for part-payment and confirm the payment plan.
            $active_payment_plan = $_GET['plan'] - INSTALMENT_PLAN_OBFUSCATION;

            include('inc.programme-part-payment-eligibility-calculator.php');
            
            if (!array_key_exists($active_payment_plan, $instalment_months) || !$part_payment_allowed) { // If URL was manipulated and wrong payment plan value was passed.
                include_once('404.php');
                exit();
            }
            
            // Calculate the date of the first instalment and the amount which will be used as the bedrock of the loop for the payment plan on the sidebar.
            $student_number_of_instalments = $part_payment_allowed / $active_payment_plan; // $active_payment_plan is the "months per instalment" that the student selected on the cart page.

            // Re-calculate the increment percentage for this instalment
            $instalmental_fee_percentage_increase = get_increment_percentage($student_number_of_instalments);

            $profile_student_amount_incremented = round($profile_student_amount + ($profile_student_amount * $instalmental_fee_percentage_increase), 2); // Increase by 20 percent
            $profile_student_amount_saved = round($profile_student_amount_incremented - $profile_student_amount, 2); 
            $profile_monthly_amount = round($profile_student_amount_incremented / $part_payment_allowed, 2);

            $student_amount_per_instalment = round($profile_monthly_amount * $active_payment_plan, 2);
            $student_first_payment_date = date("Y-m-d");
            $instalment_serial_no = 1;

            if ($enable_cert_attestation) {
                $cert_attestation_fee_cookie_value = $_COOKIE['s2certattestion'];
                $progs_in_certificate_attestation_array = array();
                $instalment_cert_attest_fee = $certificate_attestation_fees["fee"];

                if ($cert_attestation_fee_cookie_value) {
                    $progs_in_certificate_attestation_array = explode(",", $cert_attestation_fee_cookie_value);
                }

                if (count($progs_in_certificate_attestation_array) && in_array($dis_programme_id, $progs_in_certificate_attestation_array)) { // Add the attestation fees for certificate.
                    if ($certificate_attestation_fees["currency"] != $dis_programme_currency) { // Get the attestation fee equivalent amount of the chosen country.
                        $instalment_cert_attest_fee = convert_amount_from_currency($certificate_attestation_fees["currency"], $dis_programme_currency, $certificate_attestation_fees["fee"]);
                    }
                }
            }
        }
        elseif (($dis_is_instalment_payment && !$_GET['plan']) || !$enable_part_payment_in_cart) { // Redirect to cart page in case the student tries to change location from the menu dropdown because the $_GET['plan'] will be stripped out of the URL after the page reloads OR When the whole part-payment feature has been disabled.
            redirect(ROOT_DIR.'cart');
        }
    }

    // Authorized payment gateways
    $allow_paytabs_payment = false;
    $test_paytabs = false;
    $allow_paypal_payment = true;
    $allow_2checkout_payment = false;
    $allow_stripe_payment = true;
    $allow_cellulant_payment = false;
    $payment_domain_country = $coresite_curr_country_slug ? $coresite_curr_country_slug : "united-kingdom";

    if (defined('FLUTTERWAVE_SECRET_KEY')) {
        $allow_flutterwave_payment = false; // This line should be changed to true if the feature has been tested and considered okay.
        /**
         * Test Card: 5531 8866 5214 2950
         * CVV: 564
         * Expiry: 09/32
         * OTP: 12345
         * PIN: 3310
         */
    }

    if (defined('CHECKOUT_GATEWAY_SECRET_KEY')) {
        $allow_checkoutdotcom_payment = false; // This line should be changed to true if the feature has been tested and considered okay.
    }

    if (defined('PAYSTACK_PUBLIC_KEY') && (PAYSTACK_PUBLIC_KEY) && !$do_not_collect_money) {
        $allow_paystack_payment = true;

        if ($dis_programme_type == 'short course') { // Do not use Paystack for short courses
            //$allow_paystack_payment = false;
        }

        if (is_array($feature_testers_email) && !in_array($_SESSION['email'], $feature_testers_email)) {
            //$allow_paystack_payment = false;
        }
    }

    if (!$is_cart_checkout) {
        if ($dis_is_instalment_payment) { // Instalment payment url preparations
            $this_signin_link = ROOT_DIR.$location_slug.'instalment/'.$dis_programme_url.'?plan='.$_GET['plan'].'&signin=true';
            $this_signup_link = ROOT_DIR.$location_slug.'instalment/'.$dis_programme_url.'?plan='.$_GET['plan'];
            $current_checkout_page_url = ROOT_DIR.$location_slug.'instalment/'.$dis_programme_url.'?plan='.$_GET['plan'];

            $redirect_to_payments_page_on_portal = true;
        } 
        else {
            $this_signin_link = ROOT_DIR.$location_slug.'enrol/'.$dis_programme_url.'?signin=true';
            $this_signup_link = ROOT_DIR.$location_slug.'enrol/'.$dis_programme_url;
            $current_checkout_page_url = ROOT_DIR.$location_slug.'enrol/'.$dis_programme_url;
        }
    }
    else {
        $this_signin_link = ROOT_DIR.$location_slug.'checkout/'.$checkout_uniq_id.'?signin=true';
        $this_signup_link = ROOT_DIR.$location_slug.'checkout/'.$checkout_uniq_id;
        $current_checkout_page_url = ROOT_DIR.$location_slug.'checkout/'.$checkout_uniq_id;
    }

    // If 2checkout payment was processed
    if ($_REQUEST['order_number'] && $_REQUEST['key'] && $_REQUEST['sid'] == TWOCHECKOUT_MERCHANT_CODE) {
        // Validation transaction
        $hashSecretWord = TWOCHECKOUT_SECRET_KEY; //2Checkout Secret Word
        $hashSid = TWOCHECKOUT_MERCHANT_CODE; //2Checkout account number
        $hashTotal = $_REQUEST['total']; //Sale total to validate against
        $hashOrder = $_REQUEST['order_number']; //2Checkout Order Number
        $StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));

        //echo '<pre>'; var_dump($_REQUEST); echo '</pre>'; exit();
        
        if ($StringToHash != $_REQUEST['key']) {
            $hash_is_valid = true; 
        } else { 
            $hash_is_valid = false;
        }
        
        // If transaction was successful
        if ($hash_is_valid) {
            $transaction_status_info = 'successful';

            // Record the transaction in the database
            require('inc.2checkout-record-response.php');

            if ($insertStatus >= 0) { 
                // If INSERT is successful, give access to the student
                $target_prospect_id = $_REQUEST['merchant_order_id'];

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
                    record_Api_Publisher_Earnings($api_Publisher_Id, $dis_programme_id, $_REQUEST['total'], $_REQUEST['currency_code']);
                }

                // check if user has this course in cart and delete it
                clear_user_cart_details_from_db($dis_programme_id);
            }

            // Take the student to the student portal dashboard
            if ($row_DisProg['programme_name']) {
                $_SESSION['userfeedback'] = "Congratulations. <br>You have successfully enrolled for <strong>".ucwords($row_DisProg['programme_name'])."</strong>.";
            }
            else {
                $_SESSION['userfeedback'] = "Congratulations. <br>Your enrolment was successful.";
            }
            $_SESSION['feedbacktype'] = "success";
            redirect(ROOT_DIR_PORTAL.'student/course-welcome');
        }
        else { // If transaction failed
            $transaction_status_info = 'unsuccessful';

            // Record the transaction in the database
            require('inc.2checkout-record-response.php');

            $transaction_failed_message = 'Your payment was unsuccessful. Please allow for some time and try again.';
        }
    }

	// If signin/signup form is submitted
	if ($_POST['signin']) {
        $return_here = $current_checkout_page_url;
        
        require_once(ROOT_PATH_TO_PORTAL.'inc.signin-processing.php');
    }
    elseif ($_POST['signup']) {
        $return_here = $current_checkout_page_url;
        
        require_once(ROOT_PATH_TO_PORTAL.'inc.signup-processing.php');
    }
    else {
        // Put Initiate Checkout code here
        if ($_SESSION['initiate_checkout_event']) {
            if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in
                if ($checkout_uniq_id) {
                    // Get all matching programmes 
                    mysqli_select_db($config, $database_config);
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
                                            WHERE chk.uniq_id = '".sql_string($checkout_uniq_id)."' 
                                                AND chk.login_id = '".sql_int($sess_user_id)."'
                                        )
                                        AND np.deleted = '0'";
                    $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                    $row_DisProg = mysqli_fetch_assoc($DisProg);
                    $totalRows_DisProg = mysqli_num_rows($DisProg);
                }
            }
            elseif ($_COOKIE['s2cart']) {
                $retrieved_Cart_Info = array();

                $retrieved_Cart_Info = explode(",", $_COOKIE['s2cart']);

                if (count($retrieved_Cart_Info)) {
                    // Get all matching programmes 
                    mysqli_select_db($config, $database_config);
                    $query_DisProg = "SELECT np.*, oprel.option_two, op.option_value  
                                    FROM option_relationships oprel 
                                    JOIN options op ON oprel.option_two = op.option_id 
                                    JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                    WHERE oprel.relationship_name = 'programme type' 
                                        AND np.programme_status = 'active' 
                                        AND np.programme_id IN (".implode(",", $retrieved_Cart_Info).")
                                        AND np.deleted = '0'"; 
                    $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                    $row_DisProg = mysqli_fetch_assoc($DisProg);
                    $totalRows_DisProg = mysqli_num_rows($DisProg);
                }
            }

            if ($totalRows_DisProg) {
                unset($_SESSION['initiate_checkout_event']); // This session was moved here because if a student is signed in already before reaching here, this variable $checkout_uniq_id wouldn't exist until the page reloads but the session would've been unset if it was still placed below where it was before i moved it here.

                $is_allowed_to_track_fb_initiate_checkout_event = $is_allowed_to_track_fb_add_payment_info_event = true;
                $is_allowed_to_track_tiktok_initiate_checkout_event = $is_allowed_to_track_tiktok_add_payment_info_event = true;

                $dis_initiate_checkout_val = 0;
                $fb_capi_content_ids = $tiktok_contents = array();

                do {
                    $fb_capi_content_ids[] = $row_DisProg['course_code'];

                    // Tiktok
                    $tiktok_contents[] = array(
                        'price' => round($row_DisProg['discounted_fee']),
                        'quantity' => 1,
                        'content_type' => 'course',
                        'content_id' => $row_DisProg['course_code'],
                    );

                    $dis_initiate_checkout_val = $dis_initiate_checkout_val + $row_DisProg['discounted_fee'];
                } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }
            
                if ($enable_facebook_capi_events_tracking) {
                    $facebook_pixel_unique_event_id = $facebook_initiate_checkout_event_id = array();

                    foreach ($conversion_api_tokens_and_pixels as $key => $value) {
                        $facebook_pixel_unique_event_id[$key] = generate_facebook_event_id(); 

                        $fb_initiate_checkout_browser_script = $fb_initiate_checkout_server_script = $fb_add_payment_info_browser_script = $fb_add_payment_info_server_script = "";
                            
                        include('inc.facebook-capi-general-parameters.php'); // This variable $capi_additional_object is initialized in this file.
                        
                        // Prepare ViewContent specific parameters for this page.
                        $capi_additional_object["value"] = round($dis_initiate_checkout_val, 2);
                        $capi_additional_object["currency"] = BASE_CURRENCY; // e.g AED
                        $capi_additional_object["content_type"] = 'product';
                        $capi_additional_object["content_ids"] = $fb_capi_content_ids;

                        if (count($capi_additional_object)) {
                            $fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
                        }

                        if ($facebook_pixel_unique_event_id[$key]) {                        
                            $fb_initiate_checkout_browser_script = "<script>
                                                                        fbq('init', '".$value['pixel']."');
                                                                        fbq('track', 'InitiateCheckout'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_unique_event_id[$key]."'});
                                                                    </script>";

                            $fb_initiate_checkout_server_script = "<script>
                                                                $(function() {
                                                                    $.ajax({
                                                                        url: '".$link_root_default."capi/se',
                                                                        method: 'POST',
                                                                        data: {
                                                                            en: 'InitiateCheckout',
                                                                            eid: '".$facebook_pixel_unique_event_id[$key]."',
                                                                            eip: '".$capi_ip_addr."',
                                                                            eagent: '".$capi_user_agent."',
                                                                            ecur: '".BASE_CURRENCY."',
                                                                            eval: ".round($dis_initiate_checkout_val, 2).",
                                                                            enum: ".$totalRows_DisProg.",
                                                                            ecoid: ".json_encode($fb_capi_content_ids).",
                                                                            etype: 'product',
                                                                            ".$capi_fbp."
                                                                            ".$capi_fbc."
                                                                            ".$capi_email."
                                                                        }
                                                                    });
                                                                });
                                                            </script>";
                        }

                        $facebook_initiate_checkout_event_id[$key] = generate_facebook_event_id();

                        if ($facebook_initiate_checkout_event_id[$key]) {
                            $fb_add_payment_info_browser_script = "<script>
                                                                        fbq('init', '".$value['pixel']."');
                                                                        fbq('track', 'AddPaymentInfo'".$fb_capi_init_string.", {eventID: '".$facebook_initiate_checkout_event_id[$key]."'});
                                                                    </script>";     
            
                            $fb_add_payment_info_server_script = "<script>
                                                                    $(function() {
                                                                        $.ajax({
                                                                            url: '".$link_root_default."capi/se',
                                                                            method: 'POST',
                                                                            data: {
                                                                                en: 'AddPaymentInfo',
                                                                                eid: '".$facebook_initiate_checkout_event_id[$key]."',
                                                                                eip: '".$capi_ip_addr."',
                                                                                eagent: '".$capi_user_agent."',
                                                                                eurl: '".$fb_evt_url."',
                                                                                ecur: '".BASE_CURRENCY."',
                                                                                eval: ".round($dis_initiate_checkout_val, 2).",
                                                                                ecoid: ".json_encode($fb_capi_content_ids).",
                                                                                etype: 'product',
                                                                                ".$capi_fbp."
                                                                                ".$capi_fbc."
                                                                                ".$capi_email."
                                                                            }
                                                                        });
                                                                    });
                                                                </script>";
                        }
                    }
                }

                if ($enable_tiktok_events_tracking) {
                    $tiktok_initiate_checkout_event = $tiktok_add_payment_info_event = "";

                    include('inc.tiktok-events-general-parameters.php');

                    $tiktok_initiate_checkout_event = "<script>
                                                        $(function() {
                                                            $.ajax({
                                                                url: '".$link_root_default."tiktok/se',
                                                                method: 'POST',
                                                                data: {
                                                                    en: 'InitiateCheckout',
                                                                    eip: '".$tiktok_ip_addr."',
                                                                    eagent: '".$tiktok_user_agent."',
                                                                    eurl: '".$tiktok_evt_url."',
                                                                    eval: ".round($dis_initiate_checkout_val, 2).",
                                                                    ecur: '".BASE_CURRENCY."',
                                                                    cont: ".json_encode(iso88591_to_utf8($tiktok_contents)).",
                                                                    ".$tiktok_clid."
                                                                    ".$tiktok_email."
                                                                }
                                                            });
                                                        });
                                                    </script>";

                    $tiktok_add_payment_info_event = "<script>
                                                        $(function() {
                                                            $.ajax({
                                                                url: '".$link_root_default."tiktok/se',
                                                                method: 'POST',
                                                                data: {
                                                                    en: 'AddPaymentInfo',
                                                                    eip: '".$tiktok_ip_addr."',
                                                                    eagent: '".$tiktok_user_agent."',
                                                                    eurl: '".$tiktok_evt_url."',
                                                                    eval: ".round($dis_initiate_checkout_val, 2).",
                                                                    ecur: '".BASE_CURRENCY."',
                                                                    cont: ".json_encode(iso88591_to_utf8($tiktok_contents)).",
                                                                    ".$tiktok_clid."
                                                                    ".$tiktok_email."
                                                                }
                                                            });
                                                        });
                                                    </script>";
                }
            }
        }
    }

    // Perform coupon discount checks
    $all_valid_discounted_prog = array();

    $coupon_id = $this_coupon_discount = 0;
    $thisCouponCodeInfo = $_COOKIE['coupon_code'];

    if ($thisCouponCodeInfo) {
        $user_signed_in = false;

        if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') {
            $user_signed_in = true;
        }
        
        $coupon_to_check = $thisCouponCodeInfo;
        include('inc.coupon-validity-checker.php');

        if ($totalRows_checkifCouponValid) {
            // check if coupon is valid for all programmes
            mysqli_select_db($config, $database_config);
            $query_couponValidForAllProgs = "SELECT *
                                            FROM coupon_programmes
                                            WHERE coupon_id = ".sql_int($row_checkifCouponValid['coupon_id'])."
                                                AND prog_id > 7000000
                                            LIMIT 1";
            $couponValidForAllProgs = mysqli_query($config, $query_couponValidForAllProgs) or die(mysqli_error($config));
            $row_couponValidForAllProgs = mysqli_fetch_assoc($couponValidForAllProgs);
            $totalRows_couponValidForAllProgs = mysqli_num_rows($couponValidForAllProgs);


            $this_coupon_discount = $row_checkifCouponValid['coupon_discount'];
            $coupon_id = $row_checkifCouponValid['coupon_id'];
        }
    }
    
    // If student is logged in
    if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') {
        if (!$is_cart_checkout) {
            if ($coresite_page_segment_1_slug == 'subscription-enrol') {
                // Check if this student has any subscription at all.
                mysqli_select_db($config, $database_config);
                $query_ActiveSubscription = "SELECT *
                                            FROM users_subscriptions 
                                            WHERE login_id = ".sql_int($sess_user_id)." 
                                                AND sub_status = 'active' 
                                                AND end_date > '".sql_string($date_now)."'
                                            LIMIT 1";
                $ActiveSubscription = mysqli_query($config, $query_ActiveSubscription) or die(mysqli_error($config));
                $row_ActiveSubscription = mysqli_fetch_assoc($ActiveSubscription);
                $totalRows_ActiveSubscription = mysqli_num_rows($ActiveSubscription);
    
                if ($totalRows_ActiveSubscription) { // There's an active subscription.
                    $enrolled_via_subscription = false;
                    $total_programmes_enrolled_through_subscription = array();
    
                    // Check if the current programme is included in any active student subscriptions
                    $programme_in_subscription = find_active_student_programme_subscription($row_DisProg['programme_id']);
    
                    // The array will be empty if the programme is not among the selected programmes of the subscription
                    if (count($programme_in_subscription)) {
                        // Retrieve the subscription ID for the user and the URL for the programme
                        $user_sub_id = $programme_in_subscription[$row_DisProg['programme_id']];
                        $dis_programme_url = $row_DisProg['url_name'] ? $row_DisProg['url_name'] : '';
    
                        $do_not_redirect_after_creating_prospect = true;
    
                        // Fetch information about any older version of the programme
                        $dis_old_prog_id = get_old_programme_info($row_DisProg['programme_id'], 'option_id');
    
                        if ($dis_old_prog_id) {
                            // Check if the student is already a prospect for this programme
                            $dis_order_id = student_is_a_prospect($_SESSION['email'], $dis_old_prog_id);
    
                            // If the student is not a prospect, include a file to handle new prospect creation and set a value for $dis_order_id
                            if (!$dis_order_id) {
                                require('inc.create-new-prospect.php');
                            }
    
                            // If the student has a prospect record, attempt to assign the course
                            if ($dis_order_id) {
                                // If the course is successfully assigned to the prospect, take the student to their portal
                                if (assign_course_to_prospect($dis_order_id, 0, false, false, $user_sub_id)) {
                                    $_SESSION['userfeedback'] = "Congratulations. You have enrolled into the programme using your subscription package.";
                                    $_SESSION['feedbacktype'] = "success";
            
                                    redirect(ROOT_DIR_PORTAL.'student/dashboard');
                                }
                            }
                        }
                    }
                    else {
                        $_SESSION['userfeedback'] = "This course is not covered by your active subscription.";
                        $_SESSION['feedbacktype'] = "error";
            
                        redirect(link_back(ROOT_DIR));
                    }
                }
                else {
                    $_SESSION['userfeedback'] = "You do not have any active subscription.";
                    $_SESSION['feedbacktype'] = "error";
        
                    redirect(link_back(ROOT_DIR));
                }
                
                $_SESSION['userfeedback'] = "There was an error while trying to perform the action. Please try again later.";
                $_SESSION['feedbacktype'] = "error";
    
                redirect(link_back(ROOT_DIR));
            }
            elseif (!$is_cart_checkout) {
                if ($direct_enrol_for_free_course){
                    $do_not_redirect_after_creating_prospect = true;

                    $has_taken_free_course = has_student_taken_a_free_course($_SESSION['email']);

                    if (!$has_taken_free_course) { // Has not taken a free course in the last 30 days.
                        // Check if student has taken this particular free course before.
                        $dis_order_id = student_is_a_prospect($_SESSION['email'], $dis_old_prog_id);

                        if (!$dis_order_id) { // Has not taken this free course before. Proceed to assign the course.
                            require('inc.create-new-prospect.php');

                            if ($dis_order_id && check_and_assign_free_course($dis_prog_id, $dis_order_id)) {
                                // Note the welcome prog_id
                                $_SESSION['track_p_id'] = $dis_order_id;

                                $_SESSION['userfeedback'] = "Congratulations. <br>Your enrolment was successful.";            
                                $_SESSION['feedbacktype'] = "success";

                                redirect(ROOT_DIR_PORTAL.'student/course-welcome');
                            }
                        } else { // Student has taken this free course before. Hence, redirect to the programme individual landing page.
                            $_SESSION['userfeedback'] = "You have already enrolled for this free course: <strong>".$dis_programme_name."</strong>";            
                            $_SESSION['feedbacktype'] = "error";

                            redirect(ROOT_DIR.$location_slug.'programme/'.$dis_programme_url);
                        }
                    }
                    else { // Has taken a free course within 30 days.
                        $_SESSION['userfeedback'] = "You cannot enrol in another free course until after <strong>".$has_taken_free_course."</strong> from now.";            
                        $_SESSION['feedbacktype'] = "error";

                        redirect(ROOT_DIR.$location_slug.'programme/'.$dis_programme_url);
                    }
                }
                else {
                    require('inc.create-new-prospect.php');
                }
            }
        }
        else {
            // If there is a checkout ID, don't create a new record. 
            // Because there is no checkout ID being passed when student clicks "Secure Checkout" from cart overview.
            // On the other hand, it will already exist if the page is reloaded or after the payment gateway sends a callback to this page.
            if (!$checkout_uniq_id) {
                $user_cart_info = get_user_cart_info();
                $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];
                
                if ($no_of_courses_in_cart) {
                    // Check if student is already enrolled to this programme
                    $course_verdict = array();
                    foreach ($user_cart_info['programme_info'] as $key => $value) {
                        $prog_interest = get_old_programme_info($value['prog_id'], 'option_id');
                        $student_is_already_enrolled_for_this_course = is_student_already_enrolled_for_this_course($prog_interest);

                        if ($student_is_already_enrolled_for_this_course) {
                            $course_verdict[] = $student_is_already_enrolled_for_this_course;

                            // Remove the programme from cart
                            mysqli_select_db($config, $database_config);
                            $deleteSQL = "DELETE FROM saved_to_cart
                                        WHERE login_id = ".sql_int($sess_user_id)."
                                            AND prog_id = ".sql_int($value['prog_id'])."
                                        LIMIT 1";
                            $deleteResult = mysqli_query($config, $deleteSQL) or die(mysqli_error($config));
                            $deleteStatus = mysqli_affected_rows($config);
                        }
                    } 
                    
                    if (count($course_verdict)) { // Redirect to cart page
                        $_SESSION['userfeedback'] = "You have already enrolled for the course(s) listed below <br/> - ";
                        $_SESSION['userfeedback'] .= implode("<br> - ", $course_verdict);
                        $_SESSION['feedbacktype'] = "success";

                        redirect(ROOT_DIR.'cart');
                    }          

                    // Generate the unique id
                    $checkout_uniq_id = sha1($sess_user_id.$time_now);

                    $api_Publisher_Id = 0;

                    $thisPublisherIDInfo = $_COOKIE["a_pub_id"];

                    if ($thisPublisherIDInfo) {
                        $api_Publisher_Id = $thisPublisherIDInfo - PUBLISHER_INCREMENT;
                    }
                    
                    // Record the checkout ID
                    $insertSQL = "INSERT INTO checkout (login_id, uniq_id, checkout_date, coupon_id, publisher_id) 
                                VALUES (
                                    ".sql_int($sess_user_id).", 
                                    '".sql_string($checkout_uniq_id)."', 
                                    '".sql_string($time_now)."',
                                    '".sql_int($coupon_id)."',
                                    '".sql_int($api_Publisher_Id)."'
                                )";
                    mysqli_select_db($config, $database_config);
                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                    $insertStatus = mysqli_affected_rows($config);

                    if ($insertStatus >= 0) { // If INSERT is successful
                        // Get the ID of this checkout
                        mysqli_select_db($config, $database_config);
                        $query_Checkout_Info = "SELECT *
                                                FROM checkout 
                                                WHERE login_id = ".sql_int($sess_user_id)." 
                                                        AND uniq_id = '".sql_string($checkout_uniq_id)."' 
                                                ORDER BY checkout_id DESC
                                                LIMIT 1";
                        $Checkout_Info = mysqli_query($config, $query_Checkout_Info) or die(mysqli_error($config));
                        $row_Checkout_Info = mysqli_fetch_assoc($Checkout_Info);
                        $totalRows_Checkout_Info = mysqli_num_rows($Checkout_Info);

                        if ($totalRows_Checkout_Info) {
                            $courses_SQL = $courses_chosen_for_cert_shipping = $courses_chosen_for_cert_attestation = $courses_chosen_for_othm_cert = array();

                            $cert_extra_fee_cookie_value = $_COOKIE['s2certextrafee'];

                            if ($cert_extra_fee_cookie_value) {
                                $courses_chosen_for_cert_shipping = explode(",", $cert_extra_fee_cookie_value);
                            }

                            $cert_attestation_fee_cookie_value = $_COOKIE['s2certattestion'];

                            if ($cert_attestation_fee_cookie_value) {
                                $courses_chosen_for_cert_attestation = explode(",", $cert_attestation_fee_cookie_value);
                            }

                            $othm_cert_fee_cookie_value = $_COOKIE['s2othmextrafee'];

                            if ($othm_cert_fee_cookie_value) {
                                $courses_chosen_for_othm_cert = explode(",", $othm_cert_fee_cookie_value);
                            }

                            foreach ($user_cart_info['programme_info'] as $key => $value) { // loop through the cart
                                $coupon_insert_sql = "'0'";
                                $checkout__amount_to_pay = $user_cart_info['programme_info'][$key]['fee'];
                                if ($coupon_id) {
                                    if (check_if_programme_matches_coupon($coupon_id, $user_cart_info['programme_info'][$key]['prog_id']) || $totalRows_couponValidForAllProgs) {
                                        $coupon_insert_sql = "'".sql_int($coupon_id)."'";
                                        $checkout__amount_to_pay = $user_cart_info['programme_info'][$key]['fee'] - (($this_coupon_discount / 100) * $user_cart_info['programme_info'][$key]['fee']);
                                    }
                                }

                                if (count($courses_chosen_for_cert_shipping) && in_array($user_cart_info['programme_info'][$key]['prog_id'], $courses_chosen_for_cert_shipping)) { // If student wants certificate shipped for this course
                                    $wants_cert_shipped = "'1'";
                                }
                                else {
                                    $wants_cert_shipped = "'0'";
                                }

                                if (count($courses_chosen_for_cert_attestation) && in_array($user_cart_info['programme_info'][$key]['prog_id'], $courses_chosen_for_cert_attestation)) { // If student wants certificate attestation for this course.
                                    $wants_cert_attestation = "'1'";
                                }
                                else {
                                    $wants_cert_attestation = "'0'";
                                }

                                if (count($courses_chosen_for_othm_cert) && in_array($user_cart_info['programme_info'][$key]['prog_id'], $courses_chosen_for_othm_cert)) { // If student wants certificate attestation for this course.
                                    $wants_othm_cert = "'1'";
                                }
                                else {
                                    $wants_othm_cert = "'0'";
                                }

                                $courses_SQL[] = "('".sql_int($row_Checkout_Info['checkout_id'])."', '".sql_int($user_cart_info['programme_info'][$key]['prog_id'])."', '".sql_string($user_cart_info['payment_currency'])."', '".sql_float($checkout__amount_to_pay)."', ".$coupon_insert_sql.", ".$wants_cert_shipped.", ".$wants_cert_attestation.", ".$wants_othm_cert.")";
                            }

                            if (count($courses_SQL)) {
                                // Record the courses in the cart during this checkout
                                $insertSQL = "INSERT INTO checkout_courses (checkout_id, prog_id, currency, amount_each, coupon_id, ship_cert, attest, othm_cert) 
                                            VALUES ".implode(", ", $courses_SQL)."
                                            ON DUPLICATE KEY UPDATE how_many = VALUES(how_many)";
                                mysqli_select_db($config, $database_config);
                                $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                                $insertStatus = mysqli_affected_rows($config);

                                if ($insertStatus >= 0) { // If INSERT is successful
                                    redirect(ROOT_DIR.'checkout/'.$checkout_uniq_id);
                                }
                                else { // If INSERT failed
                                    $_SESSION['userfeedback'] = "Something went wrong. Please try again later.";
                                    $_SESSION['feedbacktype'] = "error";
            
                                    redirect(ROOT_DIR.'cart');
                                }
                            }
                        }
                        else {
                            // Nothing in cart
                            redirect(ROOT_DIR.'cart');
                        }
                    }
                    else { // If INSERT failed
                        $_SESSION['userfeedback'] = "Something went wrong. Please try again later.";
                        $_SESSION['feedbacktype'] = "error";

                        redirect(ROOT_DIR.'cart');
                    }
                }
                else {
                    // Nothing in cart
                    redirect(ROOT_DIR.'cart');
                }
            }
            else {
                $total_fees_in_cart = $total_db_fees_in_cart = 0;
                $thiss2certextrafeeInfo = $_COOKIE['s2certextrafee'];
                $thiss2shipdetailsInfo = $_COOKIE['s2shipdetails'];
                $thiss2certAttestationInfo = $_COOKIE['s2certattestion'];
                $thiss2certOTHMInfo = $_COOKIE['s2othmextrafee'];

                // Save the shipping details in the checkout table.
                if ($enable_prog_certificate_extra_fees && $thiss2shipdetailsInfo && $thiss2certextrafeeInfo) { // If there's at least a course that an printing and shipping was selected for and the shipping details was filled.
                    // Check if shipping details has not been added to the checkout.
                    mysqli_select_db($config, $database_config);
                    $query_Check_Shipping = "SELECT checkout_id
                                            FROM checkout
                                            WHERE uniq_id = '".sql_string($checkout_uniq_id)."'
                                                AND shipping = ''
                                            LIMIT 1";
                    $Check_Shipping = mysqli_query($config, $query_Check_Shipping) or die(mysqli_error($config));
                    $row_Check_Shipping = mysqli_fetch_assoc($Check_Shipping);
                    $totalRows_Check_Shipping = mysqli_num_rows($Check_Shipping);

                    if ($totalRows_Check_Shipping) {
                        mysqli_select_db($config, $database_config);
                        $updateSQL = "UPDATE checkout
                                    SET shipping = '".sql_string($thiss2shipdetailsInfo)."'
                                    WHERE uniq_id = '".sql_string($checkout_uniq_id)."'
                                    LIMIT 1";
                        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
                        $updateStatus = mysqli_affected_rows($config);

                        if ($updateStatus < 0) { // If UPDATE failed
                            $_SESSION['userfeedback'] = "Something went wrong. Please try again later.";
                            $_SESSION['feedbacktype'] = "error";

                            redirect(ROOT_DIR.'cart');
                        }
                    }
                }
                elseif ($enable_prog_certificate_extra_fees && $thiss2certextrafeeInfo && !$thiss2shipdetailsInfo) { // Don't continue if there's no shipping address details and there's extra fee
                    $_SESSION['userfeedback'] = "Please fill your shipping details.";
                    $_SESSION['feedbacktype'] = "error";

                    redirect(ROOT_DIR.'cart');
                }

                // Get all matching programmes 
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
                                        WHERE chk.uniq_id = '".sql_string($checkout_uniq_id)."' 
                                            AND chk.login_id = '".sql_int($sess_user_id)."'
                                    )
                                    AND np.deleted = '0'"; 
                $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                $row_DisProg = mysqli_fetch_assoc($DisProg);
                $totalRows_DisProg = mysqli_num_rows($DisProg);

                if ($totalRows_DisProg) {
                    // Check if this student has any subscription at all.
                    mysqli_select_db($config, $database_config);
                    $query_ActiveSubscription = "SELECT *
                                                FROM users_subscriptions 
                                                WHERE login_id = ".sql_int($sess_user_id)." 
                                                    AND sub_status = 'active' 
                                                    AND end_date > '".sql_string($date_now)."'
                                                LIMIT 1";
                    $ActiveSubscription = mysqli_query($config, $query_ActiveSubscription) or die(mysqli_error($config));
                    $row_ActiveSubscription = mysqli_fetch_assoc($ActiveSubscription);
                    $totalRows_ActiveSubscription = mysqli_num_rows($ActiveSubscription);

                    if ($totalRows_ActiveSubscription) { // There's an active subscription.
                        $total_programmes_enrolled_through_subscription = array();

                        do {
                            // Check if the current programme is included in any active student subscriptions
                            $programme_in_subscription = find_active_student_programme_subscription($row_DisProg['programme_id']);

                            // The array will be empty if the programme is not among the selected programmes of the subscription
                            if (count($programme_in_subscription)) {
                                // Retrieve the subscription ID for the user and the URL for the programme
                                $user_sub_id = $programme_in_subscription[$row_DisProg['programme_id']];
                                $dis_programme_url = $row_DisProg['url_name'] ? $row_DisProg['url_name'] : '';

                                $do_not_redirect_after_creating_prospect = true;

                                // Fetch information about any older version of the programme
                                $dis_old_prog_id = get_old_programme_info($row_DisProg['programme_id'], 'option_id');

                                if ($dis_old_prog_id) {
                                    // Check if the student is already a prospect for this programme
                                    $dis_order_id = student_is_a_prospect($_SESSION['email'], $dis_old_prog_id);

                                    // If the student is not a prospect, include a file to handle new prospect creation and set a value for $dis_order_id
                                    if (!$dis_order_id) {
                                        require('inc.create-new-prospect.php');
                                    }

                                    // If the student has a prospect record, attempt to assign the course
                                    if ($dis_order_id) {
                                        // If the course is successfully assigned to the prospect, add it to the total programmes enrolled via subscription
                                        if (assign_course_to_prospect($dis_order_id, 0, false, false, $user_sub_id)) {
                                            $total_programmes_enrolled_through_subscription[] = $row_DisProg['programme_id'];
                                        }
                                    }
                                }
                            }
                        } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }

                        if ($total_subbed_programme = count($total_programmes_enrolled_through_subscription)) { // If there are programmes that have been added to the student's portal due to availability of subscription.
                            foreach ($total_programmes_enrolled_through_subscription as $sub_programme) {
                                clear_user_cart_details_from_db($sub_programme);
                            }

                            if ($total_subbed_programme == $totalRows_DisProg) { // If all the programmes in the student's cart have been enrolled into using the active subscription, delete the entire cart items and take the student to the portal.
                                $_SESSION['userfeedback'] = "Congratulations. You have enrolled into the programme(s) using your subscription package.";
                                $_SESSION['feedbacktype'] = "success";

                                redirect(ROOT_DIR_PORTAL.'student/dashboard');
                            }
                            else { // There are some programmes in the cart that the subscription did not apply to.
                                $_SESSION['userfeedback'] = "You have been automatically enrolled into some of the programmes you selected because you have an active subscription which requires no payment for those programmes.";
                                $_SESSION['feedbacktype'] = "success";

                                redirect(ROOT_DIR.'cart');
                            }
                        }
                    }

                    if ($enable_data_layer_scripts && $site_version == "online") { // Initialized here for dataHash "checkout 2" code.
                        $data_layer_programme_items = array();
                        $asset_position = 0;
                        $execute_for_dataHash_checkout_event_only = true; // This is used to execute the dataHash code in the "inc.coupon-discount-calc.php" file
                        $populate_dataLayer_checkout_items = true; // Compile the items for the dataLayer event.
                    }

                    if ($enable_prog_certificate_extra_fees) { // Initialized variables for extra fees calculations.
                        $progs_in_certificate_extra_fees_array = $extra_fee_ids = array();
                        $cert__extra_fee = $cert__extra_fee_currency = $cert_payment_extra_fee = "";

                        if ($thiss2certextrafeeInfo) {
                            $progs_in_certificate_extra_fees_array = explode(",", $thiss2certextrafeeInfo);
                        }
                    }

                    if ($enable_cert_attestation) { // Initialized variables for cert attestation calculations.
                        $progs_in_certificate_attestation_array = $cert_attestion_fee_ids = array();
                        $cert__attestation_fee = $cert__attestation_fee_currency = $cert_payment_attestation_fee = "";

                        if ($thiss2certAttestationInfo) {
                            $progs_in_certificate_attestation_array = explode(",", $thiss2certAttestationInfo);
                        }
                    }

                    if ($enable_othm_certificate_extra_fee) { // Initialized variables for cert attestation calculations.
                        $progs_in_certificate_othm_fees_array = $othm_fee_ids = array();
                        $cert__othm_fee = $cert__othm_fee_currency = $cert_payment_othm_fee = "";

                        if ($thiss2certOTHMInfo) {
                            $progs_in_certificate_othm_fees_array = explode(",", $thiss2certOTHMInfo);
                        }
                    }

                    do {
                        require('inc.coupon-discount-calc.php');
                    } while($row_DisProg = mysqli_fetch_assoc($DisProg));                    
                    // Then reset the pointer to the beginning. I added this here because this $row_DisProg['course_code'] that is used as a condition whether to start ecommerce tracking or not is returning NULL.
                    $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }

                    // The variable below "populate_dataLayer_checkout_items" is used to prevent the dataLayer script in the include file above "inc.coupon-discount-calc.php" 
                    // from executing again when the include file gets called below at the "Payment Summary" segment that displays the programme details at the right hand side of the page.
                    $populate_dataLayer_checkout_items = false;
                }
                else {
                    redirect(ROOT_DIR.'checkout');
                }

                // Get the details of the student for this checkout
                $student_email_address = $_SESSION['email'];
                
                // Manipulate the remaining code execution by setting expected values manually
                $totalRows_LetsGo = 1;
                $cart_checkout_id = get_checkout_id($checkout_uniq_id, $sess_user_id);
                $row_LetsGo['p_id'] = $cart_checkout_id;
                $row_LetsGo['full_name'] = get_student_name($student_email_address, true);
                $row_LetsGo['phone'] = get_student_exact_db_value('phone', $student_email_address);
                $row_LetsGo['email'] = $student_email_address;
                $row_LetsGo['nationality'] = get_student_exact_db_value('nationality', $student_email_address);
                $row_DisProg['discounted_fee'] = $total_db_fees_in_cart; // This is the BASE_CURRENCY value total
                $dis_programme_fee = $total_fees_in_cart;
                $dis_programme_id = $cart_checkout_id;

                if ($totalRows_DisProg > 1) {
                    $cellulant_item_name = $dis_programme_name = $totalRows_DisProg.' Courses #'.$cart_checkout_id;
                }
                
                if ($enable_prog_certificate_extra_fees && count($progs_in_certificate_extra_fees_array) && $thiss2certextrafeeInfo) { // Record the extra fees info into the database and get the fee IDs which will be attached to the payment gateways urls.
                    foreach ($progs_in_certificate_extra_fees_array as $array_value) {
                        $dis_fee_id = record_extra_fees_payment($array_value, "certificate printing and shipping", $cert__extra_fee_currency, $cert__extra_fee, "pending", $cart_checkout_id);
                        $extra_fee_ids[] = "&extra_fee[]=".$dis_fee_id;
                    }

                    if (count($extra_fee_ids)) { // Attach the extra fee IDs to the payment gateways redirect url.
                        $cert_payment_extra_fee = implode("", $extra_fee_ids);
                    }
                }

                if ($enable_cert_attestation && count($progs_in_certificate_attestation_array) && $thiss2certAttestationInfo) { // Record the attestation fees info into the database and get the fee IDs which will be attached to the payment gateways urls.
                    foreach ($progs_in_certificate_attestation_array as $array_value) {
                        $dis_attest_fee_id = record_extra_fees_payment($array_value, "certificate attestation", $cert__attestation_fee_currency, $cert__attestation_fee, "pending", $cart_checkout_id);
                        $cert_attestion_fee_ids[] = "&attest_fee[]=".$dis_attest_fee_id;
                    }

                    if (count($cert_attestion_fee_ids)) { // Attach the extra fee IDs (attestation) to the payment gateways redirect url.
                        $cert_payment_attestation_fee = implode("", $cert_attestion_fee_ids);
                    }
                }

                if ($enable_othm_certificate_extra_fee && count($progs_in_certificate_othm_fees_array) && $thiss2certOTHMInfo) { // Record the OTHM fees info into the database and get the fee IDs which will be attached to the payment gateways urls.
                    foreach ($progs_in_certificate_othm_fees_array as $array_value) {
                        $dis_othm_fee_id = record_extra_fees_payment($array_value, "othm certificate", $cert__othm_fee_currency, $cert__othm_fee, "pending", $cart_checkout_id);
                        $othm_fee_ids[] = "&othm_fee[]=".$dis_othm_fee_id;
                    }

                    if (count($othm_fee_ids)) { // Attach the extra fee IDs (othm cert) to the payment gateways redirect url.
                        $cert_payment_othm_fee = implode("", $othm_fee_ids);
                    }
                }
            }
        }

        // If there is a valid entry, continue with the payment process
        if ($totalRows_LetsGo) {
            // Variables
            $payment_var_order_id = $row_LetsGo['p_id'];
            $payment_var_client_name = $row_LetsGo['full_name'];
            $payment_var_client_phone = $row_LetsGo['phone'];
            $payment_var_client_email = $row_LetsGo['email'];
            $payment_var_client_country = $row_LetsGo['nationality'];
            $payment_var_amount_to_pay = $row_DisProg['discounted_fee'];

            if ($demo_mode) {
                $allow_2checkout_payment = false;
            }

            // Default currency in database
            $default_db_currency = BASE_CURRENCY;
            $default_db_currency_check = $default_db_currency.'_'; // Added the underscore to force GBP in all countries
            
            // Choose the preferred currency
            if ($site_version == 'localhost' && $allow_paytabs_payment && $test_paytabs) { // Since the paytabs test account only accepts GBP
                $preferred_paytabs_currency = 'AED';
                $preferred_paypal_currency = PREFERRED_CURRENCY;
            }
            else{
                $preferred_paytabs_currency = $preferred_paypal_currency = 'USD';
            }

            if ($payment_var_order_id && $payment_var_amount_to_pay) {
                // If the person is not paying in GBP, then show the conversion rate to our preferred PayTabs currency.
                if ($dis_programme_currency != $default_db_currency_check) {
                    // If today's rates for EURO has not been stored
                    if (!$todays_euro_conversion_rate = get_todays_conversion_rate($preferred_paytabs_currency)) {
                        // Get today's rate
                        $api_euro_conversion_rate = get_api_conversion_rate($default_db_currency, $preferred_paytabs_currency); 

                        if ($api_euro_conversion_rate) { 
                            store_todays_conversion_rate($default_db_currency, $preferred_paytabs_currency, $api_euro_conversion_rate);

                            $todays_euro_conversion_rate = $api_euro_conversion_rate;
                        }
                        else { // If request failed, use last known rate
                            if (!$todays_euro_conversion_rate = get_last_conversion_rate($preferred_paytabs_currency, BASE_CURRENCY)) {
                                // Default back to BASE_CURRENCY
                                $dis_programme_currency = $default_db_currency;
                                $todays_euro_conversion_rate = DEFAULT_BASE_CURRENCY_CONVERSION_RATE;
                            }
                        }
                    }
                }
                
                // Determine PayTabs values
                $payment_ish_secret_key = $dis_programme_currency == $default_db_currency_check ? PAYTABS_SECRET_KEY : PAYTABS_SECRET_KEY_EURO;
                $payment_ish_merchant_id = $dis_programme_currency == $default_db_currency_check ? PAYTABS_MERCHANT_ID : PAYTABS_MERCHANT_ID_EURO;

                $payment_ish_currency = $dis_programme_currency == $default_db_currency_check ? $default_db_currency : $preferred_paytabs_currency;
                $payment_ish_amount = $dis_programme_currency == $default_db_currency_check ? round($payment_var_amount_to_pay, 2) : round($payment_var_amount_to_pay * $todays_euro_conversion_rate, 2);

                // If the person is not paying in GBP, then show Paypal option. Otherwise, since payment is GBP, don't show Paypal option.
                if ($dis_programme_currency != $default_db_currency_check) {
                    // If today's rates for GBP has not been stored
                    if (!$todays_usd_conversion_rate = get_todays_conversion_rate($preferred_paypal_currency)) {
                        // Get today's rate
                        $api_usd_conversion_rate = get_api_conversion_rate($default_db_currency, $preferred_paypal_currency); 

                        if ($api_usd_conversion_rate) { 
                            store_todays_conversion_rate($default_db_currency, $preferred_paypal_currency, $api_usd_conversion_rate);

                            $todays_usd_conversion_rate = $api_usd_conversion_rate;
                        }
                        else { // If request failed, use last known rate
                            if (!$todays_usd_conversion_rate = get_last_conversion_rate($preferred_paypal_currency, BASE_CURRENCY)) {
                                // Don't show paypal option since we can't get the GBP amount
                                $todays_usd_conversion_rate = DEFAULT_BASE_CURRENCY_CONVERSION_RATE;
                            }
                        }
                    }
                }

                // Calculate the Paypal amount to use
                $paypal_usd_amount = round($payment_var_amount_to_pay * $todays_usd_conversion_rate, 2); 

                // Override amount for testers
                if ($demo_mode) {
                    //$paypal_usd_amount = 1.00;
                }

                if (is_array($feature_testers_email) && in_array($_SESSION['email'], $feature_testers_email)) {
                    $paypal_usd_amount = 1.00;
                }

                if ($allow_flutterwave_payment || $demo_flutterwave_payment) {
                    $show_flutterwave_button_at_the_top = false;
                    $show_flutterwave_button_at_the_bottom = false;

                    // if currency is among acceptable flutterwave currencies
                    if (in_array($dis_programme_currency, $flutterwave_acceptable_currencies)) {   
                        $amount_flutterwave = $dis_programme_fee;
                        $currency_flutterwave = $dis_programme_currency;

                        if (!in_array($currency_flutterwave, array('GBP', 'EUR', 'USD', 'NGN'))) { // Stack flutterwave button at the top if currency is not any of those in the array
                            $show_flutterwave_button_at_the_top = true;
                        } else { // Show flutterwave button below other payment gateways: Paypal, Paytabs, 2checkout
                            $show_flutterwave_button_at_the_bottom = true;
                        }
                    } else {
                        $allow_flutterwave_payment = false;
                    }
                }

                if ($allow_stripe_payment) {
                    // if currency is among acceptable stripe currencies
                    if (in_array($dis_programme_currency, $stripe_acceptable_currencies)) {   
                        $stripe_amount = $total_fees_in_cart ?:$dis_programme_fee;
                        $stripe_currency = $dis_programme_currency;

                        if ($demo_stripe_payment) {
                            if ($stripe_currency == 'NGN' && $stripe_amount <= 20000) {
                                $stripe_amount = 1000;
                            }
                            elseif (in_array($stripe_currency, array('GBP', 'USD')) && $stripe_amount <= 100) {
                                $stripe_amount = 2;
                            }
                        }
                    }
                }

                if ($allow_cellulant_payment) {
                    // if currency is among acceptable cellulant currencies
                    if (in_array($dis_programme_currency, $cellulant_acceptable_currencies)) {   
                        $cellulant_amount = $dis_programme_fee;
                        $cellulant_currency = $dis_programme_currency;
                    }
                    else {
                        $allow_cellulant_payment = false;
                    }
                }
            } else { // Total amount is zero
                if ($totalRows_checkifCouponValid && $row_checkifCouponValid['coupon_discount'] == 100) { // If coupon that was applied is likely one that gives 100% discount.
                    // Prepare the parameter needed to assign the courses to the student.
                    $target_prospect_id = $payment_var_order_id;

                    // Start recording and applying updates so that student can have access to the course
                    require_once(ROOT_PATH_TO_PORTAL.'inc.student-just-paid.php');

                    if ($totalRows_thisUserLogin) { // This is from the inc.student-just-paid file
                        // Mark the checkout_id as paid
                        mysqli_select_db($config, $database_config);
                        $updateSQL = "UPDATE checkout
                                    SET paid = '1'
                                    WHERE uniq_id = '".sql_string($checkout_uniq_id)."'
                                        AND login_id = ".sql_int($sess_user_id)."
                                    LIMIT 1";
                        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
                        $updateStatus = mysqli_affected_rows($config);

                        // Delete the courses from the cart
                        $user_cart_info = get_user_cart_info();
                        $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];
                    
                        if ($no_of_courses_in_cart) {
                            foreach ($user_cart_info['programme_info'] as $key => $value) {                            
                                // Remove the programme from cart
                                mysqli_select_db($config, $database_config);
                                $deleteSQL = "DELETE FROM saved_to_cart
                                            WHERE login_id = ".sql_int($sess_user_id)."
                                                AND prog_id = ".sql_int($value['prog_id'])."
                                            LIMIT 1";
                                $deleteResult = mysqli_query($config, $deleteSQL) or die(mysqli_error($config));
                                $deleteStatus = mysqli_affected_rows($config);
                            }
                        }

                        // Note the welcome prog_id
                        $_SESSION['track_p_id'] = $target_prospect_id;

                        $_SESSION['userfeedback'] = "Congratulations. <br>Your enrolment was successful.";            
                        $_SESSION['feedbacktype'] = "success";

                        redirect(ROOT_DIR_PORTAL.'student/course-welcome');
                    }
                } 
                else { // Just so they don't get to see the "oops" message below when the script reaches down and doesn't find any amount to display.
                    $_SESSION['userfeedback'] = "Something went wrong. Please contact Support or try again later.";            
                    $_SESSION['feedbacktype'] = "error";

                    redirect(ROOT_DIR.'cart');
                }
            }
        }
    }

    if ($allow_paytabs_payment) { // If paytabs gateway is allowed 
        // Send the learner to the PayTabs page to complete the action
        if ($_GET['gateway'] == 'paytabs') { 
            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_checkout_page_url;

            // Force live server domain for localhost since only https url can be used here
            $paytabs_return_url_prefix = $site_version == 'localhost' ? 'https://lrea.co.uk/' : ROOT_DIR;

            $data = array(
                "profile_id" => PAYTABS_PROFILE_ID,
                "tran_type" => "sale",
                "tran_class" => "ecom" ,
                "cart_id" => $payment_var_order_id,
                "cart_description" => $dis_programme_name,
                "cart_currency" => $payment_ish_currency,
                "cart_amount" => $payment_ish_amount,
                "hide_shipping" => true,
                "return" => $paytabs_return_url_prefix.'paytabs-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee,
            );
             
            $payload = json_encode($data);

            echo '<pre>'; var_dump($data, $payload); echo '</pre>'; exit();
             
            // Prepare new cURL resource
            $ch = curl_init('https://secure.paytabs.com/payment/request');
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
             
            // Close cURL session handle
            curl_close($ch);

            $response = json_decode($result);

            //echo '<pre>'; var_dump(PAYTABS_PROFILE_ID, PAYTABS_SERVER_KEY, $payload, $response); echo '</pre>'; exit();

            if ($paytabs_redirect_url = $response->redirect_url) {
                redirect($paytabs_redirect_url);
            }
            else {
                $_SESSION['userfeedback'] = "Unable to complete the request. Please try again later or use another payment option.";
                $_SESSION['feedbacktype'] = "error";

                redirect($current_checkout_page_url);
            }
        }
    }

    if ($allow_flutterwave_payment || $demo_flutterwave_payment) { // If flutterwave gateway is allowed
        // Send the learner to the Flutterwave page to complete the action
        if ($_GET['gateway'] == 'flutterwave') {
            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_checkout_page_url;
            
            $flutterwave_return_url_prefix = ROOT_DIR;
            
            // We can extract this programme id attached to the transaction reference which can be used to redirect user back to the checkout page incase the payment fails/they cancel the payment because flutterwave would always
            // take them back to the redirect_url no matter the result of the transaction and the reference is always sent along with the url.
            $transaction_ref_flutterwave = time().'F'.$dis_programme_id;

            record_payment_transaction_reference($payment_var_order_id, $transaction_ref_flutterwave, $_GET['gateway']);

            if (($site_version == "localhost") && ($currency_flutterwave == "NGN")) {
                $amount_flutterwave = 3000;
            }

            $request_object = array(
                'tx_ref' => $transaction_ref_flutterwave,
                'amount' => $amount_flutterwave,
                'currency' => $currency_flutterwave,
                'redirect_url' => $flutterwave_return_url_prefix.'flutterwave-transaction-complete.php?oid='.$payment_var_order_id.'&cid='.$dis_programme_id.'&initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee,
                'payment_option' => 'card',
                'customer' => array(
                    'email' => $payment_var_client_email,
                    'phonenumber' => $payment_var_client_phone,
                    'name' => $payment_var_client_name
                ),
                'customizations' => array(
                    'title' => $dis_programme_name,
                    'logo' => MEDIA_ROOT_DIR.'images/logo-dark.png'
                ),
                'meta' => array(
                    'oID' => $payment_var_order_id,
                    'cID' => $dis_programme_id
                )
            );

            if ($debug_flutterwave_payment_process) {
                $debug_file_name = 'flutterwave-debug-'.$payment_var_order_id.'.txt';
                my_log_to_file(var_dump_ret($request_object), $debug_file_name);
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/payments",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($request_object),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer ".FLUTTERWAVE_SECRET_KEY
                ),
            ));
            
            $result = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);

            $response = json_decode($result);

            if ($flutterwave_redirect_url = $response->data->link) {
                redirect($flutterwave_redirect_url);
            }
            else {
                $_SESSION['userfeedback'] = "Unable to complete the request. Please try again later or use another payment option.";
                $_SESSION['feedbacktype'] = "error";

                redirect($current_checkout_page_url);
            }
        }
    }

    if ($allow_checkoutdotcom_payment || $demo_checkout_dot_com_payment) { // If Checkoutdotcom gateway is allowed.
        $checkoutdotcom_currency = 'USD';
        $checkoutdotcom_amount = convert_amount_from_currency(BASE_CURRENCY, $checkoutdotcom_currency, $payment_var_amount_to_pay);

        // Send the learner to the Checkoutdotcom page to complete the action
        if ($_GET['gateway'] == 'checkoutdotcom') {
            // Get county webcode (ISO2). This is what checkout.com gateway requires as the country value.
            mysqli_select_db($config, $database_config);
            $query_Country_Web_Code = "SELECT mmc.webCode
                                        FROM myip_ms_countries mmc
                                        JOIN loccountries loc ON loc.cntry = mmc.countryID
                                        WHERE mmc.api_currency_code = '".sql_string($payment_ish_currency)."'
                                        LIMIT 1";
            $Country_Web_Code = mysqli_query($config, $query_Country_Web_Code) or die(mysqli_error($config));
            $row_Country_Web_Code = mysqli_fetch_assoc($Country_Web_Code);
            $totalRows_Country_Web_Code = mysqli_num_rows($Country_Web_Code);

            if ($totalRows_Country_Web_Code) {
                $payment_var_client_country_webcode = $row_Country_Web_Code['webCode'];
            } else {
                $payment_var_client_country_webcode = 'GB';
            }

            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_checkout_page_url;
            
            // Force live server domain for localhost since only https url can be used here
            $checkoutdotcom_return_url_prefix = $site_version == 'localhost' ? 'https://lrea.co.uk/' : ROOT_DIR;
            
            $checkoutdotcom_transaction_ref = time().'CH'.$payment_var_order_id;
            record_payment_transaction_reference($payment_var_order_id, $checkoutdotcom_transaction_ref, $_GET['gateway']);

            // Checkout.com only allows amount as integer
            if (in_array($checkoutdotcom_currency, $checkout_dot_com_currencies_that_accept_full_amount_format)) { // The amount is rounded to the nearest whole number and type casted to (int).
                $checkoutdotcom_amount = (int)round($checkoutdotcom_amount);
            } elseif (in_array($checkoutdotcom_currency, $checkout_dot_com_1000_format_currencies)) {
                $checkoutdotcom_amount = (int)($checkoutdotcom_amount * 1000);
            } else {
                $checkoutdotcom_amount = (int)($checkoutdotcom_amount * 100);
            }
    
            if ($demo_checkout_dot_com_payment) {
                $checkoutdotcom_amount = 100;
            }

            $request_object = array(
                'amount' => $checkoutdotcom_amount,
                'currency' => $checkoutdotcom_currency,
                'reference' => $checkoutdotcom_transaction_ref,
                'billing' => array(
                    'address' => array(
                        'country' => $payment_var_client_country_webcode
                    )
                ),
                'customer' => array(                    
                    'name' => $payment_var_client_name,
                    'email' => $payment_var_client_email
                ),
                'success_url' => $checkoutdotcom_return_url_prefix.'checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&cid='.$dis_programme_id.'&dcon='.$payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee,
                'failure_url' => $checkoutdotcom_return_url_prefix.'checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&cid='.$dis_programme_id.'&dcon='.$payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee,
                'cancel_url' => $checkoutdotcom_return_url_prefix.'checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&cid='.$dis_programme_id.'&dcon='.$payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee
            );

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.checkout.com/hosted-payments",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($request_object),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: ".CHECKOUT_GATEWAY_SECRET_KEY
                ),
            ));
            
            $result = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);

            $response = json_decode($result);

            //echo '<pre>'; var_dump(json_encode($request_object, JSON_PRETTY_PRINT), $response); echo '</pre>'; exit();
            //echo '<pre>'; var_dump($request_object, $response); echo '</pre>'; exit();

            if ($checkoutdotcom_redirect_url = $response->_links->redirect->href) {
                redirect($checkoutdotcom_redirect_url);
            }
            else {
                $_SESSION['userfeedback'] = "Unable to complete the request. Please try again later or use another payment option.";
                $_SESSION['feedbacktype'] = "error";

                redirect($current_checkout_page_url);
            }
        }
    }

    if ($allow_stripe_payment) { // If stripe gateway is allowed.
        $stripe_currency = 'USD';
        $stripe_amount = convert_amount_from_currency(BASE_CURRENCY, $stripe_currency, $payment_var_amount_to_pay);

        if (is_array($feature_testers_email) && in_array($_SESSION['email'], $feature_testers_email)) {
            $stripe_amount = 1;
        }

        // Send the learner to the stripe page to complete the action
        if ($_GET['gateway'] == 'stripe') {
            require __DIR__ . '/vendor/autoload.php';
        
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        
            header('Content-Type: application/json');

            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_checkout_page_url;
            
            $stripe_transaction_ref = time().'CH'.$payment_var_order_id;
            record_payment_transaction_reference($payment_var_order_id, $stripe_transaction_ref, $_GET['gateway']);

            // Stripe only allows amount as integer
            $stripe_amount = (int)($stripe_amount * 100);
        
            $checkout_session = \Stripe\Checkout\Session::create([
                'client_reference_id' => $stripe_transaction_ref,
                'customer_email' => $payment_var_client_email,
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($stripe_currency),
                            'product_data' => [
                                'name' => $dis_programme_name,
                            ],
                            'unit_amount' => $stripe_amount,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => ROOT_DIR.'stripe-transaction-complete.php?strp_s={CHECKOUT_SESSION_ID}&initpage='.rawurlencode($the_current_page).'&cid='.$dis_programme_id.'&dcon='.$payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee,
                'cancel_url' => $current_checkout_page_url,
            ]);

            if ($stripe_redirect_url = $checkout_session->url) {
                header("HTTP/1.1 303 See Other");
                header("Location: " . $stripe_redirect_url);
                exit();
            }
            else {
                $_SESSION['userfeedback'] = "Unable to complete the request. Please try again later or use another payment option.";
                $_SESSION['feedbacktype'] = "error";

                redirect($current_checkout_page_url);
            }
        }
    }

    $hide_signin_option = true;
    $render_old_btns = false;

    $disable_criteo_tag_from_this_page = true;
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
    
    if (!$is_cart_checkout) {
        $page_title = 'Enrol for '.$row_DisProg['programme_name'];
    }
    else {
        $page_title = 'Checkout';
    }

	require_once('components/doctype.inc.php');
?>

    <?php if ($site_version == "online") { ?>
        <?php if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in ?>
            <?php if ($row_DisProg['course_code'] && !$disable_criteo_tag_from_this_page) { // If it has a course code ?>
                <?php // Start Criteo Tag ?>
                    <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
                    <script type="text/javascript">
                        window.criteo_q = window.criteo_q || [];
                        var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
                        window.criteo_q.push(
                            { event: "setAccount", account: <?php echo $criteo_partner_id; ?>}, // You should never update this line
                            { event: "setEmail", email: "" }, // Can be an empty string 
                            { event: "setSiteType", type: deviceType},
                            { event: "viewBasket", item: [
                                {id: "<?php echo $row_DisProg['course_code']; ?>", price: <?php echo round($row_DisProg['discounted_fee'], 2); ?>, quantity: 1 }
                            ]}
                        );
                    </script> 
                <?php // End Criteo Tag ?>
            <?php } ?>

            <?php if ($dis_order_id) { // Google tag ?>
                <?php $gtag_conversion_id = ''; ?>

                <?php if ($gtag_conversion_id) { // Start Google tag ?>
                    <script>
                        gtag('event', 'conversion', {'send_to': '<?php echo $gtag_conversion_id; ?>'});
                    </script>
                <?php } // End Google tag ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <style>

    </style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
  </head>

  <body>
    <?php include_once('components/beginning-of-body.inc.php'); ?> 

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>
      
    <!-- Main Content -->
        <div class="main-content">
            <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
                <div class="container position-relative" style="z-index: 1">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-lg-0 mb-3">
                            <div class="text-start">
                                <?php if ($send_to_profile_for_completion || $direct_enrol_for_free_course) { ?>
                                    <h3 class="text-white">Enrol Now</h3>
                                <?php } else { ?>
                                    <div class="mb-4">
                                        <a href="<?php echo ROOT_DIR; ?>cart" class="btn btn-custom-size-2 btn-light btn-custom"><i data-feather="chevron-left"></i> Back to cart</a>
                                    </div>
                                <?php } ?>

                                <h1 class="text-bold text-white ft-custom-hero mb-4">Checkout</h1>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cart-container py-5">
                <div class="container">
                    <div class="row">
                        <?php
                            if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in, proceed with payment
                                if ($checkout_uniq_id) {
                                    // Get all matching programmes 
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
                                                            WHERE chk.uniq_id = '".sql_string($checkout_uniq_id)."' 
                                                                AND chk.login_id = '".sql_int($sess_user_id)."'
                                                        )
                                                        AND np.deleted = '0'"; 
                                    $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                                    $row_DisProg = mysqli_fetch_assoc($DisProg);
                                    $totalRows_DisProg = mysqli_num_rows($DisProg);

                                     if ($totalRows_DisProg) { $total_fees_in_cart = 0; ?>
                                        <div class="col-lg-4 order-2">
                                            <?php // Show extra fees when not logged in.
                                                if ($_COOKIE['s2certextrafee'] && ($sess_user_id < 1)) {
                                                    $progs_in_certificate_extra_fees_array = $extra_fee_ids = array();
                                                    $cert__extra_fee = $cert__extra_fee_currency = $cert_payment_extra_fee = "";

                                                    $progs_in_certificate_extra_fees_array = explode(",", $_COOKIE['s2certextrafee']);
                                                }
                                                
                                                if ($_COOKIE['s2othmextrafee'] && ($sess_user_id < 1)) {
                                                    $progs_in_certificate_othm_fees_array = $othm_fee_ids = array();
                                                    $cert__othm_fee = $cert__othm_fee_currency = $cert_payment_othm_fee = "";

                                                    $progs_in_certificate_othm_fees_array = explode(",", $_COOKIE['s2othmextrafee']);
                                                }
                                            ?>

                                            <?php do { ?>
                                                <?php require('inc.coupon-discount-calc.php'); ?>
                                                <?php 
                                                    $new_programme_total = $discount_amount = $discounted_programme_total = 0;
                                                    if (in_array($dis_programme_id, $all_valid_discounted_prog) && $this_coupon_discount) {
                                                        $discount_amount = ($this_coupon_discount / 100) * $dis_programme_fee;
                                                        $discounted_programme_total = $dis_programme_fee - $discount_amount;
                                                    }

                                                    $addBorderTop = true;
                                                ?>
                                                <div class="card card-body bg-green text-white border-0 rounded-0 mb-2">
                                                    <div class="row">
                                                        <div class="col-lg-4 mb-lg-0 mb-3">
                                                            <div class="prog-img-5 rounded-0 shadow">
                                                                <img src="<?php echo $dis_programme_thumbnail; ?>" class="w-100 h-100 object-fit-cover object-position-center img-fluid" alt="...">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <p class="mb-0">
                                                                <a href="<?php echo $link_root_path.$dis_programme_a_href; ?>" class="text-decoration-none" target="_blank">
                                                                    <?php echo $dis_programme_name; ?>
                                                                </a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); } ?>
                                        </div>
                                    <?php } ?>
                        <?php   }
                            } elseif ($_COOKIE['s2cart']) {
                                $retrieved_Cart_Info = array();
                                
                                $retrieved_Cart_Info = explode(",", $_COOKIE['s2cart']);
                                
                                if (count($retrieved_Cart_Info)) {
                                    // Get all matching programmes 
                                    $query_DisProg = "SELECT np.*, oprel.option_two, op.option_value  
                                                        FROM option_relationships oprel 
                                                        JOIN options op ON oprel.option_two = op.option_id 
                                                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                                        WHERE oprel.relationship_name = 'programme type' 
                                                            AND np.programme_status = 'active' 
                                                            AND np.programme_id IN (".implode(",", $retrieved_Cart_Info).")
                                                            AND np.deleted = '0'"; 
                                        $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                                        $row_DisProg = mysqli_fetch_assoc($DisProg);
                                        $totalRows_DisProg = mysqli_num_rows($DisProg);
                                    }
                            }
                        ?>

                        <div class="col-lg-8 order-1">
                            <div class="<?php echo ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') ? 'card card-body bg-light border-0 rounded-0 p-lg-5' : 'row justify-content-center'; ?>">
                                <?php if (isset($transaction_failed_message)) { ?>
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        <h4>Transaction Error</h4>
                                        <?php if (!$transaction_failed_message) { ?>
                                            <p>Your payment was unsuccessful. Please allow for some time and try again.</p>
                                        <?php } else { ?>
                                            <p><?php echo $transaction_failed_message; ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <!-- here -->
                                <?php if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in, proceed with payment ?>
                                    <?php if ($payment_var_order_id && $payment_var_amount_to_pay) { // If the payment details are available ?>
                                        <?php if ($allow_paypal_payment) { // If paypal gateway is allowed ?>
                                            <script 
                                                src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=<?php echo $preferred_paypal_currency; ?><?php /* ?>&disable-funding=card<?php */ ?>"
                                                data-order-id="<?php echo $payment_var_order_id; ?>"
                                            >
                                            </script>
                                        <?php } ?>

                                        <?php if (isset($_REQUEST['proc_ongoing'])) { ?>
                                            <div class="alert alert-success userfeedback mb-3">
                                                <span><i class="fa fa-check-circle fa-2x"></i> Please wait. Do not close this window...<i class="fa fa-spinner fa-lg fa-fw fa-pulse"></i></span>
                                            </div>
                                        <?php } ?>

                                        <h3 class="text-bold">Select your payment option</h3>

                                        <h6>Payment Summary</h6>

                                        <?php if ($allow_flutterwave_payment || $demo_flutterwave_payment) { ?>
                                            <p>You can pay in <strong><?php echo $currency_flutterwave; ?></strong> using your credit or debit card by choosing the <?php echo $currency_flutterwave; ?> payment option below.</p>
                                        <?php } elseif ($allow_paystack_payment) { ?>
                                            <p>You can pay in <strong><?php echo $dis_programme_currency; ?></strong> using your credit or debit card by choosing the <?php echo $dis_programme_currency; ?> payment option below.</p>
                                        <?php } else { ?>
                                            <p>You will be charged in <strong><?php echo $payment_ish_currency; ?></strong>.</p>
                                        <?php } ?>

                                        <?php if ($dis_programme_currency != $default_db_currency_check && $dis_programme_currency != $preferred_paytabs_currency) { ?>
                                            <h2 class="text-bold mb-3">
                                                <?php echo $symbol_for_programme_currency.' '.show_amount($total_fees_in_cart); ?> = <strong><?php echo get_passed_currency_symbol($payment_ish_currency).' '.number_format($payment_ish_amount, 2); ?></strong>
                                            </h2>
                                        <?php } else { ?>
                                            <h2 class="text-bold mb-3">
                                                <strong><?php echo get_passed_currency_symbol($payment_ish_currency).' '.number_format($payment_ish_amount, 2); ?></strong> 

                                                <?php if ($checkoutdotcom_currency && ($payment_ish_currency != $checkoutdotcom_currency)) { ?>
                                                    = <strong><?php echo get_passed_currency_symbol($checkoutdotcom_currency).' '.number_format($checkoutdotcom_amount, 2); ?></strong>
                                                <?php } ?>
                                            </h2>
                                        <?php } ?>

                                        <img src="<?php echo ROOT_DIR; ?>images/payment-logos.svg" class="img-fluid mb-3"> 

                                        <hr />

                                        <p class="text-dark text-center">If one of the payment gateways doesn't work, please try another.</p>

                                        <?php if (!isset($_REQUEST['proc_ongoing'])) { ?>
                                            <div class="row justify-content-center">
                                                <?php if ($allow_paystack_payment) { // If paystack gateway is allowed ?>
                                                    <?php // Prepare specific parameters
                                                        $single_paystack_payment = true;
                                                        $ref_separator = $single_paystack_payment ? 'S' : 'M'; 
                                                        $transaction_ref_paystack = time().$ref_separator.$payment_var_order_id;

                                                        if (in_array($dis_programme_currency, array('NGN'))) {
                                                            $amount_paystack = $total_fees_in_cart ?: $dis_programme_fee;
                                                            $currency_paystack = $dis_programme_currency;
                                                        }
                                                    ?>       
                                                    <?php if ($amount_paystack) { ?>
                                                        <div class="col-lg-4 col-6 mb-3">
                                                            <form action="" method="post" id="paymentForm" class="h-100">
                                                                <a href="javascript:{}" onclick="document.getElementById('payWithPaystack').click()" class="init_checkout text-decoration-none">
                                                                    <div class="card card-body py-3 h-100 rounded-0 justify-content-center align-items-center cursor-pointer hover-card-effect">
                                                                        <div>
                                                                            <img src="<?php echo ROOT_DIR; ?>images/Paystack_Logo.png" class="img-fluid w-100px">
                                                                            <button type="submit" name="submit" id="payWithPaystack" value="Checkout" class="d-none btn btn-outline-dark btn-lg btn-block init_checkout click_wait" onclick="payWithPaystack()" data-gatewayname="Paystack">Pay with <strong>Paystack</strong> (<?php echo $currency_paystack.' '.number_format($amount_paystack, 2); ?>)</button>
                                                                        </div>
                                                                        <div class="text-center mt-3">
                                                                            <small class="mb-0 text-muted ft-sm">Pay <strong>NGN</strong> (Nigerians Only)</small>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </form>
                                                        </div>

                                                        <script src="https://js.paystack.co/v1/inline.js"></script> 

                                                        <script type="text/javascript">
                                                            const paymentForm = document.getElementById('paymentForm');
                                                            paymentForm.addEventListener("submit", payWithPaystack, false);

                                                            function payWithPaystack(e) {
                                                                e.preventDefault();

                                                                let handler = PaystackPop.setup({
                                                                    key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
                                                                    email: '<?php echo $payment_var_client_email; ?>',
                                                                    amount: <?php echo round($amount_paystack * 100); ?>,
                                                                    currency: '<?php echo $currency_paystack; ?>',
                                                                    ref: '<?php echo $transaction_ref_paystack; ?>',
                                                                    
                                                                    onClose: function(){
                                                                        <?php if ($site_version == 'localhost') { ?>
                                                                            alert('Transaction was not completed, window closed.');
                                                                        <?php } ?>
                                                                    },
                                                                    callback: function(response){
                                                                        let message = 'Payment complete! Reference: ' + response.reference;
                                                                        window.location = "<?php echo ROOT_DIR; ?>paystack-transaction-complete.php?oid="+response.reference+"&cid=<?php echo $dis_programme_id; ?>&dcon=<?php echo $payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee; ?>";
                                                                    }
                                                                });

                                                                handler.openIframe();
                                                            }
                                                        </script>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php if ($allow_stripe_payment) { // If stripe gateway is allowed ?>
                                                    <div class="col-lg-4 col-6 mb-3">
                                                        <a href="<?php echo $current_checkout_page_url; ?>?gateway=stripe" class="init_checkout text-decoration-none" data-gatewayname="Stripe">
                                                            <div class="card card-body py-3 h-100 rounded-0 justify-content-center align-items-center cursor-pointer hover-card-effect">
                                                                <div>
                                                                    <img src="<?php echo ROOT_DIR; ?>images/stripe.svg.png" class="img-fluid w-100px">
                                                                </div>
                                                                <div class="text-center">
                                                                    <small class="mb-0 text-muted ft-sm">Pay <strong>USD</strong> with (Credit or Debit Card)</small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($todays_usd_conversion_rate && PAYPAL_CLIENT_ID) { ?>
                                                    <?php if ($allow_paypal_payment) { // If paypal gateway is allowed ?>
                                                        <div class="col-lg-4 col-6 mb-3">
                                                            <a href="#" class="init_checkout text-decoration-none" data-gatewayname="Paypal">
                                                                <div class="card card-body py-3 h-100 rounded-0 justify-content-center align-items-center cursor-pointer hover-card-effect" id="paypal-button-container" style="padding:10px">
                                                                    <div>
                                                                        <img src="<?php echo ROOT_DIR; ?>images/PayPal.svg.png" class="img-fluid w-100px">
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <small class="mb-0 text-muted ft-sm">Pay <strong>USD</strong> with (Credit or Debit Card)</small>
                                                                    </div>                                                                    
                                                                </div>
                                                            </a>
                                                        </div>
                                                    <?php } ?>

                                                    <script>
                                                        paypal.Buttons({
                                                            createOrder: function(data, actions) {
                                                                <?php if ($dis_order_id && $site_version == "online") { // Google tag ?>
                                                                    gtag('event', 'conversion', {'send_to': 'AW-587934796/wWjrCK2k9N4BEMzYrJgC'});
                                                                <?php } ?>
                                                                
                                                                return actions.order.create({
                                                                    purchase_units: [{
                                                                        reference_id: '<?php echo $payment_var_order_id; ?>', 
                                                                        description: '<?php echo htmlentities(substr($dis_programme_name, 0, 125), ENT_QUOTES); ?>', 
                                                                        amount: {
                                                                            value: '<?php echo round($paypal_usd_amount, 2); ?>'
                                                                        }
                                                                    }]
                                                                });
                                                            },
                                                            onApprove: function(data, actions) {
                                                                // This function captures the funds from the transaction.
                                                                return actions.order.capture().then(function(details) {
                                                                    console.log('Transaction completed by ' + details.payer.name.given_name);
                                                                    //alert('Your payment has been processed. Please wait while we get everything setup for you.');
                                                                    window.location = "<?php echo ROOT_DIR; ?>paypal-transaction-complete.php?oid="+data.orderID+"&cid=<?php echo $dis_programme_id; ?>&dcon=<?php echo $payment_domain_country.$cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee; ?>";
                                                                });
                                                            }
                                                        }).render('#paypal-button-container');
                                                    </script>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <h4 class="text-center">
                                            Oops. Something went wrong.
                                        </h4>
                                    <?php } ?>
                            </div>
                        </div>
                                <?php } else { ?>
                            </div>
                        </div>
                                    <?php if (isset($_GET['signin'])) { // If the student wants to sign in ?>
                                        <div class="container my-5">
                                            <div class="row">
                                                <div class="col-lg-10 mx-auto">
                                                    <?php include("inc.signin-form.php"); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { // Ask student to register ?>
                                            <?php // Variables needed to provide links in the included file
                                                $termsConditionURL = ROOT_DIR.$location_slug."terms";
                                                $privacyPolicyURL = ROOT_DIR.$location_slug."terms/privacy-policy";
                                            ?>
                                            <div class="container my-5">
                                                <div class="row">
                                                    <div class="col-lg-10 mx-auto">
                                                        <?php include("inc.register-socials-form.php"); ?>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php } ?>
                                <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>

    <?php if ($allow_2checkout_payment) { // If 2checkout gateway is allowed ?>
        <!-- <script src="https://www.2checkout.com/static/checkout/javascript/direct.min.js"></script> -->

        <script>
            $(function() {
                $('body').on('click', '.click_wait', function(e){
                    //e.preventDefault(); e.stopPropagation();
                    var target_class = $(this), init_html = target_class.html();

                    window.setTimeout(function(){
                        target_class.attr('disabled', 'disabled').html('Please wait. Do not close this window...<i class="fa fa-spinner fa-lg fa-fw fa-pulse"></i>');
                    }, 500); // Disable the button

                    window.setTimeout(function(){
                        target_class.removeAttr('disabled').html(init_html);
                    }, 60000 * 15); // Reset the button after 15 mins
                });

                <?php if ($dis_order_id && $site_version == "online") { // Google tag ?>
                    $('body').on('click', '.init_checkout', function(e){
                        gtag('event', 'conversion', {'send_to': 'AW-587934796/wWjrCK2k9N4BEMzYrJgC'});
                    });
                <?php } ?>
            });
        </script>
    <?php } ?>

    <?php if ($enable_data_layer_scripts && is_array($data_layer_programme_items) && count($data_layer_programme_items) && $site_version == "online" && $gtag_pixel_ID) { // Datalayer Checkout step 2 event ?>
        <script>
            $("body").on("click", ".init_checkout", function(e) {
                var $this = $(this), gateway = $this.attr('data-gatewayname');

                var dataHashJsonValue = <?php echo json_encode(iso88591_to_utf8($data_layer_programme_items)); ?>;

                for (let index = 0; index < dataHashJsonValue.length; index++) {
                    dataHashJsonValue[index]["dimension3"] = gateway;                    
                }

                function onCheckout() {
                    if (typeof dataLayer !== 'undefined') { // Javascript Level: If dataLayer is defined
                        dataLayer.push({
                            'event': 'checkout',
                            'ecommerce': {
                                'currencyCode': '<?php echo $coresite_curr_currency; ?>',
                                'checkout': {
                                    'actionField': {'step': 2},
                                    'products': dataHashJsonValue
                                }
                            }
                        });
                    }
                }
                onCheckout();
            });
        </script>
    <?php } ?>

    <?php if ($allow_paystack_payment) { ?>
        <script>
            $(function(e){
                $("body").on("submit", "#paymentForm", function(e){
                    var txref_id = "<?php echo $payment_var_order_id; ?>";
                    var texRef = "<?php echo $transaction_ref_paystack; ?>";
                    var gateway = "paystack";
                    
                    $.ajax({
                        url: '<?php echo ROOT_DIR."add-to-cart-ajax.php"; ?>',
                        method: 'POST',
                        data: {
                            txref_id: txref_id, 
                            texRef: texRef, 
                            gateway: gateway, 
                            saveTxRef : true
                        }
                    });
                });
            });
        </script>
    <?php } ?>

    <?php /*if ($_GET['gateway'] == 'paytabs' && $payment_var_order_id && $payment_var_amount_to_pay) { ?>
        <script>
            Paytabs.openPaymentPage();
        </script>
    <?php }*/ ?>

    <?php if ($allow_cellulant_payment) { ?>
        <script src="<?php echo CELLULANT_JS_URL; ?>"></script>
        <script>
            $(function() {
                Tingg.renderPayButton({
                    className: 'cellulant_btn', 
                    checkoutType: 'redirect'
                });

                $('body').on('click', '.cellulant_btn', function(e) {
                    e.preventDefault();

                    var currency = "<?php echo $cellulant_currency; ?>";
                    var fee = "<?php echo $cellulant_amount; ?>";
                    var order = "<?php echo $payment_var_order_id; ?>";
                    var email = "<?php echo $payment_var_client_email; ?>";
                    var item = "<?php echo $cellulant_item_name ? $cellulant_item_name : addslashes(substr($dis_programme_name, 0, 128)); ?>";
                    var product = "<?php echo $dis_programme_id; ?>";
                    var dcon = "<?php echo $payment_domain_country; ?>";
                    var extra = "<?php echo $cert_payment_extra_fee.$cert_payment_attestation_fee.$cert_payment_othm_fee; ?>";
                    var name = "<?php echo $payment_var_client_name; ?>";

                    $.ajax({
                        url: '<?php echo ROOT_DIR."add-to-cart-ajax.php"; ?>',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            currency: currency, 
                            fee: fee, 
                            order: order,
                            email: email,
                            item: item,
                            product: product,
                            dcon: dcon,
                            extra: extra,
                            name: name,
                            sanitize_celuload : true
                        },
                        success: function(response) {
                            Tingg.renderCheckout({
                                merchantProperties: response,
                                checkoutType: 'redirect'
                            });
                        }
                    });
                });
            });
        </script>
    <?php } ?>

    <?php include_once('components/end-of-body.inc.php'); ?> 
  </body>
</html>