<?php  
    $this_prog_coupon_isValid = false;
    require('inc.programme-variables.php');

    if ($totalRows_checkifCouponValid) {
        if ($coupon_id) {
            if ($totalRows_couponValidForAllProgs) {
                $if_programme_matches_coupon = true;
            }
            else {
                $if_programme_matches_coupon = check_if_programme_matches_coupon($coupon_id, $dis_programme_id);
            }
        }

        if ($if_programme_matches_coupon) {
            $all_valid_discounted_prog[] = $dis_programme_id;

            $this_discounted_amout = ($this_coupon_discount / 100) * $dis_programme_fee;
            $this_db_fee_discount_amt = ($this_coupon_discount / 100) * $row_DisProg['discounted_fee'];

            $total_fees_in_cart += ($dis_programme_fee - $this_discounted_amout);
            $total_db_fees_in_cart += ($row_DisProg['discounted_fee'] - $this_db_fee_discount_amt);

            $this_prog_coupon_isValid = true;
        }
    }

    if (!$this_prog_coupon_isValid)  {
        $total_fees_in_cart += $dis_programme_fee;
        $total_db_fees_in_cart += $row_DisProg['discounted_fee'];
    }

    if ($enable_prog_certificate_extra_fees) { // Extra Fees calculation.
        $cert_extra_fee = $cert_extra_fee_in_aed = 0;

        if (count($progs_in_certificate_extra_fees_array) && in_array($dis_programme_id, $progs_in_certificate_extra_fees_array)) { // Add the extra fees for certificate.
            if ($display_single_extra_fees_checkbox) { // Shipping and printing fee already added together from the onset.                
                $cert_extra_fee = $certificate_extra_fees["fee"];
                $cert_extra_fee_in_aed = convert_amount_from_currency($certificate_extra_fees["currency"], "GBP", $cert_extra_fee); // Get the extra fee equivalent amount in GBP. (The amount being passed to the payment gateway calculation aspect is by default GBP but gets converted to whichever currency the gateway accepts.)

                if ($certificate_extra_fees["currency"] != $dis_programme_currency) { // Get the ceritificate fee equivalent amount of the chosen country.
                    $cert_extra_fee = convert_amount_from_currency($certificate_extra_fees["currency"], $dis_programme_currency, $cert_extra_fee);
                }
            }
            else { // For Different extra fee checkboxes (i.e Printing and shipping fees). Incase the fees get separated later.
                $cert_extra_fee = $certificate_extra_fees["printing_fee"] + $certificate_extra_fees["shipping_fee"];
                $cert_extra_fee_in_aed = convert_amount_from_currency($certificate_extra_fees["currency"], "GBP", $cert_extra_fee); // Get the printing and shipping fee equivalent amount in GBP.

                if ($certificate_extra_fees["currency"] != $dis_programme_currency) { // Get the printing and shipping fee equivalent amount of the chosen country.
                    $cert_extra_fee = convert_amount_from_currency($certificate_extra_fees["currency"], $dis_programme_currency, $cert_extra_fee);
                }
            }

            $total_fees_in_cart += $cert_extra_fee;
            $total_db_fees_in_cart += $cert_extra_fee_in_aed;

            // Prepare variables that will be used to record the extra fee into the database.
            $cert__extra_fee = $cert_extra_fee;
            $cert__extra_fee_currency = $dis_programme_currency;
        }
    }

    if ($enable_othm_certificate_extra_fee) { // Extra Fees calculation.
        $cert_othm_fee = $cert_othm_fee_in_aed = 0;

        if (count($progs_in_certificate_othm_fees_array) && in_array($dis_programme_id, $progs_in_certificate_othm_fees_array)) { // Add the OTHM fees for certificate.
            $cert_othm_fee = $certificate_extra_fees["othm_cert_price"];
            $cert_othm_fee_in_aed = convert_amount_from_currency($certificate_extra_fees["othm_cert_price"], "GBP", $cert_othm_fee); // Get the extra fee equivalent amount in GBP. (The amount being passed to the payment gateway calculation aspect is by default GBP but gets converted to whichever currency the gateway accepts.)

            if ($certificate_extra_fees["othm_cert_currency"] != $dis_programme_currency) { // Get the OTHM fee equivalent amount of the chosen country.
                $cert_othm_fee = convert_amount_from_currency($certificate_extra_fees["othm_cert_currency"], $dis_programme_currency, $cert_othm_fee);
            }

            // Add the amount to the total fee of everything in the cart.
            $total_fees_in_cart += $cert_othm_fee;
            $total_db_fees_in_cart += $cert_othm_fee_in_aed;

            // Prepare variables that will be used to record the OTHM fee into the database.
            $cert__othm_fee = $cert_othm_fee;
            $cert__othm_fee_currency = $dis_programme_currency;
        }
    }

    if ($enable_cert_attestation) { // Certificate attestation calculation.
        $cert_attest_fee = $cert_attest_fee_in_aed = 0;

        if (count($progs_in_certificate_attestation_array) && in_array($dis_programme_id, $progs_in_certificate_attestation_array)) { // Add the attestation fees for certificate.
            $cert_attest_fee = $cert_attest_fee_in_aed = $certificate_attestation_fees["fee"];

            if ($certificate_attestation_fees["currency"] != $dis_programme_currency) { // Get the attestation fee equivalent amount of the chosen country.
                $cert_attest_fee = convert_amount_from_currency($certificate_attestation_fees["currency"], $dis_programme_currency, $cert_attest_fee);
            }

            // Add the amount to the total fee of everything in the cart.
            $total_fees_in_cart += $cert_attest_fee;
            $total_db_fees_in_cart += $cert_attest_fee_in_aed;

            // Prepare variables that will be used to record the attestation fee into the database.
            $cert__attestation_fee = $cert_attest_fee;
            $cert__attestation_fee_currency = $dis_programme_currency;
        }
    }

    if ($execute_for_dataHash_checkout_event_only && $enable_data_layer_scripts && $populate_dataLayer_checkout_items && $site_version == "online") {
        $dis_dataHash_programme_subject = get_programme_relationship($dis_programme_id, 'programme subjects', ', ');
        $dis_dataHash_programme_level = get_programme_relationship($dis_programme_id, 'programme difficulty');
        $dataHash_checkout_currency = $dis_programme_currency;
        $datalayer_asset_collection_source = "Home Page";
        $asset_position++;

        $data_layer_programme_items[] = array(
            "name" => $dis_programme_name,
            "id" => $row_DisProg['course_code'] ? $row_DisProg['course_code'] : "",
            "price" => $dis_programme_fee,
            "brand" => $dis_dataHash_programme_subject ? $dis_dataHash_programme_subject : "Business",
            "category" => ucwords($dis_programme_type),
            "variant" => $dis_dataHash_programme_level ? $dis_dataHash_programme_level : "",
            "list" => $datalayer_asset_collection_source,
            "position" => $asset_position,
            "dimension1" => $dis_programme_duration,
            "dimension2" => $dis_programme_format,
            "dimension3" => ""
        );
    }

    //  echo '<pre>'; var_dump($dis_programme_fee); echo '</pre>';
    //             exit;