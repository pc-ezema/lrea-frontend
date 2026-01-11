<div class="row">
    <?php if ($totalRows_DisProg) { ?>
        <div class="col-lg-12">
            <div class="mb-3">
                <p class="text-dark">Order details (<?php echo $totalRows_DisProg; ?> <?php echo ($totalRows_DisProg > 1) ? 'Items' : 'Item'; ?>)</p>
            </div>
        </div>

        <div class="col-lg-8">
            <?php echo $show_coupon_applied_msg; 
                if ($enable_prog_certificate_extra_fees) { // Extra fees variables
                    $progs_in_certificate_extra_fees_array = array();

                    if ($thiss2certextrafeeInfo = $_COOKIE['s2certextrafee']) {
                        $progs_in_certificate_extra_fees_array = explode(",", $thiss2certextrafeeInfo);
                    }
                }

                if ($enable_othm_certificate_extra_fee) { // OTHM Fee Variables
                    $progs_in_certificate_othm_array = array();

                    if ($thiss2othmextrafeeInfo = $_COOKIE['s2othmextrafee']) {
                        $progs_in_certificate_othm_array = explode(",", $thiss2othmextrafeeInfo);
                    }
                }

                if ($enable_cert_attestation) { // Cert attestation Cookie.
                    $progs_in_certificate_attestation_array = array();

                    if ($this_cert_attestation_fee_Info = $_COOKIE['s2certattestion']) {
                        $progs_in_certificate_attestation_array = explode(",", $this_cert_attestation_fee_Info);
                    }
                }
            ?>

            <div class="row cart-view-sect">
                <?php do { ?>
                    <?php require('inc.programme-variables.php'); ?>

                    <?php if ($enable_data_layer_scripts || $enable_facebook_capi_events_tracking) { // This helps to generate the dataHash script. I remove the check for if site version is online so that we can be able to debug the Facebook Capi on localhost but the check is placed at the point where the datalayer script executes.
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
                                "dimension2" => $dis_programme_format
                            );
                        }
                    ?>

                    <?php 
                        $prog_is_eligible = false;
                        if ($totalRows_couponValidForAllProgs) {
                            $discount_amount = ($row_checkifCouponValid['coupon_discount'] / 100) * $dis_programme_fee;
                            $discounted_programme_total = $dis_programme_fee - $discount_amount;

                            $total_discounted_fee += $discount_amount;
                            $prog_is_eligible = true;
                        }
                        else {
                            if (check_if_programme_matches_coupon($row_checkifCouponValid['coupon_id'], $dis_programme_id)) {
                                $discount_amount = ($row_checkifCouponValid['coupon_discount'] / 100) * $dis_programme_fee;
                                $discounted_programme_total = $dis_programme_fee - $discount_amount;

                                $total_discounted_fee += $discount_amount;
                                $prog_is_eligible = true;
                            }
                        }

                        if ($enable_part_payment_in_cart) { // The code block in this include file below was borrowed from the file "inc.payment-status-parameter.php" on portal.
                            include('inc.programme-part-payment-eligibility-calculator.php');

                            // This check ensures that the text is maintained if the symbol is non-existent
                            if ($profile_student_currency == $dis_programme_currency) {
                                $symbol_of_profile_student_currency = $symbol_for_programme_currency;
                            }
                            else {
                                $symbol_of_profile_student_currency = $profile_student_currency;
                            }
                        }
                        
                        if (!$part_payment_allowed) {
                            $profile_student_amount_saved = (float)$dis_programme_original_fee - (float)$dis_programme_fee;
                            $symbol_of_profile_student_currency = $symbol_for_programme_currency;
                        }

                        if ($prog_is_eligible) {
                            $profile_student_amount_saved = $discount_amount;
                        }

                        $profile_student_total_amount_saved += $profile_student_amount_saved;
                    ?>

                    <div class="col-lg-6 mb-4 position-relative">
                        <div class="card course-new-card position-relative bg-white border rounded-8 h-100">

                            <div class="prog-img-3">
                                <img src="<?php echo $dis_programme_thumbnail; ?>" class="img-fluid object-fit-cover object-position-center w-100 h-100" />
                            </div>

                            <div class="card-body prog-card-body d-flex flex-column p-5">

                                <div class="flex-grow-1">
                                    <h5 class="mb-4 text-bold">
                                        <a class="text-white" href="<?php echo $link_root_path.$dis_programme_a_href; ?>" style="text-decoration: none;"><?php echo $dis_programme_name; ?></a>
                                    </h5>
                                    <div class="row payment-row">
                                        <div class="col-lg-12">
                                            <?php if ($enable_prog_certificate_extra_fees && in_array($dis_programme_subcategory_id, array(883, 409))) { // For now, extra fees applies to only PCCs and APCCs. ?>
                                                <?php if ($display_single_extra_fees_checkbox) { // Single extra fee checkbox ?>
                                                    <?php 
                                                        $cert_extra_fee = $certificate_extra_fees["fee"];

                                                        if ($certificate_extra_fees["currency"] != $dis_programme_currency) { // Convert the extra fees to the amount in the currency of the chosen country.
                                                            $cert_extra_fee = convert_amount_from_currency($certificate_extra_fees["currency"], $dis_programme_currency, $certificate_extra_fees["fee"]);
                                                        }
                                                    ?>
                                                    <div class="form-check text-white ft-sm <?php if (!$enable_cert_attestation) echo 'mb-3'; ?> extra-fees-parent">
                                                        <input type="checkbox" class="cert-extra-fees form-check-input " <?php if (count($progs_in_certificate_extra_fees_array) && in_array($dis_programme_id, $progs_in_certificate_extra_fees_array)) echo "checked"; ?> id="cert_extra_fee<?php echo $dis_programme_id; ?>" name="cert_extra_fee" data-certextrafee="<?php echo round($cert_extra_fee); ?>" data-certextrafeeid="<?php echo $dis_programme_id; ?>">
                                                        <label class="form-check-label" for="cert_extra_fee<?php echo $dis_programme_id; ?>">Certificate Printing + Shipping: <?php echo $symbol_of_profile_student_currency.' '.number_format($cert_extra_fee, 2); ?></label>
                                                    </div>
                                                <?php } else { // Different extra fee checkboxes (i.e Printing and shipping fees) ?>
                                                    <?php 
                                                        $cert_printing_fee = $certificate_extra_fees["printing_fee"];
                                                        $cert_shipping_fee = $certificate_extra_fees["shipping_fee"];

                                                        if ($certificate_extra_fees["currency"] != $dis_programme_currency) { // Convert the extra fees to the amount in the currency of the chosen country.
                                                            $cert_printing_fee = convert_amount_from_currency($certificate_extra_fees["currency"], $dis_programme_currency, $certificate_extra_fees["printing_fee"]);
                                                            $cert_shipping_fee = convert_amount_from_currency($certificate_extra_fees["currency"], $dis_programme_currency, $certificate_extra_fees["shipping_fee"]);
                                                        }
                                                    ?>

                                                    <div class="extra-fees-parent">
                                                        <div class="form-check text-white ft-sm">
                                                            <input class="form-check-input cert_printing_fee" type="checkbox" <?php if (count($progs_in_certificate_extra_fees_array) && in_array($dis_programme_id, $progs_in_certificate_extra_fees_array)) echo "checked"; ?> id="printing_fee<?php echo $dis_programme_id; ?>" name="cert_printing_fee" data-certfee="<?php echo round($cert_printing_fee); ?>" data-certprintingid="<?php echo $dis_programme_id; ?>">
                                                            <label class="form-check-label" for="printing_fee<?php echo $dis_programme_id; ?>">
                                                                Certificate Printing: <?php echo $symbol_of_profile_student_currency.' '.show_amount($cert_printing_fee); ?>
                                                            </label>
                                                        </div>
                                                        <div class="form-check text-white ft-sm">
                                                            <input class="form-check-input cert_shipping_fee" type="checkbox" id="shipping_fee<?php echo $dis_programme_id; ?>" <?php if (count($progs_in_certificate_extra_fees_array) && in_array($dis_programme_id, $progs_in_certificate_extra_fees_array)) echo "checked"; ?> name="cert_shipping_fee" data-certship="<?php echo round($cert_shipping_fee); ?>" data-certshippingid="<?php echo $dis_programme_id; ?>">
                                                            <label class="form-check-label" for="shipping_fee<?php echo $dis_programme_id; ?>">
                                                                Certificate Shipping: <?php echo $symbol_of_profile_student_currency.' '.show_amount($cert_shipping_fee); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>

                                        <div class="col-lg-12">
                                            <?php if ($enable_othm_certificate_extra_fee && in_array($dis_programme_subcategory_id, array(883, 409))) { ?>
                                                <?php 
                                                    $othm_cert_fee = $certificate_extra_fees["othm_cert_price"];

                                                    if ($certificate_extra_fees["othm_cert_currency"] != $dis_programme_currency) { // Convert the extra fees to the amount in the currency of the chosen country.
                                                        $othm_cert_fee = convert_amount_from_currency($certificate_extra_fees["othm_cert_currency"], $dis_programme_currency, $certificate_extra_fees["othm_cert_price"]);
                                                    }
                                                ?>
                                                <div class="form-check text-white ft-sm extra-fees-parent">
                                                    <input class="form-check-input othm_cert" type="checkbox" id="othm_cert<?php echo $dis_programme_id; ?>" <?php if (count($progs_in_certificate_othm_array) && in_array($dis_programme_id, $progs_in_certificate_othm_array)) echo "checked"; ?> name="othm_cert" data-othmcertfee="<?php echo round($othm_cert_fee); ?>" data-othmcertid="<?php echo $dis_programme_id; ?>">
                                                    <label class="form-check-label" for="othm_cert<?php echo $dis_programme_id; ?>">
                                                        e-Certificate from OTHM Qualifications, UK: <?php echo $symbol_of_profile_student_currency.' '.show_amount($othm_cert_fee); ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="col-lg-12">
                                            <?php if ($enable_cert_attestation && in_array($dis_programme_subcategory_id, array(883, 409))) { // For now, UK Certificate Attestation applies to only PCCs and APCCs. ?>
                                                <?php 
                                                    $cert_attestation_fee = $certificate_attestation_fees["fee"]; // This is declared in Custom.php

                                                    if ($certificate_attestation_fees["currency"] != $dis_programme_currency) { // Convert the extra fees to the amount in the currency of the chosen country.
                                                        $cert_attestation_fee = convert_amount_from_currency($certificate_attestation_fees["currency"], $dis_programme_currency, $certificate_attestation_fees["fee"]);
                                                    }
                                                ?>
                                                <div class="form-check text-white ft-sm cert-attestation-parent">
                                                    <input class="form-check-input cert-attestation-fees" type="checkbox" id="cert_attestation_<?php echo $dis_programme_id; ?>" <?php if (count($progs_in_certificate_attestation_array) && in_array($dis_programme_id, $progs_in_certificate_attestation_array)) echo "checked"; ?> name="cert_attestation_fee" data-certattestationfee="<?php echo round($cert_attestation_fee); ?>" data-attestationfeeid="<?php echo $dis_programme_id; ?>">
                                                    <label class="form-check-label" for="cert_attestation_<?php echo $dis_programme_id; ?>">
                                                        UK Notarisation and Apostille Service: <?php echo $symbol_of_profile_student_currency.' '.number_format($cert_attestation_fee, 2); ?>
                                                    </label>
                                                    <div class="mb-3">
                                                        <a href="#" class="text-white" data-bs-toggle="modal" data-bs-target="#attestationInfo">
                                                            <i class="fa fa-info-circle fa-lg"></i> <em><u>Important information</u></em>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 align-items-center mb-3">
                                    <div class="d-flex align-items-end">
                                        <h5 class="lto-price-on text-bold text-white mb-0 me-1">
                                            <?php 
                                                if ($prog_is_eligible) {
                                                    echo '<small class="text-danger"><s>'.$symbol_for_programme_currency.' '.show_amount($dis_programme_fee).'</s></small> <span>'.$symbol_for_programme_currency.' '.show_amount($discounted_programme_total).'</span>';
                                                    echo "<br/><span class='badge bg-success'>Discount -".$row_checkifCouponValid['coupon_discount']."%</span>"; 
                                                }
                                                else {
                                                    if ($cosmetics_dis_programme_original_fee) {
                                                        echo '<span>'.$symbol_for_programme_currency.' '.show_amount($dis_programme_fee).'</span>';
                                                    }
                                                    else {
                                                        echo '<span>'.$symbol_for_programme_currency.' '.show_amount($dis_programme_fee).'</span>';
                                                    }
                                                }
                                            ?>
                                        </h5>                                        
                                    </div>
                                    <div class="<?php echo $prog_is_eligible ? 'd-none' : 'd-flex'; ?> align-items-end">
                                        <strike class="text-white">
                                            <h6 class="ft-md text-white mb-0 me-1 lto-price-off">
                                                <?php if ($cosmetics_dis_programme_original_fee) { ?>
                                                    <?php echo '<span class="instalment-full-fee-updated">'.$symbol_for_programme_currency.' '.show_amount($cosmetics_dis_programme_fee).'</span>'; ?>
                                                <?php } else { ?>
                                                    <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_original_fee); ?>
                                                <?php } ?>
                                            </h6>
                                        </strike>
                                    </div>
                                </div>

                                <button class="btn btn-custom-size btn-light remove-from-page" id="<?php echo $dis_programme_id; ?>"><i data-feather="trash"></i> Remove from cart</button>

                                <?php if ($display_degree_course_instalment_fees) { ?>
                                    <div class="col-sm-12 text-left text-sm-right payment-plan-segment">
                                        <?php if (!$prog_is_eligible) { ?>
                                            <?php if ($part_payment_allowed && $enable_part_payment_in_cart) { // Part payment segment for eligible courses. ?>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <div class="form-check form-switch ml-3">
                                                        <input type="checkbox" class="form-check-input customSwitchPayment" role="switch" id="customSwitchPayment<?php echo $dis_programme_id; ?>">
                                                        <label class="form-check-label" for="customSwitchPayment<?php echo $dis_programme_id; ?>" <?php if ($_GET['course'] == $dis_programme_url) echo 'id="active-payment-plan"'; ?>>Choose a payment plan</label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } elseif ($prog_is_eligible && $part_payment_allowed) { ?>
                                            <div class="text-left py-2 lto-applied border p-3 rounded bg-light text-center" style="color:#000000">
                                                <a href="<?php echo $link_root_path."cart/remove-coupon"; ?>" class="text-danger make-bold confirm_remove_coupon"><u>Remove Coupon</u></a> to initiate <span class="make-bold">Instalment Payment.</span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <div class="row">
                                    <div class="col-lg-7 offset-lg-5">
                                        <form action="<?php echo $link_root_path.'instalment/'.$dis_programme_url; ?>" class="payment-plan-form" style="display: none;" method="get">
                                            <h5 class="make-bold text-right mb-3">Pay in instalments</h5>

                                            <?php foreach ($instalment_months as $ins_months => $ins_number) { ?>
                                                <?php // Re-calculate the increment percentage for each instalment
                                                    $instalmental_fee_percentage_increase = get_increment_percentage($ins_number);

                                                    $profile_student_amount_incremented = round($profile_student_amount + ($profile_student_amount * $instalmental_fee_percentage_increase), 2); // Increase by 20 percent
                                                    $profile_student_amount_saved = round($profile_student_amount_incremented - $profile_student_amount, 2); 
                                                    $profile_monthly_amount = round($profile_student_amount_incremented / $part_payment_allowed, 2);

                                                    $savings_per_this_increment = $overall_amount_incremented - $profile_student_amount_incremented;

                                                    // Field Calculations
                                                    $each_amount_per_this_instalment = round($profile_monthly_amount * $ins_months, 2);
                                                    $field_value_per_this_instalment = INSTALMENT_PLAN_OBFUSCATION + $ins_months;
                                                    $field_overall_amount_per_this_instalment = $each_amount_per_this_instalment * $ins_number;
                                                    $amount_saved_per_this_instalment = round($overall_amount_incremented - $field_overall_amount_per_this_instalment, 2);

                                                    // Field Labels
                                                    $is_field_selected_per_this_instalment = ($_GET['course'] == $dis_programme_url && ($_GET['plan'] == (INSTALMENT_PLAN_OBFUSCATION + $ins_months))) ? 'selected' : '';
                                                    $is_field_checked_per_this_instalment = ($_GET['course'] == $dis_programme_url && ($_GET['plan'] == (INSTALMENT_PLAN_OBFUSCATION + $ins_months))) ? 'checked' : '';
                                                    $pluralization_per_this_instalment = ($ins_number > 1) ? 'instalments' : 'instalment';
                                                    $each_amount_per_this_instalment_label = $symbol_of_profile_student_currency.' '.show_amount($each_amount_per_this_instalment);
                                                    $field_overall_amount_per_this_instalment_label = $symbol_of_profile_student_currency.' '.show_amount($field_overall_amount_per_this_instalment);
                                                    $amount_saved_per_this_instalment_label = $symbol_of_profile_student_currency.' '.show_amount($amount_saved_per_this_instalment);

                                                    if ($limit_duration_gap_of_instalment_payments) { // If the gap limit is enabled, check the months gap between the instalments. If it is more than the gap limit, use the gap limit we set instead.
                                                        if ($ins_months > $gap_between_instalment_payments) {
                                                            $ins_months = $gap_between_instalment_payments;
                                                        }

                                                        if ($dis_programme_currency == 'NGN') {
                                                            $ins_months = $ins_months < 3 ? 1 : 3; // Based on limitations by Paystack's Subscription API
                                                        }
                                                    }
                                                ?>

                                                <div class="form-group d-flex justify-content-end border-bottom" style="align-items: baseline;">
                                                    <label for="<?php echo 'instalment'.$dis_programme_id.$ins_number; ?>">Pay <strong><?php echo $field_overall_amount_per_this_instalment_label; ?></strong> in <strong><?php echo $ins_number; ?> <span class="text-danger"><?php echo $pluralization_per_this_instalment; ?></span></strong> of <strong><?php echo $each_amount_per_this_instalment_label; ?></strong> each (saves <strong><?php echo $amount_saved_per_this_instalment_label; ?></strong>) </label>
                                                    <input type="radio" name="plan" value="<?php echo $field_value_per_this_instalment; ?>" id="<?php echo 'instalment'.$dis_programme_id.$ins_number; ?>" data-num_of_instalments="<?php echo $ins_number; ?>" data-amount_per_instalment="<?php echo $each_amount_per_this_instalment_label; ?>" data-num_of_months_between_instalments="<?php echo $ins_months; ?>" data-target_container_id="<?php echo 'instalment-table-container-'.$dis_programme_id; ?>" data-full-pay="<?php echo $field_overall_amount_per_this_instalment_label; ?>" name="instalment-breakdown" class="transform-scale-1 ml-3 instalment-plans" <?php echo $is_field_checked_per_this_instalment; ?>>
                                                </div>
                                            <?php } ?>

                                            <div id="<?php echo 'instalment-table-container-'.$dis_programme_id; ?>"></div>

                                            <div class="row">
                                                <div class="col-lg-10 offset-lg-1">
                                                    <div class="form-group instalment-submit-button text-center">
                                                        <button type="submit" class="btn btn-custom-color-1 btn-custom text-main-4 btn-custom-size make-bold">Pay first instalment now</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <?php 
                                    if ($enable_prog_certificate_extra_fees && count($progs_in_certificate_extra_fees_array) && in_array($dis_programme_id, $progs_in_certificate_extra_fees_array)) { // Add the certificate extra fees to the programme fee.
                                        if (!$display_single_extra_fees_checkbox && $cert_printing_fee && $cert_shipping_fee) { // Different extra fee checkboxes (i.e Printing and shipping fees)
                                            $cert_extra_fee = $cert_printing_fee + $cert_shipping_fee;
                                        }

                                        $dis_programme_fee += $cert_extra_fee;
                                    }

                                    if ($enable_cert_attestation && count($progs_in_certificate_attestation_array) && in_array($dis_programme_id, $progs_in_certificate_attestation_array)) { // Add the attestation fees to the programme fee.
                                        $dis_programme_fee += $cert_attestation_fee;
                                    }

                                    if ($enable_othm_certificate_extra_fee && count($progs_in_certificate_othm_array) && in_array($dis_programme_id, $progs_in_certificate_othm_array)) { // Add the attestation fees to the programme fee.
                                        $dis_programme_fee += $othm_cert_fee;
                                    }
                                
                                    $total_fees_in_cart+=(float)$dis_programme_fee; 
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); } ?>
            </div>
        </div> 

        <div class="col-lg-4 lto-checkout">
            <?php if ($enable_prog_certificate_extra_fees) { // Shipping address form ?>
                <?php // Get the shipping details from the cookie if it exists
                    if ($this2shipdetails = $_COOKIE['s2shipdetails']) {
                        $extra_fee_shipping_details = unserialize($this2shipdetails);
                    }
                    else {
                        $extra_fee_shipping_details = array();
                    }
                ?>
                <div class="card card-body bg-white border-0 rounded-0 mb-3 shipping_info_segment lto-checkout mb-3">
                    <div class="card-body">
                        <h3>Shipping Info</h3>
                        <form action="" method="post" class="row shipping_address_form">
                            <div class="col-md-12 mb-2">
                                <label for="shipping_country">Country <sup class="text-danger">*</sup></label>
                                <select name="shipping_country" class="form-select shipping_country border" id="shipping_country">
                                    <option value="">Select Country</option>
                                    <?php // Get all countries. I tried to re-use the countries query from the navbar but the disadvantage is that if the cart segment gets reloaded via ajax, this dropdown won't get populated with the countries. Hence why i used a new query.
                                        mysqli_select_db($config, $database_config);
                                        $query_All_Countries = "SELECT mmctry.*  
                                                                FROM myip_ms_countries mmctry 
                                                                ORDER BY mmctry.countryName ASC";
                                        $All_Countries = mysqli_query($config, $query_All_Countries) or die(mysqli_error($config));
                                        $row_All_Countries = mysqli_fetch_assoc($All_Countries);
                                        $totalRows_All_Countries = mysqli_num_rows($All_Countries);
                                    ?>
                                    <?php if ($totalRows_All_Countries) { ?>
                                        <?php do { ?> 
                                            <option value="<?php echo $row_All_Countries['url_name']; ?>" <?php if (count($extra_fee_shipping_details) && ($extra_fee_shipping_details["country"] == $row_All_Countries['url_name'])) echo ' selected' ?>>
                                                <?php echo $row_All_Countries['countryName']; ?>
                                            </option>
                                        <?php } while($row_All_Countries = mysqli_fetch_assoc($All_Countries)); ?>
                                    <?php } ?>
                                </select>
                                <span id="validate_country"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="shipping_city">City <sup class="text-danger">*</sup></label>
                                <input name="shipping_city" class="form-control shipping_city border" id="shipping_city" value="<?php if (count($extra_fee_shipping_details) && $extra_fee_shipping_details["city"]) echo $extra_fee_shipping_details["city"]; ?>">
                                <span id="validate_city"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="shipping_postal_code">Postal Code <sup class="text-danger">*</sup></label>
                                <input name="shipping_postal_code" class="form-control shipping_postal_code border" id="shipping_postal_code" value="<?php if (count($extra_fee_shipping_details) && $extra_fee_shipping_details["postal_code"]) echo $extra_fee_shipping_details["postal_code"]; ?>">
                                <span id="validate_postal_code"></span>
                            </div>
                            <div class="col-md-12">
                                <label for="shipping_address">Address <sup class="text-danger font-bold">*</sup></label>
                                <textarea name="shipping_address" class="form-control shipping_address border" id="shipping_address"><?php if (count($extra_fee_shipping_details) && $extra_fee_shipping_details["address"]) echo $extra_fee_shipping_details["address"]; ?></textarea>
                                <span id="validate_address"></span>
                            </div>
                            <input type="hidden" name="action" value="addShippingAddress">
                        </form>
                    </div>
                </div>
            <?php } ?>
            
            <div class="card card-body bg-white border-0 rounded-0 mb-3 lto-checkout">
                <img src="<?php echo MEDIA_ROOT_DIR; ?>images/payment-logos.svg" class="img-fluid py-4" />
                <?php if ($total_discounted_fee) {
                        $subtotal_fees_in_cart = $total_fees_in_cart;
                        $total_fees_in_cart = $subtotal_fees_in_cart - $total_discounted_fee;
                    ?>
                    <div class="card-total-view d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 make-bold">SubTotal</p>
                        </div>
                        <div>
                            <p class="mb-0 make-bold"><?php echo $symbol_for_programme_currency.' '.show_amount($subtotal_fees_in_cart) ?></p>
                        </div>
                    </div>
                    <div class="card-total-view d-flex justify-content-between align-items-center mt-3 mb-3">
                        <div>
                            <p class="text-muted mb-0 make-bold">Discount</p>
                        </div>
                        <div>
                            <p class="mb-0 make-bold">- <?php echo $symbol_for_programme_currency.' '.show_amount($total_discounted_fee) ?></p>
                        </div>
                    </div>
                <?php } ?>
                <div class="discount-sect">
                    <?php if ($enable_prog_certificate_extra_fees) { ?>
                        <button type="button" disabled class="btn btn-warning btn-block super-btn make-bold btn-bord-radius disable_checkout mb-3" style="font-size: 1rem;">
                            <i class="fa fa-arrow-circle-up fa-lg"></i> Add Shipping Info <br>before Checkout
                        </button>
                    <?php } ?>
                    
                    <a href="<?php echo $link_root_path; ?>checkout" class="d-flex justify-content-between align-items-center btn btn-custom-color-1 btn-custom btn-custom-size text-white mb-3 checkout_button enable_checkout">
                        <span>Secure Checkout</span>
                        <span class="total-in-checkout" data-progtotal="<?php echo round($total_fees_in_cart); ?>" data-fbCur="<?php echo $dis_programme_currency; ?>" data-fbVal="<?php echo round($total_fees_in_cart, 2); ?>" data-fbNum="<?php echo $totalRows_DisProg; ?>">
                            <?php echo $symbol_for_programme_currency.' '.show_amount($total_fees_in_cart); ?>
                        </span>
                    </a>
                </div>

                <?php if ($allow_discount_code) { ?>
                    <div class="discount-sect mb-3">
                        <p class="text-center cursor-pointer text-dark" data-bs-toggle="collapse" data-bs-target="#collapseDiscount" aria-expanded="false" aria-controls="collapseDiscount">I have a<?php echo $add_new_to_msg; ?> discount</p>

                        <div class="collapse bg-transparent" id="collapseDiscount">
                            <div class="card card-body border-0 bg-transparent p-0 coupon-apply-sect">
                                <form class="row g-3">
                                    <div class="col-lg-12 mb-lg-0 mb-1">
                                        <label for="discount_code" class="visually-hidden">Discount code</label>
                                        <input type="text" class="form-control rounded-0 btn-custom-size-2" name="discount_code" value="<?php echo $row_checkifCouponValid['coupon_code']; ?>" id="discount_code" placeholder="Enter discount code">
                                        <span class="text-danger" id="is-invalid"></span>
                                    </div>
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-custom-color-1 btn-custom text-white btn-custom-size-2 btn-block" id="discount_btn">Apply</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            
        </div>
    <?php } else { ?>
        <div class="col-lg-4 mx-auto" style="padding-top:100px;padding-bottom:100px">
            <img src="<?php echo ROOT_DIR; ?>images/no-content.svg" class="img-fluid d-block mx-auto mb-4">
            <h3 class="make-bold text-center mb-4">Your Cart is empty</h3>
            <a href="<?php echo $link_root_path; ?>" class="btn btn-danger btn-block">Back to Homepage</a>
        </div>
    <?php } ?>
</div>

<!-- Attestation Info -->
<div class="modal fade" id="attestationInfo" tabindex="-1" role="dialog" aria-labelledby="attestationInfoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attestationInfoLabel"><strong>Important Information</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol>
                    <li>Please note that the attestation service can take up to 90 days before completion.</li>
                    <li>Your original certificate will only be dispatched when the attestation is completed.</li>
                    <li>This fee only covers the United Kingdom attestation service.</li>
                </ol>

                <a href="<?php echo ROOT_DIR ?>images/example-Apostille-redacted.jpg" target="_blank">
                    <u>
                        See a sample attested certificate
                    </u>
                </a>
            </div>
        </div>
    </div>
</div>