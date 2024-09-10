<?php 
    $part_payment_allowed = 0;
    
    if (!in_array($dis_programme_subcategory_id, array(884)) || in_array($dis_programme_subcategory_id, $allow_semp_for_instalment_payment)) { // This second condition is to allow instalment payment for SEMP.
        // Only courses that are more than 2 months long are eligible for part payment.
        if (preg_match('/(month|year)/i', $dis_programme_instalment_duration)) {
            $no_months = 0;
            preg_match_all('/^\d+/', $dis_programme_instalment_duration, $matches); 

            if (preg_match('/year/i', $dis_programme_instalment_duration)) { // Get the number of months
                $no_months = (int)$matches[0][0] * 12;
            }
            elseif (preg_match('/month/i', $dis_programme_instalment_duration)) { // Get the number of months
                $no_months = (int)$matches[0][0];
            }
            
            // If the programme is more than two months then part payment is allowed
            if ($no_months > 2) {
                // Part payment should be two thirds of the total months (i.e payments must be completed before end of programme)
                //$part_payment_allowed = floor($no_months * (2/3)); 
                if ($dis_country_programme_max_instalments) { // Use the max number of instalments value that has been set for the country if there's one.
                    $part_payment_allowed = $dis_country_programme_max_instalments;
                } elseif ($dis_programme_max_instalments > 1) { // Use max number of duration if it's greater than 1.
                    $part_payment_allowed = $dis_programme_max_instalments;
                }
                else { // Use to the number of months from the instalment duration.
                    $part_payment_allowed = $no_months;
                }

                // Initialize variable to store details of available instalments
                $instalment_months = array();
                        
                // Get the possible number of instalments in the span of the instalment duration
                for ($x = $part_payment_allowed - 1; $x < $part_payment_allowed && $x > 0; $x = $x - 1) {
                    // If divisible without remainder 
                    if ($part_payment_allowed % $x == 0) { // If divisible without remainder 
                        $instalment_months[$x] = floor($part_payment_allowed/$x);

                        // Re-calculate the increment percentage for each instalment
                        $instalmental_fee_percentage_increase = get_increment_percentage($instalment_months[$x]);
                    }
                }
            }
        }
    }

    // If part payment is allowed
    if ($part_payment_allowed) {
        $profile_student_currency = $coresite_curr_currency; // This should be GBP by default
        $profile_student_amount = $dis_programme_fee;

        if ($cosmetics_dis_programme_original_fee) {
            $profile_student_amount_incremented = $cosmetics_dis_programme_original_fee;
        }
        else {
            $profile_student_amount_incremented = round($profile_student_amount + ($profile_student_amount * $instalmental_fee_percentage_increase), 2); // Increase by 20 percent
        }

        $profile_monthly_amount = round($profile_student_amount_incremented / $part_payment_allowed, 2);

        if ($use_incremented_amount_saved) {
            $profile_student_amount_saved = round($profile_student_amount_incremented - $profile_student_amount, 2); 
        }
        else {
            if ($cosmetics_dis_programme_original_fee) {
                $profile_student_amount_saved = $cosmetics_dis_programme_original_fee - $dis_programme_fee;
            }  
            else {
                $profile_student_amount_saved = $dis_programme_original_fee - $dis_programme_fee; 
            }
        }

        // Just keeping this as an untouched value for use in calculating difference between each incremented instalment amount
        $overall_amount_incremented = $profile_student_amount_incremented;
    }