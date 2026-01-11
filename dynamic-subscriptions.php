<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');

    if (!$coresite_page_segment_2_slug) {
        include_once('404.php');
        exit();
    }

    $subscription_is_for_all_programmes = $display_checkout_information = false;
    $all_subscription_programmes = array();
    $show_courses = true;
    $already_subscribed_to_this_package = false;

    $allow_paytabs_payment = true;
    $test_paytabs = false;
    $allow_paypal_payment = true;
    $allow_stripe_payment = true;
    $allow_cellulant_payment = true;
    $payment_domain_country = $coresite_curr_country_slug ? $coresite_curr_country_slug : "united-kingdom";
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

    if (defined('FLUTTERWAVE_SECRET_KEY')) {
        $allow_flutterwave_payment = true; // This line should be changed to true if the feature has been tested and considered okay.
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

    if (defined('PAYSTACK_PUBLIC_KEY') && PAYSTACK_PUBLIC_KEY) {
        $allow_paystack_payment = true;
    }

    $feature_testers_email = array(
        'olutayo@msbm.org.uk', 
        'victor@msbm.org.uk', 'victorariyibi@yahoo.com', 
        'tolulope.ariyibioke@gmail.com', 'toluariyibi@gmail.com', 'olivebranche@gmail.com', 
    );

    if (is_array($feature_testers_email) && in_array($_SESSION['email'], $feature_testers_email)) {
        $demo_flutterwave_payment = false;       
        $demo_checkout_dot_com_payment = false; 

        if ($date_now <= '2024-03-05') {
            $demo_stripe_payment = true; 
        }
    }
	
    // Get the selected subscription
    mysqli_select_db($config, $database_config);
    $query_CurrentSubscription = "SELECT * 
                                FROM subscriptions 
                                WHERE status = 'active' 
                                    AND slug <> '' 
                                    AND deleted = '0' 
                                    AND slug = '".sql_string($coresite_page_segment_2_slug)."' 
                                LIMIT 1";
    $CurrentSubscription = mysqli_query($config, $query_CurrentSubscription) or die(mysqli_error($config));
    $row_CurrentSubscription = mysqli_fetch_assoc($CurrentSubscription);
    $totalRows_CurrentSubscription = mysqli_num_rows($CurrentSubscription);

    if (!$totalRows_CurrentSubscription) {
        $_SESSION['userfeedback'] = "There are no subscriptions at the moment.";
        $_SESSION['feedbacktype'] = "error";

        redirect(ROOT_DIR);
    }

    // if the currency was not set previously, then perform the check and set the variable to a value
    $dis_subscription_currency = $coresite_curr_currency;

    if ($coresite_curr_currency && !$symbol_for_programme_currency) {
        $symbol_for_programme_currency = get_passed_currency_symbol($coresite_curr_currency);
    }

    // Initialize the subscription amount
    $unformatted_subscription_amount = $row_CurrentSubscription['amount'];

    // Apply the discount if it exists.
    if ($row_CurrentSubscription['discount']) { 
        $unformatted_subscription_amount = $row_CurrentSubscription['amount'] - $row_CurrentSubscription['discount'];
    }

    $original_var_amount_to_pay = $unformatted_subscription_amount;

    // Convert currency if necessary
    if ($row_CurrentSubscription['currency'] != $coresite_curr_currency) {
        $unformatted_subscription_amount = convert_amount_from_currency($row_CurrentSubscription['currency'], $coresite_curr_currency, $unformatted_subscription_amount);
    }

    // Format the subscription amount
    $subscription_amount = $symbol_for_programme_currency.' '.number_format($unformatted_subscription_amount);

    // Initialize the monthly amount info
    $monthly_subscription_amount = $unformatted_subscription_amount;

    if ($row_CurrentSubscription['duration'] == '12 months') {
        $subscription_duration = 'year';
        $billing_duration_text = 'annually';
        $monthly_subscription_amount = ceil($monthly_subscription_amount/12);
    }
    elseif (in_array($row_CurrentSubscription['duration'], array('6 months', '3 months'))) {
        $subscription_duration = $row_CurrentSubscription['duration'];
        
        if ($row_CurrentSubscription['duration'] == '6 months') {
            $billing_duration_text = 'bi-annually';
            $monthly_subscription_amount = ceil($monthly_subscription_amount/6);
        }
        else {
            $billing_duration_text = 'quarterly';
            $monthly_subscription_amount = ceil($monthly_subscription_amount/3);
        }
    }
    else {
        $subscription_duration = 'month';
        $billing_duration_text = 'monthly';
    }

    $monthly_subscription_amount_plus_currency = $symbol_for_programme_currency.' '.number_format($monthly_subscription_amount);

    $current_subscription_page_url = ROOT_DIR.$location_slug.'subscription-courses/'.$row_CurrentSubscription['slug'];

    if (isset($_GET['auth_status'])) {
        $current_subscription_page_url = append_to_url($current_subscription_page_url, 'auth_status='.$_GET['auth_status']);
    }

    if (isset($_GET['trx_action'])) {
        $current_subscription_page_url = append_to_url($current_subscription_page_url, 'trx_action='.$_GET['trx_action']);
    }

    $this_signin_link = ROOT_DIR.$location_slug.'subscription-courses/'.$row_CurrentSubscription['slug'].'?auth_status=unknown&trx_action=authenticate&signin=true';
    $this_signup_link = ROOT_DIR.$location_slug.'subscription-courses/'.$row_CurrentSubscription['slug'].'?auth_status=unknown&trx_action=authenticate';
    
    // If signin/signup form is submitted
	if ($_POST['signin']) {
        $return_here = $current_subscription_page_url;
        
        require_once(ROOT_PATH_TO_PORTAL.'inc.signin-processing.php');
    }
    elseif ($_POST['signup']) {
        $return_here = $current_subscription_page_url;
        
        require_once(ROOT_PATH_TO_PORTAL.'inc.signup-processing.php');
    }
    else {
        if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in
            // Check if user has an active subscription for this package.
            mysqli_select_db($config, $database_config);
            $query_ActiveSubscription = "SELECT *
                                        FROM users_subscriptions 
                                        WHERE sub_id = ".sql_int($row_CurrentSubscription['sub_id'])." 
                                            AND login_id = ".sql_int($sess_user_id)." 
                                            AND sub_status = 'active' 
                                            AND end_date > '".sql_string($date_now)."'
                                        LIMIT 1";
            $ActiveSubscription = mysqli_query($config, $query_ActiveSubscription) or die(mysqli_error($config));
            $row_ActiveSubscription = mysqli_fetch_assoc($ActiveSubscription);
            $totalRows_ActiveSubscription = mysqli_num_rows($ActiveSubscription);

            if ($totalRows_ActiveSubscription) {
                $already_subscribed_to_this_package = true;
            }
            
            if (isset($_GET['trx_action']) && ($_GET['trx_action'] == 'subscribe')) { // User clicks on the subscribe button.
                $user_subscription_id = 0;

                // Check if there's a record for this subscription that hasn't been paid before
                mysqli_select_db($config, $database_config);
                $query_ExistingRecord = "SELECT user_sub_id
                                        FROM users_subscriptions
                                        WHERE sub_id = ".sql_int($row_CurrentSubscription['sub_id'])."
                                            AND login_id = ".sql_string($sess_user_id)." 
                                            AND sub_status = 'pending' 
                                        ORDER BY user_sub_id DESC
                                        LIMIT 1";
                $ExistingRecord = mysqli_query($config, $query_ExistingRecord) or die(mysqli_error($config));
                $row_ExistingRecord = mysqli_fetch_assoc($ExistingRecord);
                $totalRows_ExistingRecord = mysqli_num_rows($ExistingRecord);

                if ($totalRows_ExistingRecord) { // Use already existing record.
                    $user_subscription_id = $row_ExistingRecord['user_sub_id'];
                }
                else { 
                    // Create a new record.
                    mysqli_select_db($config, $database_config);
                    $insertSQL = "INSERT INTO users_subscriptions (sub_id, login_id, sub_status) 
                                VALUES (
                                    ".sql_int($row_CurrentSubscription['sub_id']).",
                                    ".sql_int($sess_user_id).",
                                    'pending'
                                )";
                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                    $insertStatus = mysqli_affected_rows($config);

                    $can_continue_the_process = true;
                    
                    if ($insertStatus > 0) { // If INSERT is successful
                        // Get the last record
                        mysqli_select_db($config, $database_config);
                        $query_ExistingRecord = "SELECT user_sub_id
                                                FROM users_subscriptions
                                                WHERE sub_id = ".sql_int($row_CurrentSubscription['sub_id'])."
                                                    AND login_id = ".sql_string($sess_user_id)." 
                                                    AND sub_status = 'pending' 
                                                ORDER BY user_sub_id DESC
                                                LIMIT 1";
                        $ExistingRecord = mysqli_query($config, $query_ExistingRecord) or die(mysqli_error($config));
                        $row_ExistingRecord = mysqli_fetch_assoc($ExistingRecord);
                        $totalRows_ExistingRecord = mysqli_num_rows($ExistingRecord);

                        if ($totalRows_ExistingRecord) {
                            $user_subscription_id = $row_ExistingRecord['user_sub_id'];
                        }
                        else {
                            $can_continue_the_process = false;
                        }
                    }
                    else {
                        $can_continue_the_process = false;
                    }
                    
                    if (!$can_continue_the_process) {
                        $_SESSION['userfeedback'] = "There was an error while trying to perform the action. Please try again later.";
                        $_SESSION['feedbacktype'] = "error";

                        redirect(ROOT_DIR."subscription-courses/".$row_CurrentSubscription['slug']);
                    }
                }

                if ($user_subscription_id) { // Prepare payment gateway details and parameters for payment
                    $display_checkout_information = true;
                    $show_courses = false;

                    // Choose the preferred currency
                    if ($site_version == 'localhost' && $allow_paytabs_payment && $test_paytabs) { // Since the paytabs test account only accepts AED
                        $preferred_paytabs_currency = 'AED';
                        $preferred_paypal_currency = 'GBP';
                    }
                    else{
                        $preferred_paytabs_currency = $preferred_paypal_currency = 'USD';
                    }

                    // Prepare student's details.
                    $student_email_address = $_SESSION['email'];
                    
                    $payment_var_client_name = get_student_name($student_email_address, true);
                    $payment_var_client_phone = get_student_exact_db_value('phone', $student_email_address);
                    $payment_var_client_email = $student_email_address;
                    $payment_var_client_country = get_student_exact_db_value('nationality', $student_email_address);
                    $payment_var_amount_to_pay = $original_var_amount_to_pay;
                    $payment_var_order_id = 'subscription_'.$user_subscription_id;

                    // Determine PayTabs values
                    $payment_ish_secret_key = PAYTABS_SECRET_KEY_EURO;
                    $payment_ish_merchant_id = PAYTABS_MERCHANT_ID_EURO;

                    $payment_ish_currency = $preferred_paytabs_currency;
                    $payment_ish_amount = round(convert_amount_from_currency($row_CurrentSubscription['currency'], $preferred_paytabs_currency, $payment_var_amount_to_pay), 2);

                    // Calculate the Paypal amount to use
                    $paypal_usd_amount = round(convert_amount_from_currency($row_CurrentSubscription['currency'], $preferred_paypal_currency, $payment_var_amount_to_pay), 2);

                    if ($allow_flutterwave_payment || $demo_flutterwave_payment) {
                        $show_flutterwave_button_at_the_top = false;
                        $show_flutterwave_button_at_the_bottom = false;
    
                        // if currency is among acceptable flutterwave currencies
                        if (in_array($dis_subscription_currency, $flutterwave_acceptable_currencies)) {   
                            $amount_flutterwave = $unformatted_subscription_amount;
                            $currency_flutterwave = $dis_subscription_currency;
    
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
                        if (in_array($dis_subscription_currency, $stripe_acceptable_currencies)) {   
                            $stripe_amount = $payment_var_amount_to_pay;
                            $stripe_currency = $dis_subscription_currency;
    
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
                        if (in_array($dis_subscription_currency, $cellulant_acceptable_currencies)) {   
                            $cellulant_amount = $payment_var_amount_to_pay;
                            $cellulant_currency = $dis_subscription_currency;
                        }
                        else {
                            $allow_cellulant_payment = false;
                        }
                    }
                }
            }
        }
    }

    if ($allow_paytabs_payment && $payment_var_order_id) { // If paytabs gateway is allowed 
        // Send the learner to the PayTabs page to complete the action
        if ($_GET['gateway'] == 'paytabs') { 
            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_subscription_page_url;

            // Force live server domain for localhost since only https url can be used here
            $paytabs_return_url_prefix = $site_version == 'localhost' ? 'https://msbm.org.uk/' : ROOT_DIR;

            $data = array(
                "profile_id" => PAYTABS_PROFILE_ID,
                "tran_type" => "sale",
                "tran_class" => "ecom",
                "cart_id" => $payment_var_order_id,
                "cart_description" => SITE_NAME.' Subscription - '.$row_CurrentSubscription['sub_name'],
                "cart_currency" => $payment_ish_currency,
                "cart_amount" => $payment_ish_amount,
                "hide_shipping" => true,
                "return" => $paytabs_return_url_prefix.'paytabs-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country,
            );
             
            $payload = json_encode($data);

            //echo '<pre>'; var_dump($data, $payload); echo '</pre>'; exit();
             
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

                redirect($current_subscription_page_url);
            }
        }
    }

    if (($allow_flutterwave_payment || $demo_flutterwave_payment) && $payment_var_order_id) { // If flutterwave gateway is allowed
        // Send the learner to the Flutterwave page to complete the action
        if ($_GET['gateway'] == 'flutterwave') {
            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_subscription_page_url;
            
            $flutterwave_return_url_prefix = ROOT_DIR;
            
            $transaction_ref_flutterwave = time().$payment_var_order_id;

            if (($site_version == "localhost") && ($currency_flutterwave == "NGN")) {
                $amount_flutterwave = 3000;
            }

            $request_object = array(
                'tx_ref' => $transaction_ref_flutterwave,
                'amount' => $amount_flutterwave,
                'currency' => $currency_flutterwave,
                'redirect_url' => $flutterwave_return_url_prefix.'flutterwave-transaction-complete.php?oid='.$payment_var_order_id.'&initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country,
                'payment_option' => 'card',
                'customer' => array(
                    'email' => $payment_var_client_email,
                    'phonenumber' => $payment_var_client_phone,
                    'name' => $payment_var_client_name
                ),
                'customizations' => array(
                    'title' => SITE_NAME.' Subscription - '.$row_CurrentSubscription['sub_name'],
                    'logo' => MEDIA_ROOT_DIR.'images/logo-dark.png'
                ),
                'meta' => array(
                    'oID' => $payment_var_order_id,
                    'cID' => $sess_user_id
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

                redirect($current_subscription_page_url);
            }
        }
    }

    if ($allow_checkoutdotcom_payment || $demo_checkout_dot_com_payment) { // If Checkoutdotcom gateway is allowed.
        $checkoutdotcom_currency = 'USD';
        $checkoutdotcom_amount = convert_amount_from_currency($row_CurrentSubscription['currency'], $checkoutdotcom_currency, $payment_var_amount_to_pay);

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
            $the_current_page = $current_subscription_page_url;
            
            // Force live server domain for localhost since only https url can be used here
            $checkoutdotcom_return_url_prefix = $site_version == 'localhost' ? 'https://msbm.org.uk/' : ROOT_DIR;
            
            $checkoutdotcom_transaction_ref = time().$payment_var_order_id;

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
                'success_url' => $checkoutdotcom_return_url_prefix.'checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country,
                'failure_url' => $checkoutdotcom_return_url_prefix.'checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country,
                'cancel_url' => $checkoutdotcom_return_url_prefix.'checkout-dot-com-transaction-complete.php?initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country
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

                redirect($current_subscription_page_url);
            }
        }
    }

    if ($allow_stripe_payment) { // If stripe gateway is allowed.
        $stripe_currency = 'USD';
        $stripe_amount = convert_amount_from_currency($row_CurrentSubscription['currency'], $stripe_currency, $payment_var_amount_to_pay);

        if ($demo_stripe_payment) {
            if ($stripe_currency == 'NGN' && $stripe_amount <= 20000) {
                $stripe_amount = 1000;
            }
            elseif (in_array($stripe_currency, array('GBP', 'USD')) && $stripe_amount <= 500) {
                $stripe_amount = 1;
            }
        }

        // Send the learner to the stripe page to complete the action
        if ($_GET['gateway'] == 'stripe') {
            require __DIR__ . '/vendor/autoload.php';
        
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        
            header('Content-Type: application/json');

            // To be used to determine which page to return the student to if the payment failed
            $the_current_page = $current_subscription_page_url;
            
            $stripe_transaction_ref = time().$payment_var_order_id;

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
                                'name' => SITE_NAME.' Subscription - '.$row_CurrentSubscription['sub_name'],
                            ],
                            'unit_amount' => $stripe_amount,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => ROOT_DIR.'stripe-transaction-complete.php?strp_s={CHECKOUT_SESSION_ID}&initpage='.rawurlencode($the_current_page).'&dcon='.$payment_domain_country,
                'cancel_url' => $current_subscription_page_url,
            ]);

            if ($stripe_redirect_url = $checkout_session->url) {
                header("HTTP/1.1 303 See Other");
                header("Location: " . $stripe_redirect_url);
                exit();
            }
            else {
                $_SESSION['userfeedback'] = "Unable to complete the request. Please try again later or use another payment option.";
                $_SESSION['feedbacktype'] = "error";

                redirect($current_subscription_page_url);
            }
        }
    }

    if (!isset($_GET['auth_status']) || ($sess_user_id > 0 && $_SESSION['user_type'] == 'student')) { // Get all the programmes of this subscription.
        if ($show_courses) {
            // Get the programmes of this subscription.
            mysqli_select_db($config, $database_config);
            $query_SubscriptionProgrammes = "SELECT * 
                                            FROM subscriptions_programmes 
                                            WHERE sub_id = ".sql_int($row_CurrentSubscription['sub_id'])."";
            $SubscriptionProgrammes = mysqli_query($config, $query_SubscriptionProgrammes) or die(mysqli_error($config));
            $row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes);
            $totalRows_SubscriptionProgrammes = mysqli_num_rows($SubscriptionProgrammes);

            if ($totalRows_SubscriptionProgrammes) {
                if (($totalRows_SubscriptionProgrammes == 1) && ($row_SubscriptionProgrammes['prog_id'] == 7233000)) {
                    $subscription_is_for_all_programmes = true;
                } 
                else {
                    do {
                        $all_subscription_programmes[] = $row_SubscriptionProgrammes['prog_id'];
                    } while($row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes));
                }
            }
        }
    }
    else { // User clicks on the subscribe button and is not authenticated.
        $show_courses = false;
    }

    // Prepare the condition for targeting specific subjects
    $max_visible_first = 6;
    $prog_query_limit = "LIMIT 4000";

    if (!$subscription_is_for_all_programmes && count($all_subscription_programmes)) { // The subscription is not for all programmes, filter the query to only show the selected programmes.
        $prog_query_where_additional = "AND np.programme_id IN (".implode(", ", $all_subscription_programmes).")";
    }
	
    $page_title = $row_CurrentSubscription['sub_name'].' - '.SITE_NAME.' Subscription';
	require_once('components/doctype.inc.php');
?>

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
            <div class="container position-relative mt-custom-2" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="text-start">
                            <h1 class="text-bold text-white ft-custom-hero">Subscription: <span class="text-warning"><?php echo $row_CurrentSubscription['sub_name']; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($show_courses) { ?>
            <div class="courses-tabs py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-9 mb-3">
                            <div class="tabs-programme mb-5">
                                <ul class="nav nav-pills bg-white mb-3" id="pills-tab" role="tablist" style="gap: 15px;">
                                    <?php
                                        $short_courses_results = $advanced_professional_courses_results = $masters_courses_results = $all_courses_results = [];
                                        $cache_session_id = 'subscription_' . $coresite_page_segment_2_slug;

                                        $target_key = md5($cache_session_id);
                                        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);

                                        $active_tab_exists = false;
                                    ?>
                                    
                                    <?php
                                        if ($cached_value !== NULL) {
                                            $short_courses_results = $cached_value['short-courses'];
                                        } else {
                                            // Get the matching short course programmes
                                            $prog_query_preserve_params_for_next_run = true;
                                            $prog_box_display_type = 'short course';
                                            include('inc.programme-query.php');

                                            if ($totalRows_DisProg) {
                                                do {
                                                    $short_courses_results[] = $row_DisProg;
                                                } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                                $all_courses_results['short-courses'] = $short_courses_results;
                                            }
                                        }
                                    ?>

                                    <?php if ($short_courses_results) { $active_tab_exists = true; ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-pcc-tab" data-bs-toggle="pill" data-bs-target="#pills-pcc" type="button" role="tab" aria-controls="pills-pcc" aria-selected="true">Professional Certificate Course</button>
                                        </li>
                                    <?php } ?>


                                    <?php
                                        if ($cached_value !== NULL) {
                                            $advanced_professional_courses_results = $cached_value['advanced-professional'];
                                        } else {
                                            // Get the matching APCC programmes
                                            $prog_query_preserve_params_for_next_run = true;
                                            $prog_box_display_type = 'advanced professional';
                                            include('inc.programme-query.php');
                                            
                                            if ($totalRows_DisProg) {
                                                do {
                                                    $advanced_professional_courses_results[] = $row_DisProg;
                                                } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                                $all_courses_results['advanced-professional'] = $advanced_professional_courses_results;
                                            }
                                        }
                                    ?>

                                    <?php if ($advanced_professional_courses_results) { ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?php if(!$active_tab_exists) { echo 'active'; $active_tab_exists = true; } ?>" id="pills-apcc-tab" data-bs-toggle="pill" data-bs-target="#pills-apcc" type="button" role="tab" aria-controls="pills-apcc" aria-selected="false">Advanced Professional Certificate Course</button>
                                        </li>
                                    <?php } ?>


                                    <?php /*
                                        if ($cached_value !== NULL) {
                                            $masters_courses_results = $cached_value['masters'];
                                        } else {
                                            // Get the matching Masters programmes
                                            $prog_query_preserve_params_for_next_run = true;
                                            $prog_box_display_type = 'masters';
                                            include('inc.programme-query.php');

                                            if ($totalRows_DisProg) {
                                                do {
                                                    $masters_courses_results[] = $row_DisProg;
                                                } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                                $all_courses_results['masters'] = $masters_courses_results;
                                            }
                                        }
                                    ?>

                                    <?php if ($masters_courses_results) { ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?php if(!$active_tab_exists) { echo 'active'; $active_tab_exists = true; } ?>" id="pills-masters-tab" data-bs-toggle="pill" data-bs-target="#pills-masters" type="button" role="tab" aria-controls="pills-masters" aria-selected="false">Degree Programmes</button>
                                        </li>
                                    <?php } */ ?>
                                </ul>

                                <?php
                                    if ($cached_value === NULL) {
                                        add_entry_cached_in_memory($cache_session_id, $target_key, $all_courses_results);
                                    }
                                ?>

                                <?php $active_tab_exists = false; ?>
                                <?php $cache_session_id = 'subscription_courses_tabs_section_v1'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
                                    <div class="tab-content mb-4" id="pills-tabContent">
                                        <?php if ($short_courses_results) { $active_tab_exists = true; ?>
                                            <div class="tab-pane fade show active" id="pills-pcc" role="tabpanel" aria-labelledby="pills-pcc-tab" tabindex="0">
                                                <div class="py-4">
                                                    <h3 class="text-bold">Professional Certificate Course (<?php echo number_format(count($short_courses_results)); ?>)</h3>
                                                </div>

                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = 0; $i < count($short_courses_results); $i++) { ?>
                                                            <?php if ($i == $max_visible_first) break; ?>
                                                            <?php $row_DisProg = $short_courses_results[$i]; ?>
                                                            <div class="col-xl-6 col-lg-6 col-sm-12 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="collapse" id="collapseMore">
                                                    <div class="courses-container-lister">
                                                        <div class="row">
                                                            <?php for ($i = $max_visible_first; $i < count($short_courses_results); $i++) { ?>
                                                                <?php $row_DisProg = $short_courses_results[$i]; ?>
                                                                <div class="col-xl-6 col-lg-6 col-sm-12 mb-4 position-relative">
                                                                    <?php include('inc.programme-box.php') ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if (count($short_courses_results) > $max_visible_first) { ?>
                                                    <div class="text-center mt-4">
                                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore" aria-expanded="false" aria-controls="collapseMore" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                            <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                            <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                        </button>
                                                    </div>
                                                <?php } ?>   
                                            </div>
                                        <?php } ?>


                                        <?php if ($advanced_professional_courses_results) { ?>
                                            <div class="tab-pane fade <?php if(!$active_tab_exists) { echo 'show active'; $active_tab_exists = true; } ?>" id="pills-apcc" role="tabpanel" aria-labelledby="pills-apcc-tab" tabindex="0">
                                                <div class="py-4">
                                                    <h3 class="text-bold">Advanced Professional Certificate Course (<?php echo number_format(count($advanced_professional_courses_results)); ?>)</h3>
                                                </div>

                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = 0; $i < count($advanced_professional_courses_results); $i++) { ?>
                                                            <?php if ($i == $max_visible_first) break; ?>
                                                            <?php $row_DisProg = $advanced_professional_courses_results[$i]; ?>
                                                            <div class="col-xl-6 col-lg-6 col-sm-12 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="collapse" id="collapseMore2">
                                                    <div class="courses-container-lister">
                                                        <div class="row">
                                                            <?php for ($i = $max_visible_first; $i < count($advanced_professional_courses_results); $i++) { ?>
                                                                <?php $row_DisProg = $advanced_professional_courses_results[$i]; ?>
                                                                <div class="col-xl-6 col-lg-6 col-sm-12 mb-4 position-relative">
                                                                    <?php include('inc.programme-box.php') ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if (count($advanced_professional_courses_results) > $max_visible_first) { ?>
                                                    <div class="text-center mt-4">
                                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore2" aria-expanded="false" aria-controls="collapseMore2" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                            <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                            <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>


                                        <?php /* if ($masters_courses_results) { ?>
                                            <div class="tab-pane fade <?php if(!$active_tab_exists) { echo 'show active'; $active_tab_exists = true; } ?>" id="pills-masters" role="tabpanel" aria-labelledby="pills-masters-tab" tabindex="0">
                                                <div class="py-4">
                                                    <h3 class="text-bold">Degree Programmes (<?php echo number_format(count($masters_courses_results)); ?>)</h3>
                                                </div>

                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = 0; $i < count($masters_courses_results); $i++) { ?>
                                                            <?php if ($i == $max_visible_first) break; ?>
                                                            <?php $row_DisProg = $masters_courses_results[$i]; ?>
                                                            <div class="col-xl-6 col-lg-6 col-sm-12 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="collapse" id="collapseMore3">
                                                    <div class="courses-container-lister">
                                                        <div class="row">
                                                            <?php for ($i = $max_visible_first; $i < count($masters_courses_results); $i++) { ?>
                                                                <?php $row_DisProg = $masters_courses_results[$i]; ?>
                                                                <div class="col-xl-6 col-lg-6 col-sm-12 mb-4 position-relative">
                                                                    <?php include('inc.programme-box.php') ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if (count($masters_courses_results) > $max_visible_first) { ?>
                                                    <div class="text-center mt-4">
                                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore3" aria-expanded="false" aria-controls="collapseMore3" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                            <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                            <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } */ ?>

                                    </div>
                                <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>

                            </div>
                        </div>

                        <div class="col-lg-3">
                            <?php require('inc.subscription-side-info.php'); ?>                            
                        </div>
                    </div>
                </div>
            </div>                                  
        <?php } elseif ($display_checkout_information) { // Display the payment gateways ?>
            <div class='col-lg-8 mx-auto card card-body bg-light border-0 rounded-0 p-lg-5 my-5'>
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
                        <p>You can pay in <strong><?php echo $dis_subscription_currency; ?></strong> using your credit or debit card by choosing the <?php echo $dis_subscription_currency; ?> payment option below.</p>
                    <?php } else { ?>
                        <p>You will be charged in <strong><?php echo $payment_ish_currency; ?></strong>.</p>
                    <?php } ?>

                    <?php if ($dis_subscription_currency != $preferred_paytabs_currency) { ?>
                        <h2 class="text-bold mb-3">
                            <?php echo $symbol_for_programme_currency.' '.show_amount($unformatted_subscription_amount); ?> = <strong><?php echo get_passed_currency_symbol($payment_ish_currency).' '.number_format($payment_ish_amount, 2); ?></strong>
                        </h2>
                    <?php } else { ?>
                        <h2 class="text-bold mb-3">
                            <strong><?php echo get_passed_currency_symbol($payment_ish_currency).' '.number_format($payment_ish_amount, 2); ?></strong> 

                            <?php if ($checkoutdotcom_currency && ($payment_ish_currency != $checkoutdotcom_currency)) { ?>
                                = <strong><?php echo get_passed_currency_symbol($checkoutdotcom_currency).' '.number_format($checkoutdotcom_amount, 2); ?></strong>
                            <?php } ?>
                        </h2>
                    <?php } ?>

                    <p>* Being payment for <?php echo $row_CurrentSubscription['duration']; ?></p>

                    <img src="<?php echo ROOT_DIR; ?>images/payment-logos.svg" class="img-fluid mb-3"> 

                    <hr />

                    <p class="text-dark text-center">If one of the payment gateways doesn't work, please try another.</p>

                    <?php if (!isset($_REQUEST['proc_ongoing'])) { ?>
                        <div class="row justify-content-center">
                            <?php if ($allow_paystack_payment) { // If paystack gateway is allowed ?>
                                <?php // Prepare specific parameters
                                    $single_paystack_payment = true;
                                    $transaction_ref_paystack = time().$payment_var_order_id;

                                    if (in_array($dis_subscription_currency, array('NGN'))) {
                                        $amount_paystack = $unformatted_subscription_amount;
                                        $currency_paystack = $dis_subscription_currency;
                                    }
                                ?>       
                                <?php if ($amount_paystack) { ?>
                                    <div class="col-xl-6 col-lg-6 col-sm-12 col-6 mb-3">
                                        <form action="" method="post" id="paymentForm" class="h-100">
                                            <a href="javascript:{}" onclick="document.getElementById('payWithPaystack').click()" class="text-decoration-none">
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
                                                    window.location = "<?php echo ROOT_DIR; ?>paystack-transaction-complete.php?oid="+response.reference+"&initpage=<?php echo rawurlencode($the_current_page); ?>&cid=<?php echo $row_CurrentSubscription['sub_id']; ?>&dcon=<?php echo $payment_domain_country; ?>";
                                                }
                                            });

                                            handler.openIframe();
                                        }
                                    </script>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($allow_stripe_payment) { // If stripe gateway is allowed ?>
                                <div class="col-xl-6 col-lg-6 col-sm-12 col-6 mb-3">
                                    <a href="<?php echo append_to_url($current_subscription_page_url, 'gateway=stripe'); ?>" class="init_checkout text-decoration-none" data-gatewayname="Stripe">
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

                            <?php if (PAYPAL_CLIENT_ID) { ?>
                                <?php if ($paypal_usd_amount && $allow_paypal_payment) { // If paypal gateway is allowed ?>
                                    <div class="col-xl-6 col-lg-6 col-sm-12 col-6 mb-3">
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
                                            return actions.order.create({
                                                purchase_units: [{
                                                    reference_id: '<?php echo $payment_var_order_id; ?>', 
                                                    description: '<?php echo 'Subscription - '.$row_CurrentSubscription['sub_name']; ?>', 
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
                                                window.location = "<?php echo ROOT_DIR; ?>paypal-transaction-complete.php?oid="+data.orderID+"&initpage=<?php echo rawurlencode($the_current_page); ?>&cid=<?php echo $row_CurrentSubscription['sub_id']; ?>&dcon=<?php echo $payment_domain_country; ?>";
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
        <?php } else { // Authentication (Signup and Signin) ?>
            <?php if (isset($_GET['signin'])) { // Sign in ?>
                <div class="container my-5">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <?php include("inc.signin-form.php"); ?>
                        </div>
                    </div>
                </div>
            <?php } else { // Ask user to register ?>
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
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 

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
                    var item = "<?php echo SITE_NAME.' Subscription - '.$row_CurrentSubscription['sub_name']; ?>";
                    var product = "<?php echo $row_CurrentSubscription['sub_id']; ?>";
                    var dcon = "<?php echo $payment_domain_country; ?>";
                    var extra = "";
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
</body>
</html>