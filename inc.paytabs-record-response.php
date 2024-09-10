<?php 

// Check that there is no initial record of this payment
mysqli_select_db($config, $database_config);
$query_tHistoryCheck = "SELECT th.history_id 
                        FROM transaction_history th 
                        WHERE th.transaction_id = '".sql_string($transaction_id)."' 
                                AND th.order_id = '".sql_string($order_id)."' 
                        LIMIT 1";
$tHistoryCheck = mysqli_query($config, $query_tHistoryCheck) or die(mysqli_error($config));
$row_tHistoryCheck = mysqli_fetch_assoc($tHistoryCheck);
$totalRows_tHistoryCheck = mysqli_num_rows($tHistoryCheck);

$customer_email = $customer_email ? $customer_email : get_email_from_checkout_id($order_id);
$customer_name = $customer_name ? $customer_name : get_student_exact_db_value('full_name', $customer_email);

if (!$totalRows_tHistoryCheck) {
    if ($it_is_a_subscription_payment) { // We only want to send order confirmation email for subscription payments
        $set_confirmation_email_sent = "0000-00-00 00:00:00";
        $set_delivery_email_sent = $set_installment_confirmation_email_sent = $time_now;
    }
    elseif ($prevent_multiple_delivery_email) { // if the student is not making a part payment for the first time, prevent multiple delivery email from being sent.
        // variable initialize to prevent differnce in time 
        $current_time = $time_now;
        
        $set_confirmation_email_sent = $current_time;
        $set_delivery_email_sent = $current_time;
        $set_installment_confirmation_email_sent = "0000-00-00 00:00:00";
    }
    else {
        $set_confirmation_email_sent = "0000-00-00 00:00:00";
        $set_delivery_email_sent = "0000-00-00 00:00:00";

        //if the studnet is not making a consecutive part payment, 
        //update installment set email time to prevent the Cron 
        //from sending the email multiple times to everybody
        $set_installment_confirmation_email_sent = $time_now;  

    }
    // Record the transaction in the database
    $insertSQL = "INSERT INTO transaction_history (payment_processor, transaction_id, order_id, int_order_id, response_code, response_message, customer_name, customer_email, customer_phone, transaction_amount, transaction_currency, last_4_digits, first_4_digits, card_brand, trans_date, address_shipping, city_shipping, state_shipping, country_shipping, postal_code_shipping, secure_sign, `status`, created_date, confirmation_email_sent, delivery_email_sent, installment_confirmation_email_sent, domain_country) 
                VALUES ( 
                    'PayTabs', 
                    '".sql_string($transaction_id)."', 
                    '".sql_string($order_id)."', 
                    '".sql_int($order_id)."', 
                    '".sql_string($response_code)."', 
                    '".sql_string($response_message)."', 
                    '".sql_string($customer_name)."', 
                    '".sql_string($customer_email)."', 
                    '".sql_string($customer_phone)."', 
                    '".sql_float($transaction_amount)."', 
                    '".sql_string($transaction_currency)."', 
                    '".sql_string($last_4_digits)."', 
                    '".sql_string($first_4_digits)."', 
                    '".sql_string($card_brand)."', 
                    '".sql_string($trans_date)."', 
                    '".sql_string($address_shipping)."', 
                    '".sql_string($city_shipping)."', 
                    '".sql_string($state_shipping)."', 
                    '".sql_string($country_shipping)."', 
                    '".sql_string($postal_code_shipping)."', 
                    '".sql_string($requestSignature)."', 
                    '".sql_string($transaction_status_info)."', 
                    '".date("Y-m-d H:i:s")."',
                    '".sql_string($set_confirmation_email_sent)."',
                    '".sql_string($set_delivery_email_sent)."',
                    '".sql_string($set_installment_confirmation_email_sent)."',
                    '".sql_string($payment_domain_country)."'
                )";
    mysqli_select_db($config, $database_config);
    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
    $insertStatus = mysqli_affected_rows($config);
}
else {
    // Take the student to the student portal dashboard
    redirect(ROOT_DIR_PORTAL.'student/dashboard');
}