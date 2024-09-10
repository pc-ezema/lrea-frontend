<?php 
    if (
        ($enable_prog_certificate_extra_fees && $_GET["extra_fee"] && count($_GET["extra_fee"])) ||
        ($enable_cert_attestation && $_GET["attest_fee"] && count($_GET["attest_fee"])) ||
        ($enable_othm_certificate_extra_fee && $_GET["othm_fee"] && count($_GET["othm_fee"]))
    ) { // If student chose for certificate to be shipped or wants certificate attestation or both certificate shipping and attestation.
        $total_extra_fees = 0;

        if ($record_extra_fee_payment_info) { // Get last inserted transaction ID
            mysqli_select_db($config, $database_config);
            $query_LastTransaction = "SELECT *
                                    FROM transaction_history
                                    WHERE transaction_id = '".sql_string($transaction_id)."'
                                        AND order_id = '".sql_string($order_id)."'
                                    ORDER BY created_date DESC
                                    LIMIT 1";
            $LastTransaction = mysqli_query($config, $query_LastTransaction) or die(mysqli_error($config));
            $row_LastTransaction = mysqli_fetch_assoc($LastTransaction);
            $totalRows_LastTransaction = mysqli_num_rows($LastTransaction);
        }

        if ($enable_prog_certificate_extra_fees && count($_GET["extra_fee"])) { // Certificate Shipping.
            foreach ($_GET["extra_fee"] as $extra_fee_id) {     
                // Get the extra fees amount the student paid and subtract it from the total amount paid
                mysqli_select_db($config, $database_config);
                $query_extra_Fee_Information = "SELECT target_id, currency, amount
                                                FROM extra_fees
                                                WHERE fee_id = '".sql_string($extra_fee_id)."'
                                                    AND status = 'pending'
                                                ORDER BY fee_id DESC
                                                LIMIT 1";
                $extra_Fee_Information = mysqli_query($config, $query_extra_Fee_Information) or die(mysqli_error($config));
                $row_extra_Fee_Information = mysqli_fetch_assoc($extra_Fee_Information);
                $totalRows_extra_Fee_Information = mysqli_num_rows($extra_Fee_Information);

                if ($totalRows_extra_Fee_Information) { // Extract transaction amount before recording it.
                    if ($row_extra_Fee_Information['currency'] == $transaction_currency) {
                        $extra_fee__amount = $row_extra_Fee_Information['amount'];
                    }
                    else { // Convert amount to the payment currency
                        $extra_fee__amount = convert_amount_from_currency($row_extra_Fee_Information['currency'], $transaction_currency, $row_extra_Fee_Information['amount']);
                    }

                    if ($record_extra_fee_payment_info && $totalRows_LastTransaction) { // Create the extra fee transaction history.
                        $insertSQL = "INSERT INTO transaction_history (
                            payment_processor, transaction_id, order_id, response_code, response_message, 
                            customer_name, customer_email, customer_phone, transaction_amount, transaction_currency, 
                            last_4_digits, first_4_digits, card_brand, trans_date, address_shipping, city_shipping, state_shipping, 
                            country_shipping, postal_code_shipping, secure_sign, `status`, created_date, 
                            confirmation_email_sent, delivery_email_sent, installment_confirmation_email_sent, domain_country
                        ) 
                        VALUES (
                            '".sql_string($row_LastTransaction['payment_processor'])."', 
                            '".sql_string($row_LastTransaction['transaction_id'])."', 
                            'ef".sql_string($extra_fee_id)."',
                            '".sql_string($row_LastTransaction['response_code'])."', 
                            '".sql_string($row_LastTransaction['response_message'])."', 
                            '".sql_string($row_LastTransaction['customer_name'])."', 
                            '".sql_string($row_LastTransaction['customer_email'])."', 
                            '".sql_string($row_LastTransaction['customer_phone'])."', 
                            '".sql_float($extra_fee__amount)."', 
                            '".sql_string($transaction_currency)."', 
                            '".sql_string($row_LastTransaction['last_4_digits'])."', 
                            '".sql_string($row_LastTransaction['first_4_digits'])."', 
                            '".sql_string($row_LastTransaction['card_brand'])."', 
                            '".sql_string($row_LastTransaction['trans_date'])."', 
                            '".sql_string($row_LastTransaction['address_shipping'])."', 
                            '".sql_string($row_LastTransaction['city_shipping'])."', 
                            '".sql_string($row_LastTransaction['state_shipping'])."', 
                            '".sql_string($row_LastTransaction['country_shipping'])."', 
                            '".sql_string($row_LastTransaction['postal_code_shipping'])."', 
                            '".sql_string($row_LastTransaction['secure_sign'])."', 
                            '".sql_string($row_LastTransaction['status'])."', 
                            '".sql_string($row_LastTransaction['created_date'])."', 
                            '".sql_string($time_now)."',
                            '".sql_string($time_now)."',
                            '".sql_string($time_now)."',
                            '".sql_string($row_LastTransaction['domain_country'])."'
                        )";
                        mysqli_select_db($config, $database_config);
                        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                        $insertStatus = mysqli_affected_rows($config);

                        // Update the extra fee to note that it has been paid for.
                        record_extra_fees_payment($row_extra_Fee_Information['target_id'], "certificate printing and shipping", $transaction_currency, $extra_fee__amount, 'paid', $order_id);

                        if ($debug_flutterwave_payment_process) {
                            my_log_to_file(var_dump_ret($insertSQL), $debug_file_name);
                        }
                    }

                    if ($separate_extra_fees_from_total_amount) { // This is for the first include of this script in the transaction-complete files of the payment gateways. Here, we sum up the extra fees that will be removed from the total amount paid.
                        $total_extra_fees += $extra_fee__amount;
                    }
                }
            }
        }

        if ($enable_cert_attestation && count($_GET["attest_fee"])) { // Certificate Attestation.
            foreach ($_GET["attest_fee"] as $attest_fee_id) {
                // Get the attestation fees amount the student paid and subtract it from the total amount paid
                mysqli_select_db($config, $database_config);
                $query_attest_Fee_Information = "SELECT target_id, currency, amount
                                                FROM extra_fees
                                                WHERE fee_id = '".sql_string($attest_fee_id)."'
                                                    AND status = 'pending'
                                                ORDER BY fee_id DESC
                                                LIMIT 1";
                $attest_Fee_Information = mysqli_query($config, $query_attest_Fee_Information) or die(mysqli_error($config));
                $row_attest_Fee_Information = mysqli_fetch_assoc($attest_Fee_Information);
                $totalRows_attest_Fee_Information = mysqli_num_rows($attest_Fee_Information);

                if ($totalRows_attest_Fee_Information) { // Extract transaction amount before recording it.
                    if ($row_attest_Fee_Information['currency'] == $transaction_currency) {
                        $attest_fee__amount = $row_attest_Fee_Information['amount'];
                    }
                    else { // Convert amount to the payment currency
                        $attest_fee__amount = convert_amount_from_currency($row_attest_Fee_Information['currency'], $transaction_currency, $row_attest_Fee_Information['amount']);
                    }

                    if ($record_extra_fee_payment_info && $totalRows_LastTransaction) { // Create the extra fee transaction history.
                        $insertSQL = "INSERT INTO transaction_history (
                            payment_processor, transaction_id, order_id, response_code, response_message, 
                            customer_name, customer_email, customer_phone, transaction_amount, transaction_currency, 
                            last_4_digits, first_4_digits, card_brand, trans_date, address_shipping, city_shipping, state_shipping, 
                            country_shipping, postal_code_shipping, secure_sign, `status`, created_date, 
                            confirmation_email_sent, delivery_email_sent, installment_confirmation_email_sent, domain_country
                        ) 
                        VALUES (
                            '".sql_string($row_LastTransaction['payment_processor'])."', 
                            '".sql_string($row_LastTransaction['transaction_id'])."', 
                            'ef".sql_string($attest_fee_id)."',
                            '".sql_string($row_LastTransaction['response_code'])."', 
                            '".sql_string($row_LastTransaction['response_message'])."', 
                            '".sql_string($row_LastTransaction['customer_name'])."', 
                            '".sql_string($row_LastTransaction['customer_email'])."', 
                            '".sql_string($row_LastTransaction['customer_phone'])."', 
                            '".sql_float($attest_fee__amount)."', 
                            '".sql_string($transaction_currency)."', 
                            '".sql_string($row_LastTransaction['last_4_digits'])."', 
                            '".sql_string($row_LastTransaction['first_4_digits'])."', 
                            '".sql_string($row_LastTransaction['card_brand'])."', 
                            '".sql_string($row_LastTransaction['trans_date'])."', 
                            '".sql_string($row_LastTransaction['address_shipping'])."', 
                            '".sql_string($row_LastTransaction['city_shipping'])."', 
                            '".sql_string($row_LastTransaction['state_shipping'])."', 
                            '".sql_string($row_LastTransaction['country_shipping'])."', 
                            '".sql_string($row_LastTransaction['postal_code_shipping'])."', 
                            '".sql_string($row_LastTransaction['secure_sign'])."', 
                            '".sql_string($row_LastTransaction['status'])."', 
                            '".sql_string($row_LastTransaction['created_date'])."', 
                            '".sql_string($time_now)."',
                            '".sql_string($time_now)."',
                            '".sql_string($time_now)."',
                            '".sql_string($row_LastTransaction['domain_country'])."'
                        )";
                        mysqli_select_db($config, $database_config);
                        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                        $insertStatus = mysqli_affected_rows($config);

                        // Update the extra fee to note that it has been paid for.
                        record_extra_fees_payment($row_attest_Fee_Information['target_id'], "certificate attestation", $transaction_currency, $attest_fee__amount, 'paid', $order_id);

                        if ($debug_flutterwave_payment_process) {
                            my_log_to_file(var_dump_ret($insertSQL), $debug_file_name);
                        }
                    }

                    if ($separate_extra_fees_from_total_amount) { // This is for the first include of this script in the transaction-complete files of the payment gateways. Here, we sum up the attestation fees that will be removed from the total amount paid.
                        $total_extra_fees += $attest_fee__amount;
                    }
                }
            }
        }

        if ($enable_othm_certificate_extra_fee && count($_GET["othm_fee"])) { // Certificate Attestation.
            foreach ($_GET["othm_fee"] as $othm_fee_id) {
                // Get the attestation fees amount the student paid and subtract it from the total amount paid
                mysqli_select_db($config, $database_config);
                $query_othm_fee_Information = "SELECT target_id, currency, amount
                                                FROM extra_fees
                                                WHERE fee_id = '".sql_string($othm_fee_id)."'
                                                    AND status = 'pending'
                                                ORDER BY fee_id DESC
                                                LIMIT 1";
                $othm_fee_Information = mysqli_query($config, $query_othm_fee_Information) or die(mysqli_error($config));
                $row_othm_fee_Information = mysqli_fetch_assoc($othm_fee_Information);
                $totalRows_othm_fee_Information = mysqli_num_rows($othm_fee_Information);

                if ($totalRows_othm_fee_Information) { // Extract transaction amount before recording it.
                    if ($row_othm_fee_Information['currency'] == $transaction_currency) {
                        $othm_fee__amount = $row_othm_fee_Information['amount'];
                    }
                    else { // Convert amount to the payment currency
                        $othm_fee__amount = convert_amount_from_currency($row_othm_fee_Information['currency'], $transaction_currency, $row_othm_fee_Information['amount']);
                    }

                    if ($record_extra_fee_payment_info && $totalRows_LastTransaction) { // Create the extra fee transaction history.
                        $insertSQL = "INSERT INTO transaction_history (
                            payment_processor, transaction_id, order_id, response_code, response_message, 
                            customer_name, customer_email, customer_phone, transaction_amount, transaction_currency, 
                            last_4_digits, first_4_digits, card_brand, trans_date, address_shipping, city_shipping, state_shipping, 
                            country_shipping, postal_code_shipping, secure_sign, `status`, created_date, 
                            confirmation_email_sent, delivery_email_sent, installment_confirmation_email_sent, domain_country
                        ) 
                        VALUES (
                            '".sql_string($row_LastTransaction['payment_processor'])."', 
                            '".sql_string($row_LastTransaction['transaction_id'])."', 
                            'ef".sql_string($othm_fee_id)."',
                            '".sql_string($row_LastTransaction['response_code'])."', 
                            '".sql_string($row_LastTransaction['response_message'])."', 
                            '".sql_string($row_LastTransaction['customer_name'])."', 
                            '".sql_string($row_LastTransaction['customer_email'])."', 
                            '".sql_string($row_LastTransaction['customer_phone'])."', 
                            '".sql_float($othm_fee__amount)."', 
                            '".sql_string($transaction_currency)."', 
                            '".sql_string($row_LastTransaction['last_4_digits'])."', 
                            '".sql_string($row_LastTransaction['first_4_digits'])."', 
                            '".sql_string($row_LastTransaction['card_brand'])."', 
                            '".sql_string($row_LastTransaction['trans_date'])."', 
                            '".sql_string($row_LastTransaction['address_shipping'])."', 
                            '".sql_string($row_LastTransaction['city_shipping'])."', 
                            '".sql_string($row_LastTransaction['state_shipping'])."', 
                            '".sql_string($row_LastTransaction['country_shipping'])."', 
                            '".sql_string($row_LastTransaction['postal_code_shipping'])."', 
                            '".sql_string($row_LastTransaction['secure_sign'])."', 
                            '".sql_string($row_LastTransaction['status'])."', 
                            '".sql_string($row_LastTransaction['created_date'])."', 
                            '".sql_string($time_now)."',
                            '".sql_string($time_now)."',
                            '".sql_string($time_now)."',
                            '".sql_string($row_LastTransaction['domain_country'])."'
                        )";
                        mysqli_select_db($config, $database_config);
                        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                        $insertStatus = mysqli_affected_rows($config);

                        // Update the extra fee to note that it has been paid for.
                        record_extra_fees_payment($row_othm_fee_Information['target_id'], "othm certificate", $transaction_currency, $othm_fee__amount, 'paid', $order_id);

                        if ($debug_flutterwave_payment_process) {
                            my_log_to_file(var_dump_ret($insertSQL), $debug_file_name);
                        }
                    }

                    if ($separate_extra_fees_from_total_amount) { // This is for the first include of this script in the transaction-complete files of the payment gateways. Here, we sum up the attestation fees that will be removed from the total amount paid.
                        $total_extra_fees += $othm_fee__amount;
                    }
                }
            }
        }

        if ($record_extra_fee_payment_info) { // Delete the extra fee cookie and the shipping address after recording the payment details.
            if ($enable_prog_certificate_extra_fees && count($_GET["extra_fee"])) { // Clear the cookie of the certificate shipping fee.
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('s2certextrafee', "", time() - (3600 * 24), "/", $uniqueDomainRoot);
                        setcookie('s2shipdetails', "", time() - (3600 * 24), "/", $uniqueDomainRoot);
                    }
                }
            }
            

            if ($enable_cert_attestation && count($_GET["attest_fee"])) {
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('s2certattestion', "", time() - (3600 * 24), "/", $uniqueDomainRoot);
                    }
                }
            }


            if ($enable_othm_certificate_extra_fee && count($_GET["othm_fee"])) {
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('s2othmextrafee', "", time() - (3600 * 24), "/", $uniqueDomainRoot);
                    }
                }
            }
        }

        if ($separate_extra_fees_from_total_amount) { // Transaction amount for the courses alone without the extra fees.
            $transaction_amount = $transaction_amount - $total_extra_fees;
        }
    }