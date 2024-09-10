<?php 
if ($_GET['extra_fee']) {
    // Get the duration missed
    mysqli_select_db($config, $database_config);
    $query_studentMissedMonths = "SELECT p.missed_months
                                FROM prospects p
                                WHERE p.p_id = ".sql_int($oID)."
                                LIMIT 1";
    $studentMissedMonths = mysqli_query($config, $query_studentMissedMonths) or die(mysqli_error($config));
    $row_studentMissedMonths = mysqli_fetch_assoc($studentMissedMonths);
    $totalRows_studentMissedMonths = mysqli_num_rows($studentMissedMonths);

    if ($totalRows_studentMissedMonths) {
        if ($row_studentMissedMonths['missed_months'] == '12 months') {
            clear_out_student_course_progress($row_studentMissedMonths['p_id']);
        }
    }

    // Get the reactivation amount the student paid and subtract it from the total paid
    mysqli_select_db($config, $database_config);
    $query_extraFeeInformation = "SELECT currency, amount
                                FROM extra_fees
                                WHERE target_id = ".sql_int($order_id)."
                                    AND fee_id = '".sql_string($_GET['extra_fee'])."'
                                    AND status = 'pending'
                                ORDER BY fee_id DESC
                                LIMIT 1";
    $extraFeeInformation = mysqli_query($config, $query_extraFeeInformation) or die(mysqli_error($config));
    $row_extraFeeInformation = mysqli_fetch_assoc($extraFeeInformation);
    $totalRows_extraFeeInformation = mysqli_num_rows($extraFeeInformation);

    if ($totalRows_extraFeeInformation) { // Extract transaction amount just before it is recorded
        if ($row_extraFeeInformation['currency'] == $transaction_currency) {
            $extra_fee__amount = $row_extraFeeInformation['amount'];
        }
        else {
            // Convert amount to the payment currency
            $extra_fee__amount = convert_amount_from_currency($row_extraFeeInformation['currency'], $transaction_currency, $row_extraFeeInformation['amount']);
        }

        $transaction_amount = $transaction_amount - $extra_fee__amount;
    }
    elseif ($row_studentMissedMonths['missed_months'] && !$totalRows_extraFeeInformation) { // In case the records for the person is incorrect.
        $reactivationValues = $arrayOfReactivationFeeAndPeriod[$row_studentMissedMonths['missed_months']];

        if (count($reactivationValues) && $reactivationValues) {
            $reactivation_fee = $reactivationValues['fee'];
            $reactivation_currency = $reactivationValues['currency'];
            
            if ($reactivation_currency == $transaction_currency) {
                $extra_fee__amount = $reactivation_fee;
            }
            else {
                // Convert amount to the payment currency
                $extra_fee__amount = convert_amount_from_currency($reactivation_currency, $transaction_currency, $reactivation_fee);
            }

            $transaction_amount = $transaction_amount - $extra_fee__amount;
        }
    }
}

if ($_GET['attest_fee']) { // Certificate attestation.
    // Get the Certificate attestation fee the student paid and subtract it from the total paid.
    mysqli_select_db($config, $database_config);
    $query_attestFeeInformation = "SELECT currency, amount
                                FROM extra_fees
                                WHERE target_id = ".sql_int($order_id)."
                                    AND fee_id = ".sql_int($_GET['attest_fee'])."
                                    AND status = 'pending'
                                ORDER BY fee_id DESC
                                LIMIT 1";
    $attestFeeInformation = mysqli_query($config, $query_attestFeeInformation) or die(mysqli_error($config));
    $row_attestFeeInformation = mysqli_fetch_assoc($attestFeeInformation);
    $totalRows_attestFeeInformation = mysqli_num_rows($attestFeeInformation);

    if ($totalRows_attestFeeInformation) { // Extract transaction amount just before it is recorded
        if ($row_attestFeeInformation['currency'] == $transaction_currency) {
            $cert_attest_fee__amount = $row_attestFeeInformation['amount'];
        }
        else {
            // Convert amount to the payment currency
            $cert_attest_fee__amount = convert_amount_from_currency($row_attestFeeInformation['currency'], $transaction_currency, $row_attestFeeInformation['amount']);
        }

        $transaction_amount = $transaction_amount - $cert_attest_fee__amount;
    }
}