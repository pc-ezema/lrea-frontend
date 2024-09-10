<?php 

// Check that there is no initial record of this payment
mysqli_select_db($config, $database_config);
$query_tHistoryCheck = "SELECT th.history_id 
                        FROM transaction_history th 
                        WHERE th.transaction_id = '".sql_string($_REQUEST['order_number'])."' 
                                AND th.order_id = '".sql_string($_REQUEST['merchant_order_id'])."' 
                        LIMIT 1";
$tHistoryCheck = mysqli_query($config, $query_tHistoryCheck) or die(mysqli_error($config));
$row_tHistoryCheck = mysqli_fetch_assoc($tHistoryCheck);
$totalRows_tHistoryCheck = mysqli_num_rows($tHistoryCheck);

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
    $insertSQL = "INSERT INTO transaction_history (payment_processor, transaction_id, order_id, int_order_id, response_code, response_message, customer_name, customer_email, customer_phone, transaction_amount, transaction_currency, trans_date, address_shipping, city_shipping, state_shipping, country_shipping, postal_code_shipping, secure_sign, `status`, created_date, confirmation_email_sent, delivery_email_sent, installment_confirmation_email_sent, domain_country) 
                VALUES ( 
                    '2Checkout', 
                    '".sql_string($_REQUEST['order_number'])."', 
                    '".sql_string($_REQUEST['merchant_order_id'])."', 
                    '".sql_int($_REQUEST['merchant_order_id'])."', 
                    '".sql_string($_REQUEST['PTCOID'])."', 
                    '".sql_string($_REQUEST['credit_card_processed'])."', 
                    '".sql_string($_REQUEST['card_holder_name'])."', 
                    '".sql_string($_REQUEST['email'])."', 
                    '".sql_string($_REQUEST['phone'])."', 
                    '".sql_float($_REQUEST['total'])."', 
                    '".sql_string($_REQUEST['currency_code'])."', 
                    '".date("Y-m-d H:i:s")."', 
                    '".sql_string($_REQUEST['street_address'])."', 
                    '".sql_string($_REQUEST['city'])."', 
                    '".sql_string($_REQUEST['state'])."', 
                    '".sql_string($_REQUEST['country'])."', 
                    '".sql_string($_REQUEST['zip'])."', 
                    '".sql_string($_REQUEST['key'])."', 
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