<?php 
    // Basic information
    $dis_programme_id = $row_DisProg['programme_id'] ? $row_DisProg['programme_id'] : '0';
    $dis_programme_name = $row_DisProg['programme_name'] ? $row_DisProg['programme_name'] : '';
    $dis_programme_url = $row_DisProg['url_name'] ? $row_DisProg['url_name'] : '';
    $dis_programme_excerpt = $row_DisProg['programme_excerpt'] ? $row_DisProg['programme_excerpt'] : '';
    $dis_programme_thumbnail = $row_DisProg['image_url'] ? $row_DisProg['image_url'] : '';
    $dis_programme_banner_image = $row_DisProg['banner_image_url'] ? $row_DisProg['banner_image_url'] : '';
    $dis_programme_category = strtolower(get_programme_relationship($dis_programme_id, 'programme category'));
    $dis_programme_subcategory_id = $row_DisProg['subcategory_id'];
    $dis_programme_parent_category_id = $row_DisProg['parent_category_id'];
    $dis_programme_type = strtolower(get_programme_relationship($dis_programme_id, 'programme type'));
    $dis_programme_pre_url = $dis_programme_category == 'training' ? 'training' : 'programme';

    $dis_programme_duration = get_programme_relationship($dis_programme_id, 'programme duration');
    $dis_programme_instalment_duration = get_programme_relationship($dis_programme_id, 'programme instalment duration');
    $dis_programme_max_instalments =  $row_DisProg['max_instalments'];
    $dis_country_programme_max_instalments =  $row_DisProg['country_max_instalment'];

    $da_prog_has_instalment = false;
    
    if ($dis_country_programme_max_instalments) { // Use the max number of instalments value that has been set for the country if there's one.
        $dis_programme_instalment_months_as_number = $dis_country_programme_max_instalments;
        $dis_programme_increment_percentage = get_increment_percentage($dis_programme_instalment_months_as_number);
        $da_prog_has_instalment = true;
    }
    elseif ($dis_programme_max_instalments > 1) {
        $dis_programme_instalment_months_as_number = $dis_programme_max_instalments;
        $dis_programme_increment_percentage = get_increment_percentage($dis_programme_instalment_months_as_number);
        $da_prog_has_instalment = true;
    } else {
        $dis_programme_instalment_months_as_number = $dis_programme_instalment_duration ? extract_number_of_months($dis_programme_instalment_duration) : extract_number_of_months($dis_programme_duration);
        $dis_programme_increment_percentage = $dis_programme_instalment_months_as_number ? get_increment_percentage($dis_programme_instalment_months_as_number) : 0.80;

        if (in_array($dis_programme_subcategory_id, array(884))) { // No instalment for SEMP, so their increment percentage should be 80%.
            if (!in_array($dis_programme_subcategory_id, $allow_semp_for_instalment_payment)) { // If part payment for SEMP is disabled.
                $dis_programme_increment_percentage = 0.80; // SEMP Online now has instalment payments
            }
        }

        if ($dis_programme_instalment_months_as_number > 1) {
            $da_prog_has_instalment = true;
        }
    }
    
    $dis_programme_effort = get_programme_relationship($dis_programme_id, 'programme effort');
    $dis_programme_format = get_programme_relationship($dis_programme_id, 'programme format');
    $array_of_dis_programme_partner_schools = get_programme_partner_schools($dis_programme_id);

    $is_Girne_American_University = $is_european_masters_university = $is_IU_university = $is_gloucester_university = false;

    $dis_programme_partner_school_logo = $dis_programme_partner_school_slug = "";
    $school_is_masked = false;
    $first_partner_school_logo = $second_partner_school_logo = $first_partner_school_name = $second_partner_school_name = $first_partner_school_slug = $second_partner_school_slug = "";
    $join_both_schools = $double_partner_names = "";
    $display_max_of_two_logos = true;
    $display_first_school = $display_second_school = false;

    if (is_array($array_of_dis_programme_partner_schools) && count($array_of_dis_programme_partner_schools)) {
        $programme_partner_schools = $array_of_dis_programme_partner_schools;

        if (!isset($is_partner_university_page) && $display_max_of_two_logos && (count($array_of_dis_programme_partner_schools) > 1)) { // If multiple schools were chosen for a programme.
            $array_of_double_schools = array();
            $join_both_schools = " and ";

            foreach ($array_of_dis_programme_partner_schools as $key => $value) {
                $array_of_double_schools[][$key] = $value;
            }

            $first_partner_school = $array_of_double_schools[0];
            $second_partner_school = $array_of_double_schools[1];

            $first_partner_school_key = array_key_first($first_partner_school);
            $second_partner_school_key = array_key_first($second_partner_school);

            if ($first_partner_school_key) { // Get details of the first school.
                if ($first_partner_school_key == 12) {
                    $is_IU_university = true;
                }
                elseif ($first_partner_school_key == 11) {
                    $is_gloucester_university = true;
                }

                $da_school_info_from_db = get_partner_school_info($first_partner_school_key, ['partner_name', 'logo', 'is_masked', 'masked_name', 'slug']);

                if (is_array($da_school_info_from_db) && count($da_school_info_from_db)) {
                    if ($da_school_info_from_db['is_masked']) {
                        $school_is_masked = true;
                    }

                    if ($da_school_info_from_db['is_masked'] && $da_school_info_from_db['masked_name']) { // Use the masked name if there's one and school is masked.
                        $first_partner_school_name = $da_school_info_from_db['masked_name'];
                    }
                    else { // Use the original name
                        $first_partner_school_name = $da_school_info_from_db['partner_name'];
                    }

                    if (!preg_match('/lrea/i', $first_partner_school_name)) {
                        $display_first_school = true;
                    }

                    if ($da_school_info_from_db['slug'] && !$school_is_masked) {
                        $first_partner_school_slug = $da_school_info_from_db['slug'];
                    }

                    // Prepare school logo
                    $da_school_logo_from_db = $da_school_info_from_db['logo'];

                    if (!$da_school_info_from_db['is_masked'] && $da_school_logo_from_db) { // Do not display the logo if school is masked.
                        if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                            $first_partner_school_logo = ROOT_DIR_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                        }
                        elseif (file_exists('uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                            $first_partner_school_logo = ROOT_DIR.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                        }
                    }
                }
            }

            if ($second_partner_school_key) { // Get details of the first school.
                if ($second_partner_school_key == 12) {
                    $is_IU_university = true;
                }
                elseif ($second_partner_school_key == 11) {
                    $is_gloucester_university = true;
                }

                $da_school_info_from_db = get_partner_school_info($second_partner_school_key, ['partner_name', 'logo', 'is_masked', 'masked_name', 'slug']);

                if (is_array($da_school_info_from_db) && count($da_school_info_from_db)) {
                    if ($da_school_info_from_db['is_masked']) {
                        $school_is_masked = true;
                    }

                    if ($da_school_info_from_db['is_masked'] && $da_school_info_from_db['masked_name']) { // Use the masked name if there's one.
                        $second_partner_school_name = $da_school_info_from_db['masked_name'];
                    }
                    else {
                        $second_partner_school_name = $da_school_info_from_db['partner_name'];
                    }

                    if (!preg_match('/lrea/i', $second_partner_school_name)) {
                        $display_second_school = true;
                    }

                    if ($da_school_info_from_db['slug'] && !$school_is_masked) {
                        $second_partner_school_slug = $da_school_info_from_db['slug'];
                    }

                    // Prepare school logo
                    $da_school_logo_from_db = $da_school_info_from_db['logo'];

                    if (!$da_school_info_from_db['is_masked'] && $da_school_logo_from_db) { // Do not display the logo if school is masked.
                        if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                            $second_partner_school_logo = ROOT_DIR_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                        }
                        elseif (file_exists('uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                            $second_partner_school_logo = ROOT_DIR.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                        }
                    }
                }
            }

            if ($display_first_school && $display_second_school && $first_partner_school_name && $second_partner_school_name) { // Use both school names if none of them is MSBM.
                $double_partner_names = $first_partner_school_name.$join_both_schools.$second_partner_school_name; 
            }
            elseif ($display_first_school && !$display_second_school && $first_partner_school_name && $second_partner_school_name) { // Use the first school name if the second school is MSBM.
                $double_partner_names = $first_partner_school_name;
            }
            elseif (!$display_first_school && $display_second_school && $first_partner_school_name && $second_partner_school_name) { // Use the second school name if the first school is MSBM.
                $double_partner_names = $second_partner_school_name;
            }
        }
        else { // Only one school is chosen for a programme
            shuffle($programme_partner_schools);

            $dis_programme_partner_school = $programme_partner_schools[0];

            if (!isset($is_partner_university_page)) { // Display partner school logo if it exists. (For other pages except the partner school page)
                $da_school_key_in_array = array_search($dis_programme_partner_school, $array_of_dis_programme_partner_schools); // The key is the ID of the partner school.
    
                if ($da_school_key_in_array) {
                    if ($da_school_key_in_array == 12) {
                        $is_IU_university = true;
                    }
                    elseif ($da_school_key_in_array == 11) {
                        $is_gloucester_university = true;
                    }
    
                    $da_school_info_from_db = get_partner_school_info($da_school_key_in_array, ['logo', 'is_masked', 'masked_name', 'slug']);
    
                    if (is_array($da_school_info_from_db) && count($da_school_info_from_db)) {
                        if ($da_school_info_from_db['is_masked']) {
                            $school_is_masked = true;
                        }
    
                        if ($da_school_info_from_db['is_masked'] && $da_school_info_from_db['masked_name']) { // Use the masked name if there's one.
                            $dis_programme_partner_school = $da_school_info_from_db['masked_name'];
                        }
    
                        if ($da_school_info_from_db['slug'] && !$school_is_masked) {
                            $dis_programme_partner_school_slug = $da_school_info_from_db['slug'];
                        }
    
                        // Prepare school logo
                        $da_school_logo_from_db = $da_school_info_from_db['logo'];
    
                        if (!$da_school_info_from_db['is_masked'] && $da_school_logo_from_db) { // Do not display the logo if school is masked.
                            if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                                $dis_programme_partner_school_logo = ROOT_DIR_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                            }
                            elseif (file_exists('uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                                $dis_programme_partner_school_logo = ROOT_DIR.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                            }
                        }
                    }
                }
            }
        }

        foreach ($array_of_dis_programme_partner_schools as $key => $value) {
            if (preg_match('/girne american/i', $value)) {
                $is_Girne_American_University = true;
            }
            elseif (preg_match('/european university/i', $value)) {
                $is_european_masters_university = true;
            }

            // If we are on a partner school page, prevent showing other schools incase multiple schools were chosen for this programme since a random school would have been chosen above.
            if (isset($da_partner_university_ID) && isset($is_partner_university_page) && ($da_partner_university_ID == $key)) {
                $dis_programme_partner_school = $value;

                if ($da_partner_university_ID == 11) {
                    $is_gloucester_university = true;
                }
                elseif ($da_partner_university_ID == 12) {
                    $is_IU_university = true;
                }
                
                $da_school_info_from_db = get_partner_school_info($da_partner_university_ID, ['logo', 'is_masked', 'masked_name', 'slug']);
                
                if (is_array($da_school_info_from_db) && count($da_school_info_from_db)) {
                    if ($da_school_info_from_db['is_masked']) {
                        $school_is_masked = true;
                    }
                    
                    if ($da_school_info_from_db['is_masked'] && $da_school_info_from_db['masked_name']) { // Use the masked name if there's one.
                        $dis_programme_partner_school = $da_school_info_from_db['masked_name'];
                    }

                    if ($da_school_info_from_db['slug'] && !$school_is_masked) {
                        $dis_programme_partner_school_slug = $da_school_info_from_db['slug'];
                    }

                    // Process school logo.
                    $da_school_logo_from_db = $da_school_info_from_db['logo'];

                    if (!$da_school_info_from_db['is_masked'] && $da_school_logo_from_db) { // Do not display the logo if school is masked.
                        if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                            $dis_programme_partner_school_logo = ROOT_DIR_PORTAL.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                        }
                        elseif (file_exists('uploads/partner_school_logo/'.$da_school_logo_from_db)) {
                            $dis_programme_partner_school_logo = ROOT_DIR.'uploads/partner_school_logo/'.$da_school_logo_from_db;
                        }
                    }
                }
                
                break;
            }
        }
    }
    else {
        $dis_programme_partner_school = $row_DisProg['partner_school'];

        if (preg_match('/girne american/i', $dis_programme_partner_school)) {
            $is_Girne_American_University = true;
        }
        elseif (preg_match('/european university/i', $dis_programme_partner_school)) {
            $is_european_masters_university = true;
        }
    }

    // Doctorates
    if (in_array($dis_programme_subcategory_id, array(405))) { 
        if (!$is_Girne_American_University) { // If partner school is not GAU
            if (preg_match('/\(dba\)/i', $dis_programme_name)) {
                $is_International_American_University = true;
            }
            else {
                $is_Girne_American_University = true;
            }
        }
    }

    if ($row_DisProg['country_image']) {
        $dis_programme_thumbnail = $dis_programme_banner_image = $row_DisProg['country_image'];
    }

    // Prepare the URL for the link
    $dis_programme_a_href = $dis_programme_pre_url.'/'.$dis_programme_url;
    if ($dis_programme_category == 'training' && $row_DisProg['prog_date_id']) {
        $dis_programme_a_href .= '?dt='.$row_DisProg['prog_date_id'];
    }
    
    if (!$coresite_curr_currency || !$todays_conversion_rate) {
        $coresite_curr_currency = !$coresite_curr_currency ? PREFERRED_CURRENCY : $coresite_curr_currency;
        
        if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'inc.currency-processing.php')) {
            require(ROOT_PATH_TO_PORTAL.'inc.currency-processing.php');
        }
        else {
            require('inc.currency-processing.php');
        }
    }
    $dis_programme_currency = $coresite_curr_currency;

    // if the currency was not set previously, then perform the check and set the variable to a value
    if ($dis_programme_currency && !$symbol_for_programme_currency) {
        $symbol_for_programme_currency = get_passed_currency_symbol($dis_programme_currency);
    }
    
    $dis_programme_fee = $row_DisProg['discounted_fee'] ? round($row_DisProg['discounted_fee'] * $todays_conversion_rate, 2) : '';
    $dis_programme_original_fee = $dis_programme_increment_percentage ? round($dis_programme_fee + ($dis_programme_fee * $dis_programme_increment_percentage), 2) : round($dis_programme_fee + ($dis_programme_fee * 0.80), 2);
    $dis_programme_monthly_fee = $row_DisProg['monthly_instalment'] ? round($row_DisProg['monthly_instalment'] * $todays_conversion_rate, 2) : '';

    $dis_programme_video_url = $row_DisProg['video_url'] ? $row_DisProg['video_url'] : '';

    $cosmetics_dis_programme_original_fee = $cosmetics_dis_programme_fee = 0;

    if ($scholarship_cosmetics_is_on && (!in_array($dis_programme_subcategory_id, array(883, 409, 884, 1253, 885)) || in_array($dis_programme_subcategory_id, $allow_semp_for_instalment_payment))) { // Excluding the short courses and trainings.
        $cosmetics_dis_programme_original_fee = $dis_programme_fee * 2; // 200% (TODO:: MAKE THIS ASPECT COME FROM THE DATABASE
        $cosmetics_dis_programme_fee = $dis_programme_original_fee;
    }

    // Process the thumbnail
    $fallback_thumbnails = array(
        ROOT_DIR.$fileCacheVersion.'-images/MA-Human-Resource-Management-.png', 
        ROOT_DIR.$fileCacheVersion.'-images/Strategy.jpg', 
        ROOT_DIR.$fileCacheVersion.'-images/'.rawurlencode('Master of Business Administration.png'), 
        ROOT_DIR.$fileCacheVersion.'-images/'.rawurlencode('MA in Marketing & Innovation-2.png'), 
        ROOT_DIR.$fileCacheVersion.'-images/bg2.jpg', 
    );
    $current_fallback_image_id = $current_fallback_image_id ? $current_fallback_image_id : 0; // Start the count if not started yet
    if ($dis_programme_thumbnail) {
        if ($site_version == "localhost") {
            if (file_exists('../'.PORTAL_FOLDER.'/uploads/course_image/'.$dis_programme_thumbnail)) {
                $dis_programme_thumbnail = ROOT_DIR_PORTAL.'uploads/course_image/'.$dis_programme_thumbnail;
            }
            else {
                $dis_programme_thumbnail = $fallback_thumbnails[$current_fallback_image_id];
                $current_fallback_image_id++;
            }
        }
        else {
            if (file_exists('../'.PORTAL_FOLDER.'/uploads/course_image/'.$dis_programme_thumbnail)) {
                $dis_programme_thumbnail = ROOT_DIR_PORTAL.'uploads/course_image/'.$dis_programme_thumbnail;
            }
            elseif (file_exists(PORTAL_FOLDER.'/uploads/course_image/'.$dis_programme_thumbnail)) {
                $dis_programme_thumbnail = ROOT_DIR_PORTAL.'uploads/course_image/'.$dis_programme_thumbnail;
            }
            else {
                $dis_programme_thumbnail = $fallback_thumbnails[$current_fallback_image_id];
                $current_fallback_image_id++;
            }
        }
    }
    else {
        $dis_programme_thumbnail = $fallback_thumbnails[$current_fallback_image_id];
        $current_fallback_image_id++;
    }
    $current_fallback_image_id = $current_fallback_image_id >= count($fallback_thumbnails) ? 0 : $current_fallback_image_id; // Reset the count if more than available images
    $lazy_placeholder = ROOT_DIR.$fileCacheVersion.'-images/img-placeholder-2.jpg';
    // End Process the thumbnail

    // Process the youtube video
    if ($dis_programme_video_url) {
        // Is it a vimeo link
        if (preg_match("/vimeo\.com/i", $dis_programme_video_url)) {
            // Do nothing for now. But later, we might need to add some validation to make sure that the embed link is properly formatted.

        }
        else {
            preg_match('/watch\?[a-z]=([a-z0-9_-]+)/i', $dis_programme_video_url, $matches);

            if ($matches[1]) {
                $dis_programme_video_url = "https://www.youtube.com/embed/".$matches[1]."?rel=0";
            }
            else $dis_programme_video_url = "https://www.youtube.com/embed/VMHzfubKAiA?rel=0";
        }
    }
    else $dis_programme_video_url = "https://www.youtube.com/embed/VMHzfubKAiA?rel=0";
    // End process the youtube video

    // Process the banner image
    if ($dis_programme_banner_image) {
        if ($site_version == "localhost") {
            if (file_exists('../'.PORTAL_FOLDER.'/uploads/course_image/'.$dis_programme_banner_image)) {
                $dis_programme_banner_image = ROOT_DIR_PORTAL.'uploads/course_image/'.$dis_programme_banner_image;
            }
            else {
                $dis_programme_banner_image = '';
            }
        }
        else {
            if (file_exists('../'.PORTAL_FOLDER.'/uploads/course_image/'.$dis_programme_banner_image)) {
                $dis_programme_banner_image = ROOT_DIR_PORTAL.'uploads/course_image/'.$dis_programme_banner_image;
            }
            elseif (file_exists(PORTAL_FOLDER.'/uploads/course_image/'.$dis_programme_banner_image)) {
                $dis_programme_banner_image = ROOT_DIR_PORTAL.'uploads/course_image/'.$dis_programme_banner_image;
            }
            else {
                $dis_programme_banner_image = '';
            }
        }
    }
    // End process the banner image

    // Extra information
    $dis_programme_overview = get_programme_info($dis_programme_id, 'overview');
    $dis_programme_modules = get_programme_info($dis_programme_id, 'modules');
    $dis_programme_accreditation = get_programme_info($dis_programme_id, 'accreditation');
    $dis_programme_uk_certificate = get_programme_info($dis_programme_id, 'uk_university_certificate');
    $dis_programme_entry_requirements = get_programme_info($dis_programme_id, 'entry_requirements');
    $dis_programme_workshops = get_programme_info($dis_programme_id, 'workshops');
    $dis_programme_visa_requirements = get_programme_info($dis_programme_id, 'visa_requirements');
    $dis_programme_course_details = get_programme_info($dis_programme_id, 'course_details');
    $dis_programme_university_progression = get_programme_info($dis_programme_id, 'university_progression');

    // Process training dates
    if ($dis_programme_category == 'training') { 
        $dis_programme_date_range = '';

        $array_of_dis_programme_take_aways = get_training_programme_take_aways($dis_programme_id);
        
        // Make sure there's a date before continuing
        if ($row_DisProg['start_date']) { 
            $dis_programme_start_month = $row_DisProg['start_date'] ? date('M', strtotime($row_DisProg['start_date'])) : '';
            $dis_programme_end_month = $row_DisProg['end_date'] ? date('M', strtotime($row_DisProg['end_date'])) : '';
    
            $dis_programme_start_year = $row_DisProg['start_date'] ? date('Y', strtotime($row_DisProg['start_date'])) : '';
            $dis_programme_end_year = $row_DisProg['end_date'] ? date('Y', strtotime($row_DisProg['end_date'])) : '';
    
            if ($dis_programme_start_month == $dis_programme_end_month && $dis_programme_start_year == $dis_programme_end_year) { // If same month and year
                $dis_programme_date_range = $dis_programme_start_month.' '.date('d', strtotime($row_DisProg['start_date'])).' - '.date('d', strtotime($row_DisProg['end_date'])).', '.date('Y', strtotime($row_DisProg['end_date']));
            }
            elseif ($dis_programme_start_month != $dis_programme_end_month && $dis_programme_start_year == $dis_programme_end_year) { // If different month but same year
                $dis_programme_date_range = $dis_programme_start_month.' '.date('d', strtotime($row_DisProg['start_date'])).' - '.$dis_programme_end_month.' '.date('d', strtotime($row_DisProg['end_date'])).', '.date('Y', strtotime($row_DisProg['end_date']));
            }
            elseif ($dis_programme_start_month != $dis_programme_end_month && $dis_programme_start_year == $dis_programme_end_year) { // If different year
                $dis_programme_date_range = $dis_programme_start_month.' '.date('d', strtotime($row_DisProg['start_date'])).', '.date('Y', strtotime($row_DisProg['start_date'])).' - '.$dis_programme_end_month.' '.date('d', strtotime($row_DisProg['end_date'])).', '.date('Y', strtotime($row_DisProg['end_date']));
            }

            // Determine whether to add link or not to the box
            if ($row_DisProg['start_date'] >= date("Y-m-d")) { 
                $not_linked_box = false;
            }
            else {
                $not_linked_box = true;
            }
        }
    }

    // Old programme information
    $dis_old_prog_name = get_old_programme_info($dis_programme_id, 'option_value');
    $dis_old_prog_id = get_old_programme_info($dis_programme_id, 'option_id');

    // Subscription information
    if ($totalRows_DisProg == 1) { // Make sure not to execute the function too many times which can slow down the script
        $programme_covered_by_subscription = is_student_and_has_programme_subscription($dis_programme_id);
        $has_already_subscribed = has_already_subscribed($sess_user_id, $dis_old_prog_id);

        if ($has_already_subscribed) {
            $course_ecomm_link_action_url = ROOT_DIR_PORTAL.'courses/list';
            $course_ecomm_link_action_text = 'Continue in Portal';
        }
        else {
            $course_ecomm_link_action_url = ROOT_DIR.'subscription-enrol/'.$dis_programme_url;
            $course_ecomm_link_action_text = 'Start Learning Now';
        }
    }