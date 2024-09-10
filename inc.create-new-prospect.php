<?php 
    // Get the details of the student for this enrolment
    if (!$student_email_address) {
        $student_email_address =  $_SESSION['email'];
    }
    $dis_prog_id = get_backend_prog_id($dis_programme_url);
    $dis_order_id = student_is_a_prospect($student_email_address, $dis_prog_id);

    $new_programme_id = get_new_programme_info($dis_prog_id, 'programme_id');
    $programme_no_of_days = get_programme_duration_days($new_programme_id);

    $redirect_to_payment_page_after_sorting_cert_attestation = false;

    if ($redirect_to_payments_page_on_portal && $enable_cert_attestation) { // Check if student chose the option of Certificate attestation.
        $progs_in_certificate_attestation_array = array();

        if ($this_cert_attestation_fee_Info = $_COOKIE['s2certattestion']) {
            $progs_in_certificate_attestation_array = explode(",", $this_cert_attestation_fee_Info);
        }

        if (count($progs_in_certificate_attestation_array)) {
            if (in_array($new_programme_id, $progs_in_certificate_attestation_array)) { // If this programme is among those chosen for certificate attestation. Since it is the prog_id that gets stored in the cookie.
                $redirect_to_payment_page_after_sorting_cert_attestation = true;
            }
        }
    }
    
    if ($dis_order_id) { // Make sure there's a valid entry before proceeding
        mysqli_select_db($config, $database_config);
        $query_LetsGo = "SELECT p.* 
                        FROM prospects p 
                        WHERE p.p_id = ".sql_int($dis_order_id)." 
                        ORDER BY p.p_id DESC
                        LIMIT 1";
        $LetsGo = mysqli_query($config, $query_LetsGo) or die(mysqli_error($config));
        $row_LetsGo = mysqli_fetch_assoc($LetsGo);
        $totalRows_LetsGo = mysqli_num_rows($LetsGo);

        if ($redirect_to_payments_page_on_portal && $active_payment_plan) { // If the student chooses to make instalment payment from the cart page.
            if (!$redirect_to_payment_page_after_sorting_cert_attestation) {
                redirect(ROOT_DIR_PORTAL.'student/profile?segment=payment&active='.$dis_order_id.'&plan='.$active_payment_plan.'&currency='.$coresite_curr_currency);
            }
        } 
        elseif ($send_to_profile_for_completion) {
            redirect(ROOT_DIR_PORTAL.'student/profile?segment=payment&single='.$dis_order_id);
        }
    }
    else { // If not yet a prospect for this programme, then add prospect record using last known info
        mysqli_select_db($config, $database_config);
        $query_LastInfo = "SELECT p.* 
                        FROM prospects p
                        JOIN users_students us ON p.email = us.email 
                        JOIN users_students_prospect usp ON us.login_id = usp.login_id 
                        WHERE p.email = '".sql_string($student_email_address)."' 
                            AND p.no_followup_emails = '1' 
                        ORDER BY p.p_id DESC
                        LIMIT 1";
        $LastInfo = mysqli_query($config, $query_LastInfo) or die(mysqli_error($config));
        $row_LastInfo = mysqli_fetch_assoc($LastInfo);
        $totalRows_LastInfo = mysqli_num_rows($LastInfo);

        if (!$totalRows_LastInfo) { // If no programme ever taken, search for any prospect info available
            mysqli_select_db($config, $database_config);
            $query_LastInfo = "SELECT * 
                            FROM prospects p 
                            WHERE p.email = '".sql_string($student_email_address)."' 
                            ORDER BY p.p_id DESC
                            LIMIT 1";
            $LastInfo = mysqli_query($config, $query_LastInfo) or die(mysqli_error($config));
            $row_LastInfo = mysqli_fetch_assoc($LastInfo);
            $totalRows_LastInfo = mysqli_num_rows($LastInfo);
        }

        if ($totalRows_LastInfo) {
            if ($send_to_profile_for_completion) {
                $dis_prog_date = 0;
                
                $zis_enrol_branch = $curr_branch_and_study_center['branch'];
                $zis_enrol_study_center = $curr_branch_and_study_center['study_center'];
            }
            else {
                $dis_prog_date = create_dynamic_prog_session($dis_prog_id, date("Y-m-d"), $programme_no_of_days); 

                $zis_enrol_branch = 9; // Online Course
                $zis_enrol_study_center = 138; // UAE
            }
            
            mysqli_select_db($config, $database_config);
            $query_Insert = "INSERT INTO prospects (stage, full_name, first_name, middle_name, last_name, email, phone, gender, nationality, prog_interest, 
                                                    prog_date, source, entry_date, marketer_id, 
                                                    study_center, branch, emp_company, emp_position, no_followup_emails, country_of_residence) 
                            VALUES (
                                19,
                                '".sql_string(trim($row_LastInfo['full_name']))."',
                                '".sql_string(trim($row_LastInfo['first_name']))."',
                                '".sql_string(trim($row_LastInfo['middle_name']))."',
                                '".sql_string(trim($row_LastInfo['last_name']))."',
                                '".sql_string($row_LastInfo['email'])."',
                                '".sql_string($row_LastInfo['phone'])."',
                                '".sql_string($row_LastInfo['gender'])."',
                                ".sql_int($row_LastInfo['nationality']).",
                                ".sql_int($dis_prog_id).",
                                ".sql_int($dis_prog_date).",
                                0,
                                '".sql_string($time_now)."',
                                0,
                                ".sql_int($zis_enrol_study_center).",
                                ".sql_int($zis_enrol_branch).",
                                '".sql_string($row_LastInfo['emp_company'])."',
                                ".sql_int($row_LastInfo['emp_position']).", 
                                '1',
                                ".sql_int($row_LastInfo['country_of_residence'])."
                            )";
            $Insert = mysqli_query($config, $query_Insert) or die(mysqli_error($config));
            
            // Get information about insert
            mysqli_select_db($config, $database_config);
            $query_lastInsert = "SELECT p_id 
                                FROM prospects 
                                WHERE email = '".sql_string($row_LastInfo['email'])."' 
                                        AND prog_interest = '".sql_int($dis_prog_id)."' 
                                        AND prog_date = '".sql_int($dis_prog_date)."' 
                                ORDER BY p_id DESC 
                                LIMIT 1";
            $lastInsert = mysqli_query($config, $query_lastInsert) or die(mysqli_error($config));
            $row_lastInsert = mysqli_fetch_assoc($lastInsert);
            $totalRows_lastInsert = mysqli_num_rows($lastInsert);
            
            if ($totalRows_lastInsert) {
                $dis_order_id = $row_lastInsert['p_id'];

                // Record the stage history
                if ($dis_prog_date) {
                    record_stage_history($row_lastInsert['p_id'], $dis_prog_date, 19);
                }

                if ($redirect_to_payments_page_on_portal && $active_payment_plan && $dis_order_id) { // If the student chooses to make instalment payment from the cart page.
                    if (!$redirect_to_payment_page_after_sorting_cert_attestation) {
                        redirect(ROOT_DIR_PORTAL.'student/profile?segment=payment&active='.$dis_order_id.'&plan='.$active_payment_plan.'&currency='.$coresite_curr_currency);
                    }
                }

                if (!$do_not_redirect_after_creating_prospect && !$redirect_to_payment_page_after_sorting_cert_attestation) {
                    redirect(ROOT_DIR.$location_slug.'programme/'.$dis_programme_url);
                }
            }
            else {
                // Something is wrong and I dont know what to do yet
                redirect(ROOT_DIR);
            }
        }
        else {
            // Something is wrong and I dont know what to do yet
            redirect(ROOT_DIR);
        }
    }

    if ($redirect_to_payment_page_after_sorting_cert_attestation && $dis_order_id && $active_payment_plan) {
        $cert__attestation_fee = $certificate_attestation_fees["fee"];

        if ($dis_programme_currency) { // If the programme currency variable exists.
            $cert__attestation_fee_currency = $dis_programme_currency;
        }
        else { 
            $cert__attestation_fee_currency = $certificate_attestation_fees["currency"];
        }
                                                                    
        if ($certificate_attestation_fees["currency"] != $cert__attestation_fee_currency) { // Convert the attestation fees to the amount in the currency of the chosen country.
            $cert__attestation_fee = convert_amount_from_currency($certificate_attestation_fees["currency"], $cert__attestation_fee_currency, $certificate_attestation_fees["fee"]);
        }
        
        $recorded_extra_fee_id = record_extra_fees_payment($dis_order_id, "certificate attestation", $cert__attestation_fee_currency, $cert__attestation_fee, 'pending');

        if ($recorded_extra_fee_id) { // Remove the programme ID from the Cert attestation cookie in case there are other programme IDs in it.
            // This segment was borrowed from add_to_cart_ajax.php file.
            $programme_Id_Key_In_Cookie = array_search($new_programme_id, $progs_in_certificate_attestation_array);

            if ($programme_Id_Key_In_Cookie !== false) {
                unset($progs_in_certificate_attestation_array[$programme_Id_Key_In_Cookie]);

                $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_attestation_array);

                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie("s2certattestion", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                    }
                }
            }
        }

        redirect(ROOT_DIR_PORTAL.'student/profile?segment=payment&active='.$dis_order_id.'&plan='.$active_payment_plan.'&currency='.$coresite_curr_currency);
    }