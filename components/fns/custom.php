<?php 

// DO NOT REMOVE OR EDIT THE LINE BELOW
// REFID: #iAmTheMainWebsite
// DO NOT REMOVE OR EDIT THE LINE ABOVE

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!defined('MEDIA_ROOT_DIR')) {
    if ($site_version == 'localhost') {
        define("MEDIA_ROOT_DIR", "http://".$_SERVER["HTTP_HOST"]."/lrea/");
    }
    else {
        define("MEDIA_ROOT_DIR", "https://lrea.co.uk/");
    }
}

$enable_free_courses = true;
$useNewRegistrationPage = true;
$useNewSocialsLogin = false;

if (SITE_NAME_LOWER == "lrea.edu.ng" || SITE_NAME_LOWER == "lrea.ae" || SITE_NAME_LOWER == "lrea.co.uk" || SITE_NAME_LOWER == "lrea.co.uk" || $site_version == 'localhost') {
    $enable_captcha_here = true;
}

// These are branches that we should not collect payment on behalf of to avoid conflict
// Array key is stateID in myip_ms_states and is also myip_state in branches
// Array value is url_name in myip_ms_states
$states_to_avoid_payment = array(
    // 988 => 'abuja',
    // 234 => 'sharjah'
);

$instalmental_fee_percentage_increase = 0.70;

$api_publisher_Earning_Percentage = 50;

define("PUBLISHER_INCREMENT", 994405952);
define("COURSE_INCREMENT", 59505);

$array_api_categories = array(
	'PCC' => array(
		'name' => "Professional Certificate Courses",
		'display_type' => 'short course'
	),
	'APC' => array(
		'name' => "Advanced Professional Certificate Courses",
		'display_type' => 'advanced professional'
	),
	'SEM' => array(
		'name' => "Online SEMP",
		'display_type' => 'semp online'
	),
	/*'MBA' => array(
		'name' => "Master's Degree",
		'display_type' => 'masters'
	),
	'DIP' => array(
		'name' => "International Postgraduate Diploma",
		'display_type' => 'intl diploma'
	),
	'BAC' => array(
		'name' => "Bachelor's Degree",
		'display_type' => 'bachelors'
	)*/
);  

$array_of_available_currency_symbols = array(
    "NGN" => "₦", 
    "USD" => "$", 
    "GBP" => "£", 
);

if (SITE_NAME_LOWER == "lrea.edu.ng") { // Nigeria 
    $conversion_api_tokens_and_pixels = array(
        'Nigeria' => array(
            'pixel' => '',
            'token' => ''
        ),
    );
} elseif (SITE_NAME_LOWER == "lrea.ae") { // UAE 
    $conversion_api_tokens_and_pixels = array(
        'United Arab Emirate' => array(
            'pixel' => '',
            'token' => ''
        ),
    );
} else { // UK and others
    $conversion_api_tokens_and_pixels = array(
        'United Kingdom' => array(
            'pixel' => '',
            'token' => ''
        ),
    );
} 

$flutterwave_acceptable_currencies = array(
    'BIF', 'CAD', 'CDF', 'CVE', 'EUR', 'GBP', 'GHS', 'GMD', 'GNF', 'KES', 'LRD', 'MWK', 
    'MZN', 'NGN', 'RWF', 'SLL', 'STD', 'TZS', 'UGX', 'USD', 'XAF', 'XOF', 'ZMK', 'ZMW', 'ZWD'
);

$stripe_acceptable_currencies = array(
    'USD', 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 
    'BGN', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', 'CHF', 'CNY', 'COP', 'CRC', 
    'CVE', 'CZK', 'DKK', 'DOP', 'DZD', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 
    'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'KES', 'KGS', 
    'KHR', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MKD', 'MMK', 'MNT', 'MOP', 
    'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 
    'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'QAR', 'RON', 'RSD', 'RUB', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 
    'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 
    'UYU', 'UZS', 'WST', 'XCD', 'YER', 'ZAR', 'ZMW'
);

$cellulant_acceptable_currencies = array(
    'USD', 'AOA', 'XOF', 'BWP', 'XOF', 'BIF', 'XAF', 'CVE', 'XAF', 'CDF', 'GMD', 'GHS', 'GNF', 'KES',
    'LRD', 'MWK', 'MZN', 'NGN', 'RWF', 'STD', 'SLL', 'SSP', 'UGX', 'TZS', 'ZMW'
);

$allow_other_facebook_conversion_event_tracking = true;

$google_app_url = "";
$apple_app_url = "";

// Every currencies in this array does not need formating i.e The amount is used as is.
$checkout_dot_com_currencies_that_accept_full_amount_format = array(
    'BIF', 'CLF', 'DJF', 'GNF', 'ISK', 'JPY', 'KMF', 'KRW', 'PYG', 'RWF', 'UGX', 'VUV', 'VND', 'XAF', 'XOF', 'XPF'
);

// Currencies that warrants multiplying the amount by 1000. (Every Other currencies that are not in this array must be multiplies by 100).
$checkout_dot_com_1000_format_currencies = array(
    'BHD', 'IQD', 'JOD', 'KWD', 'LYD', 'OMR', 'TND'
);

// Use this to flip between the old and new design of the programme box.
$use_the_new_programme_box_design = true;

$enable_data_layer_scripts = true;
$enable_tradedoubler_scripts = false;

define("TIKTOK_PIXEL", "");
define("TIKTOK_ACCESS_TOKEN", "");

if ($site_version == "online") {    
    $enable_facebook_capi_events_tracking = true;
    $debug_facebook_capi_script = false;
    $enable_tiktok_events_tracking = true;
    $debug_tiktok_script = false;
}
else {
    $enable_facebook_capi_events_tracking = true; // Set this to true if we want to run the capi codes on localhost.
    $debug_facebook_capi_script = true; // It prevents the generated capi scripts from running by turning them to comments so that we can view the contents.
    $enable_tiktok_events_tracking = true;
    $debug_tiktok_script = true;
}

$enable_prog_certificate_extra_fees = true;
$enable_othm_certificate_extra_fee = true;
$display_single_extra_fees_checkbox = true;

if ($display_single_extra_fees_checkbox) {
    $certificate_extra_fees = array(
        "fee" => 82.50,
        "currency" => "GBP"
    );
} 
else {
    $certificate_extra_fees = array(
        "printing_fee" => 41.25,
        "shipping_fee" => 41.25,
        "currency" => "GBP"
    );
}

$certificate_extra_fees['othm_cert_price'] = '25';
$certificate_extra_fees['othm_cert_currency'] = 'GBP';

$category_of_programmes_to_add_extra_fees = array('short course');

$enable_cert_attestation = true;
$certificate_attestation_fees = array(
    "fee" => 1500,
    "currency" => "AED"
);

$addToCartCookieDuration = time() + (3600 * 24 * 3); // 3 days (This variable was moved here from config.php file because it is needed in the student-profile.php file on portal.)

$malicious_keywords_to_lookout_for = array("drive.google"); // This variable is being used by the contact us page in msbm_blog as well.

/*
    Regulated = OfQUAL + OTHM
    Endorsed (Starts with "International" and contains "Diploma") = OTHM
*/

$new_course_category_ids = array(
    "Postgraduate Programme" => array(
            "id" => 876, 
            "subcategories" => array(
                    "Doctorate Degree" => 405, 
                    "Master's Degree" => 406, 
                    "Master's Top-Up" => 901, 
                    "Postgraduate Diploma (Regulated)" => 408, 
                    "International Postgraduate Diploma (Endorsed)" => 880, 
                    "Postgraduate Research Diploma" => 1373,
                )
            ),
    "Undergraduate Programme" => array(
            "id" => 877, 
            "subcategories" => array(
                    "Bachelor's Degree" => 407, 
                    "Diploma (Regulated)" => 881, 
                    "International Higher Diploma (Endorsed)" => 882, 
                )
            ),
    "Executive Education Programme" => array(
            "id" => 878, 
            "subcategories" => array(
                    "Advanced Professional Certificate Course" => 883, 
                    "Professional Certificate Course" => 409, 
                    "Special Executive Masters Programme (SEMP Online)" => 884, 
                    "Free Online Course" => 617, 
                    "Professional Certificate Course (PCC Classroom)" => 1253,
                )
            ),
    "Corporate Training" => array(
            "id" => 879, 
            "subcategories" => array(
                    "Special Executive Masters Programme (SEMP Classroom)" => 885, 
                )
            ),
);

$root_img_domain = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;
$pcc_sample_certificate_file_name = $root_img_domain."images/banners/PPC-sample-certificate-newly-updated-v3.jpg";
$apcc_sample_certificate_file_name = $root_img_domain."images/banners/Advanced-Certificate-Frame-2-newly-updated-v3.jpg";
$semp_sample_certificate_file_name = $root_img_domain."images/banners/SEMP-Online-Sample-Certificate-newly-updated-v3.jpg";
$diploma_sample_certificate_file_name = $root_img_domain."images/banners/Level-7-IPGD-sample-certificate.jpg";
$free_course_sample_certificate_file_name = $root_img_domain."images/banners/Free-Certificate-Sample-newly-updated-v3.jpg";

define("BIZ_ACCOUNT_REQUEST_ID_MASK", 3873200);
define("BIZ_ACCOUNT_TARGET_COURSE_MASK", 483200);

$scholarship_cosmetics_is_on = false;

$show_partners_info_on_the_website = true;

$limit_duration_gap_of_instalment_payments = true;
$gap_between_instalment_payments = 3; // 3 months

$allow_semp_for_instalment_payment = array(0); // If we want to exclude semp from instalment payment, change the value to zero.

$fallback_thumbnails = array(
    MEDIA_ROOT_DIR.$fileCacheVersion.'-images/MA-Human-Resource-Management-.png', 
    MEDIA_ROOT_DIR.$fileCacheVersion.'-images/Strategy.jpg', 
    MEDIA_ROOT_DIR.$fileCacheVersion.'-images/'.rawurlencode('Master of Business Administration.png'), 
    MEDIA_ROOT_DIR.$fileCacheVersion.'-images/'.rawurlencode('MA in Marketing & Innovation-2.png'), 
    MEDIA_ROOT_DIR.$fileCacheVersion.'-images/bg2.jpg', 
);

if (!function_exists("our_mysqli_real_escape_string")) {
	function our_mysqli_real_escape_string($theValue) {
		global $config;
	
		$final_value = $theValue;
	
		if (function_exists("mysqli_real_escape_string")) {
			$final_value = mysqli_real_escape_string($config, $theValue);
		}
		else {
			$final_value = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		}
	
		return $final_value;
	}
}

if (!function_exists('is_country')) { /* Check_for "is_country" */
function is_country($slug, $column='countryName') {
	global $database_config, $config;
    
    // Cache check start
    $cache_session_id = 'router_is_country';
    $target_key = md5($slug.$column);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT * 
                      FROM myip_ms_countries 
                      WHERE url_name = '".sql_string($slug)."' 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions[$column]);

		return $row_Actions[$column];
	}
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, false);
        return false;
    }
}
} /* End_check for "is_country" */

if (!function_exists('is_state')) { /* Check_for "is_state" */
function is_state($slug, $column='stateName') {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'router_is_state';
    $target_key = md5($slug.$column);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT * 
                      FROM myip_ms_states 
                      WHERE url_name = '".sql_string($slug)."' 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions[$column]);

		return $row_Actions[$column];
	}
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, false);
        return false;
    }
}
} /* End_check for "is_state" */

if (!function_exists('get_state_country')) { /* Check_for "get_state_country" */
function get_state_country($slug, $column) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'router_state_country';
    $target_key = md5($slug.$column);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT mmctry.* 
                      FROM myip_ms_states mmst 
                      JOIN myip_ms_countries mmctry ON mmst.countryID = mmctry.countryID 
                      WHERE mmst.url_name = '".sql_string($slug)."' 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions[$column]);
		return $row_Actions[$column];
	}
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, false);
        return false;
    }
}
} /* End_check for "get_state_country" */

if (!function_exists('get_curr_branch_and_study_center')) { /* Check_for "get_curr_branch_and_study_center" */
    function get_curr_branch_and_study_center($state_id = '', $country_id = '') {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'get_curr_branch_and_study_center';
        $target_key = md5($cache_session_id.$state_id.$country_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        $final_branch_id = 9; // Online Course
        $final_study_center_id = 138; // UAE
        
        if ($state_id) {
            mysqli_select_db($config, $database_config);
            $query_Actions = "SELECT b.* 
                            FROM branches b 
                            JOIN myip_ms_states state2 ON b.myip_state = state2.stateID
                            WHERE state2.url_name = '".sql_string($state_id)."' 
                            ORDER BY b.branch_id ASC
                            LIMIT 1";
            $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
            $row_Actions = mysqli_fetch_assoc($Actions);
            $totalRows_Actions = mysqli_num_rows($Actions);

            if ($totalRows_Actions) {
                $final_branch_id = $row_Actions['branch_id'];
                $final_study_center_id = $row_Actions['country_id'];
            }
        }
        elseif ($country_id) {
            mysqli_select_db($config, $database_config);
            $query_Actions = "SELECT b.* 
                            FROM branches b 
                            JOIN myip_ms_countries ctry ON b.myip_country = ctry.countryID
                            WHERE ctry.url_name = '".sql_string($country_id)."' 
                            ORDER BY b.branch_id ASC
                            LIMIT 1";
            $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
            $row_Actions = mysqli_fetch_assoc($Actions);
            $totalRows_Actions = mysqli_num_rows($Actions);

            if ($totalRows_Actions) {
                $final_branch_id = $row_Actions['branch_id'];
                $final_study_center_id = $row_Actions['country_id'];
            }
        }

        $arrayOfCalculatedResponse = array(
            'branch' => $final_branch_id,
            'study_center' => $final_study_center_id
        );

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $arrayOfCalculatedResponse);

        return $arrayOfCalculatedResponse;
    }
} /* End_check for "get_curr_branch_and_study_center" */

if (!function_exists('current_location')) { /* Check_for "current_location" */
function current_location($full_location = false) {
	global $coresite_curr_state_name, $coresite_curr_country_name, $coresite_curr_location_name_full;
    
    if ($full_location) {
        return $coresite_curr_location_name_full;
    }
    elseif ($coresite_curr_state_name) {
        return $coresite_curr_state_name;
    }
    else {
        return $coresite_curr_country_name;
    }
}
} /* End_check for "current_location" */

if (!function_exists('country_specific')) { /* Check_for "country_specific" */
function country_specific($string) {
    global $coresite_curr_state_name, $coresite_curr_country_name, $coresite_curr_location_name_full;

    if (preg_match('/{%%state%%}/i', $string) && !$coresite_curr_state_name) {
        return '';
    }

    if (preg_match('/{%%country%%}/i', $string) && !$coresite_curr_country_name) {
        return '';
    }

    if ($coresite_curr_state_name || $coresite_curr_country_name || $coresite_curr_location_name_full) {
        $coresite_curr_state_or_country = $coresite_curr_state_name ? $coresite_curr_state_name : $coresite_curr_country_name;

        $string = preg_replace(
            // What to look for
            array(
                "/{%%state%%}/i", 
                "/{%%country%%}/i", 
                "/{%%state_or_country%%}/i", 
                "/{%%location%%}/i", 
                "/{%%location_full%%}/i"
            ), 
            
            //What to replace with
            array(
                $coresite_curr_state_name, 
                $coresite_curr_country_name, 
                $coresite_curr_state_or_country, 
                current_location(), 
                current_location('full')
            )
        , $string);

        return $string;
    }
    else return '';
}
} /* End_check for "country_specific" */

if (!function_exists('get_programme_relationship')) { /* Check_for "get_programme_relationship" */
function get_programme_relationship($prog_id, $relationship_name, $separator = '/') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT op.*, oprel.option_two 
                    FROM option_relationships oprel 
                    JOIN options op ON oprel.option_two = op.option_id 
                    WHERE oprel.relationship_name = '".sql_string($relationship_name)."'  
                            AND oprel.option_one = ".sql_int($prog_id)." 
                    ORDER BY oprel.relationship_id ASC";//return $query_Actions;
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions == 1) {
		return ucfirst($row_Actions['option_value']);
    }
    elseif ($totalRows_Actions > 1) {
        $final_value = array();
        
        do {
            $final_value[] = ucwords($row_Actions['option_value']);
        } while($row_Actions = mysqli_fetch_assoc($Actions));
        
        return implode($separator, $final_value);
	}
    else return false;
}
} /* End_check for "get_programme_relationship" */

if (!function_exists('get_number_clients_in_portal')) { /* Check_for "get_number_clients_in_portal" */
function get_number_clients_in_portal() {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_number_clients_in_portal';
    $target_key = md5($cache_session_id);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT COUNT(p_id) as how_many 
                      FROM prospects";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);

    // Add to cache
    add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['how_many'], date("Y-m-d H:i:s", strtotime('+90 minutes')));
	
	return $row_Actions['how_many'];
}
} /* End_check for "get_number_clients_in_portal" */

if (!function_exists('get_number_current_students_in_portal')) { /* Check_for "get_number_current_students_in_portal" */
function get_number_current_students_in_portal() {
	global $database_config, $config;
    
    // Cache check start
    $cache_session_id = 'get_number_current_students_in_portal';
    $target_key = md5($cache_session_id);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT COUNT(p_id) as how_many 
                      FROM prospects 
                      WHERE stage IN (14, 21, 23)";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);

    // Add to cache
    add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['how_many']+4000, date("Y-m-d H:i:s", strtotime('+90 minutes')));
	
	return $row_Actions['how_many']+4000;
}
} /* End_check for "get_number_current_students_in_portal" */

if (!function_exists('get_number_new_clients_today_in_portal')) { /* Check_for "get_number_new_clients_today_in_portal" */
function get_number_new_clients_today_in_portal() {
	global $database_config, $config;
    
    // Cache check start
    $cache_session_id = 'get_number_new_clients_today_in_portal';
    $target_key = md5($cache_session_id);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT COUNT(p_id) + HOUR(NOW()) + 23 as how_many 
                      FROM prospects 
                      WHERE stage IN (2, 19) 
                        AND entry_date BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
    // Add to cache
    add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['how_many'], date("Y-m-d H:i:s", strtotime('+90 minutes')));

	return $row_Actions['how_many'];
}
} /* End_check for "get_number_new_clients_today_in_portal" */

if (!function_exists('programme_is_online')) { /* Check_for "programme_is_online" */
function programme_is_online($prog_id) {
	global $database_config, $config;
    
    // Cache check start
    $cache_session_id = 'programme_is_online';
    $target_key = md5($cache_session_id.$prog_id);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT op.*, oprel.option_two 
                    FROM option_relationships oprel 
                    JOIN options op ON oprel.option_two = op.option_id 
                    WHERE oprel.relationship_name = 'programme format'  
                            AND oprel.option_one = ".sql_int($prog_id)." 
                            AND op.option_value = 'online' 
                    ORDER BY oprel.relationship_id ASC";//return $query_Actions;
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, true, date("Y-m-d H:i:s", strtotime("+ 1 hour")));
		return true;
    }
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, false, date("Y-m-d H:i:s", strtotime("+ 1 hour")));
        return false;
    }
}
} /* End_check for "programme_is_online" */

if (!function_exists('get_programme_info')) { /* Check_for "get_programme_info" */
function get_programme_info($prog_id, $alt_option_value) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_programme_info';
    $target_key = md5($prog_id.$alt_option_value);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT op.*, npinfo.info_detail 
                    FROM new_programmes_info npinfo 
                    JOIN options op ON npinfo.option_id = op.option_id 
                    WHERE npinfo.prog_id = ".sql_int($prog_id)."  
                            AND op.option_name = 'programme info' 
                            AND op.alt_option_value = '".sql_string($alt_option_value)."' 
                    LIMIT 1";//return $query_Actions;
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['info_detail'], date("Y-m-d H:i:s", strtotime("+ 1 hour")));

		return $row_Actions['info_detail'];
    }
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, '', date("Y-m-d H:i:s", strtotime("+ 1 hour")));
        return '';
    }
}
} /* End_check for "get_programme_info" */

if (!function_exists('get_country_currency')) { /* Check_for "get_country_currency" */
function get_country_currency($slug) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_country_currency';
    $target_key = md5($cache_session_id.$slug);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT mmctry.api_currency_code 
                    FROM myip_ms_countries mmctry 
                    WHERE mmctry.url_name = '".sql_string($slug)."'
                    LIMIT 1";//return $query_Actions;
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
	if ($row_Actions['api_currency_code']) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['api_currency_code']);
		return $row_Actions['api_currency_code'];
    }
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, PREFERRED_CURRENCY);
        return PREFERRED_CURRENCY;
    }
}
} /* End_check for "get_country_currency" */

if (!function_exists('get_state_currency')) { /* Check_for "get_state_currency" */
function get_state_currency($slug) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_state_currency';
    $target_key = md5($cache_session_id.$slug);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT mmctry.api_currency_code 
                      FROM myip_ms_states mmst 
                      JOIN myip_ms_countries mmctry ON mmst.countryID = mmctry.countryID 
                      WHERE mmst.url_name = '".sql_string($slug)."'
                      LIMIT 1";//return $query_Actions;
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
	if ($row_Actions['api_currency_code']) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['api_currency_code']);
		return $row_Actions['api_currency_code'];
    }
    else {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, PREFERRED_CURRENCY);
        return PREFERRED_CURRENCY;
    }
}
} /* End_check for "get_state_currency" */

if (!function_exists('get_api_conversion_rate')) { /* Check_for "get_api_conversion_rate" */
function get_api_conversion_rate($from_currency, $to_currency) {
    global $database_config, $config, $site_version;

    // Cache check start
    $cache_session_id = 'get_api_conversion_rate';
    $target_key = sha1($from_currency.$to_currency);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    // Check for a manual rate first
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT *
                        FROM manual_rates 
                        WHERE from_currency = '" . sql_string($from_currency) . "' 
                            AND to_currency = '" . sql_string($to_currency) . "' 
                        LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);

    // If there's a manual rate and it is not zero
    if ($totalRows_Actions && $row_Actions['rate'] > 0) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, round($row_Actions['rate']), date("Y-m-d 23:59:59"));

        return round($row_Actions['rate'], 4);
    }

    // Check for a reverse manual rate next
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT *
                        FROM manual_rates 
                        WHERE to_currency = '" . sql_string($from_currency) . "' 
                            AND from_currency = '" . sql_string($to_currency) . "' 
                        LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);

    // If there's a reverse manual rate and it is not zero
    if ($totalRows_Actions && $row_Actions['rate'] > 0) {
        $alternate_multiplier = 1 / $row_Actions['rate'];

        if ($alternate_multiplier > 0) {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, round($alternate_multiplier, date("Y-m-d 23:59:59")));

            return round($alternate_multiplier, 4);
        }
    }
    
    if (defined("CURRENCY_API_ACCESS_KEY") && (get_todays_conversion_rate($to_currency, $from_currency) === false)) {
        // set API Endpoint, access key, required parameters
        $endpoint = 'convert';
        $access_key = CURRENCY_API_ACCESS_KEY;

        $from = $from_currency;
        $to = $to_currency;
        $amount = 1;

        // initialize CURL:
        $ch = curl_init('https://api.currencylayer.com/'.$endpoint.'?access_key='.$access_key.'&from='.$from.'&to='.$to.'&amount='.$amount.'');   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // get the (still encoded) JSON data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $conversionResult = json_decode($json, true); 

        // access the conversion result
        //echo $conversionResult['result'];
    }

    if (my_is_float($conversionResult['result'])) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, round($conversionResult['result'], 10), date("Y-m-d 23:59:59"));

        // Record the value in the database
        store_todays_conversion_rate($from_currency, $to_currency, round($conversionResult['result'], 10));
        
        return round($conversionResult['result'], 10);
    }
    else {
        // Get the value from the database
        $latest_rate = get_last_conversion_rate($to_currency, $from_currency);

        if (my_is_float($latest_rate)) {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, round($latest_rate, 10), date("Y-m-d 23:59:59"));

            return round($latest_rate, 10);
        }
    }

    return false;
}
} /* End_check for "get_api_conversion_rate" */

if (!function_exists('get_todays_conversion_rate')) { /* Check_for "get_todays_conversion_rate" */
function get_todays_conversion_rate($currency_code, $from_currency_code = BASE_CURRENCY) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_todays_conversion_rate';
    $target_key = sha1($currency_code.$from_currency_code);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    // Check if today's rate for this currency exists
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT dr.rate 
                    FROM daily_rates dr 
                    WHERE dr.from_currency = '".sql_string($from_currency_code)."' 
                            AND dr.to_currency = '".sql_string($currency_code)."' 
                            AND dr.date_checked BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."' 
                    ORDER BY dr.date_checked DESC
                    LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
	if ($row_Actions['rate']) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['rate']);
        
		return $row_Actions['rate'];
    }
    else return false;
}
} /* End_check for "get_todays_conversion_rate" */

if (!function_exists('store_todays_conversion_rate')) { /* Check_for "store_todays_conversion_rate" */
function store_todays_conversion_rate($from_currency, $to_currency, $rate) {
	global $database_config, $config;
    
    if (round($rate, 4) > 0) {
        $rate = round($rate, 4);

        if (get_todays_conversion_rate($to_currency, $from_currency) === false) {
            $insertSQL = "INSERT INTO daily_rates (from_currency, to_currency, rate, date_checked) 
                        VALUES (
                            '".sql_string($from_currency)."', 
                            '".sql_string($to_currency)."', 
                            ".sql_float($rate).", 
                            '".sql_string(date("Y-m-d H:i:s"))."' 
                        )";
            mysqli_select_db($config, $database_config);
            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
            $insertStatus = mysqli_affected_rows($config);

            if ($insertStatus >= 0) { // If INSERT is successful
                return true;
            }
            else { // If INSERT failed
                return false;
            } 
        }
        else return true;
    }

    return false;
}
} /* End_check for "store_todays_conversion_rate" */

if (!function_exists('get_last_conversion_rate')) { /* Check_for "get_last_conversion_rate" */
function get_last_conversion_rate($currency_code, $from_currency_code = BASE_CURRENCY) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_last_conversion_rate';
    $target_key = sha1($currency_code.$from_currency_code);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    // Check if past rate for this currency exists
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT dr.rate 
                    FROM daily_rates dr 
                    WHERE dr.from_currency = '".sql_string($from_currency_code)."' 
                            AND dr.to_currency = '".sql_string($currency_code)."' 
                            AND dr.date_checked <= '".sql_string(date("Y-m-d H:i:s"))."' 
                    ORDER BY dr.date_checked DESC 
                    LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
	if ($row_Actions['rate']) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['rate']);
        
		return $row_Actions['rate'];
    }
    else return false;
}
} /* End_check for "get_last_conversion_rate" */

if (!function_exists('get_specific_date_conversion_rate')) { /* Check_for "get_specific_date_conversion_rate" */
function get_specific_date_conversion_rate($from_currency_code, $to_currency_code, $rate_date) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_specific_date_conversion_rate';
    $target_key = sha1($from_currency_code.$to_currency_code.$rate_date);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    // Check if the date's rate for this currency exists
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT dr.rate 
                    FROM daily_rates dr 
                    WHERE dr.from_currency = '".sql_string($from_currency_code)."' 
                            AND dr.to_currency = '".sql_string($to_currency_code)."' 
                            AND dr.date_checked BETWEEN '".$rate_date."00:00:00' AND '".$rate_date."23:59:59' 
                    ORDER BY dr.date_checked ASC 
                    LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
	if ($row_Actions['rate']) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['rate']);
        
		return $row_Actions['rate'];
    }
    else { // Try to reverse-engineer the rate for the specified day
        $initial_currency = BASE_CURRENCY;

        // Check if the date's rate for currency X exists
        mysqli_select_db($config, $database_config);
        $query_ActionA = "SELECT dr.rate 
                        FROM daily_rates dr 
                        WHERE dr.from_currency = '".BASE_CURRENCY."' 
                                AND dr.to_currency = '".sql_string($from_currency_code)."' 
                                AND dr.date_checked BETWEEN '".$rate_date."00:00:00' AND '".$rate_date."23:59:59' 
                        ORDER BY dr.date_checked ASC 
                        LIMIT 1";
        $ActionA = mysqli_query($config, $query_ActionA) or die(mysqli_error($config));
        $row_ActionA = mysqli_fetch_assoc($ActionA);

        $rateA = $row_ActionA['rate'];
        
        // Check if the date's rate for currency Y exists
        mysqli_select_db($config, $database_config);
        $query_ActionB = "SELECT dr.rate 
                        FROM daily_rates dr 
                        WHERE dr.from_currency = 'GBP' 
                                AND dr.to_currency = '".sql_string($to_currency_code)."' 
                                AND dr.date_checked BETWEEN '".$rate_date."00:00:00' AND '".$rate_date."23:59:59' 
                        ORDER BY dr.date_checked ASC 
                        LIMIT 1";
        $ActionB = mysqli_query($config, $query_ActionB) or die(mysqli_error($config));
        $row_ActionB = mysqli_fetch_assoc($ActionB);

        $rateB = $row_ActionB['rate'];
        
        if (!$rateA) {
            if (!$rateA = get_first_ever_conversion_rate($initial_currency, $from_currency_code)) {
                if (!$rateA = get_todays_conversion_rate($from_currency_code, $initial_currency)) {
                    // Get today's rate
                    $api_conversion_rate = get_api_conversion_rate($initial_currency, $from_currency_code); 
            
                    if ($api_conversion_rate) { 
                        store_todays_conversion_rate($initial_currency, $from_currency_code, $api_conversion_rate);
            
                        $rateA = $api_conversion_rate;
                    }
                    else {
                        $rateA = 1.00;
                    }
                }
            }
        }

        if (!$rateB) {
            if (!$rateB = get_first_ever_conversion_rate($initial_currency, $to_currency_code)) {
                if (!$rateB = get_todays_conversion_rate($to_currency_code, $initial_currency)) {
                    // Get today's rate
                    $api_conversion_rate = get_api_conversion_rate($initial_currency, $to_currency_code); 
            
                    if ($api_conversion_rate) { 
                        store_todays_conversion_rate($initial_currency, $to_currency_code, $api_conversion_rate);
            
                        $rateB = $api_conversion_rate;
                    }
                    else {
                        $rateB = 1.00;
                    }
                }
            }
        }

        if ($rateA && $rateB) {
            $rateC = round($rateB / $rateA, 10);

            if ($rateC > 0) {
                // Remember that only 4 decimal place values can be stored in the table
                if (round($rateC, 4) > 0) {
                    $rateC = round($rateC, 4);

                    // Record the rate for next time
                    $insertSQL = "INSERT INTO daily_rates (from_currency, to_currency, rate, date_checked) 
                                VALUES (
                                    '".sql_string($from_currency_code)."', 
                                    '".sql_string($to_currency_code)."', 
                                    ".sql_float($rateC).", 
                                    '".sql_string($rate_date.date(" H:i:s"))."' 
                                )";
                    mysqli_select_db($config, $database_config);
                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                    $insertStatus = mysqli_affected_rows($config);
                }

                // Add to cache
                add_entry_cached_in_memory($cache_session_id, $target_key, $rateC);
                
                return $rateC;
            }
        }
    }
    
    return false;
}
} /* End_check for "get_specific_date_conversion_rate" */

if (!function_exists('get_first_ever_conversion_rate')) { /* Check_for "get_first_ever_conversion_rate" */
function get_first_ever_conversion_rate($from_currency_code, $to_currency_code) {
	global $database_config, $config;

    // Cache check start
    $cache_session_id = 'get_first_ever_conversion_rate';
    $target_key = sha1($from_currency_code.$to_currency_code);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    // Check if the date's rate for this currency exists
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT dr.rate 
                    FROM daily_rates dr 
                    WHERE dr.from_currency = '".sql_string($from_currency_code)."' 
                            AND dr.to_currency = '".sql_string($to_currency_code)."' 
                    ORDER BY dr.date_checked ASC 
                    LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
	
	if ($row_Actions['rate']) {
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['rate']);
        
		return $row_Actions['rate'];
    }
    else return false;
}
} /* End_check for "get_first_ever_conversion_rate" */

if (!function_exists('convert_amount_from_currency')) { /* Check_for "convert_amount_from_currency" */
    function convert_amount_from_currency($initial_currency, $final_currency, $initial_amount, $rate_date = "0000-00-00") {
        if ($initial_currency != $final_currency) {
            if ($rate_date == "0000-00-00") {
                // If today's rates for this currency has not been stored
                if (!$todays_conversion_rate = get_todays_conversion_rate($final_currency, $initial_currency)) {
                    // Get today's rate
                    $api_conversion_rate = get_api_conversion_rate($initial_currency, $final_currency); 
            
                    if ($api_conversion_rate) { 
                        store_todays_conversion_rate($initial_currency, $final_currency, $api_conversion_rate);
            
                        $todays_conversion_rate = $api_conversion_rate;
                    }
                    else { // If request failed, use last known rate
                        $todays_conversion_rate = get_last_conversion_rate($final_currency, $initial_currency);
                    }
                }
            }
            else {
                if (!$specific_date_conversion_rate = get_specific_date_conversion_rate($initial_currency, $final_currency, $rate_date)) {
                    if (!$specific_date_conversion_rate = get_first_ever_conversion_rate($initial_currency, $final_currency)) {
                        if (!$specific_date_conversion_rate = get_todays_conversion_rate($final_currency, $initial_currency)) {
                            // Get today's rate
                            $api_conversion_rate = get_api_conversion_rate($initial_currency, $final_currency); 
                    
                            if ($api_conversion_rate) { 
                                store_todays_conversion_rate($initial_currency, $final_currency, $api_conversion_rate);
                    
                                $specific_date_conversion_rate = $api_conversion_rate;
                            }
                            else {
                                $specific_date_conversion_rate = DEFAULT_BASE_CURRENCY_CONVERSION_RATE;
                            }
                        }
                    }
                }

                $todays_conversion_rate = $specific_date_conversion_rate;
            }
        }
        
        $todays_conversion_rate = $todays_conversion_rate > 0 ? $todays_conversion_rate : DEFAULT_BASE_CURRENCY_CONVERSION_RATE;

        return $initial_amount * $todays_conversion_rate;
    }
} /* End_check for "convert_amount_from_currency" */


if (!function_exists('show_amount')) { /* Check_for "show_amount" */
function show_amount($amount) {
    /*if (preg_match('/^\d+\.\d+$/', $amount)) { // If not round figure
        return number_format($amount, 2);
    }
    else return number_format($amount);*/

    return number_format($amount);
}
} /* End_check for "show_amount" */

if (!function_exists('make_meta_description')) { 
    function make_meta_description($string) {
        // Clean up the string for use
        $stripped_description = html_entity_decode(strip_tags($string));
        $stripped_description = preg_replace(array("/[\s\n\r\t]+/i", "/\s{2,}/i"), array(" ", " "), $stripped_description);
        $trimmed_description = substr(trim($stripped_description), 0, 151);

        // Check if it is long enough to need ellipses
        $length_of_description_after_trimming = mb_strlen($trimmed_description);
        $description_ellipses = $length_of_description_after_trimming == 151 ? "..." : "";

        return $trimmed_description.$description_ellipses;
    }
}

if (!function_exists('compress_page')) { 
    function compress_page($buffer) {
        $search = array(
            "/ +/" => " ",
            "/<!–\{(.*?)\}–>|<!–(.*?)–>|[\t\r\n]|<!–|–>|\/\/ <!–|\/\/ –>|<!\[CDATA\[|\/\/ \]\]>|\]\]>|\/\/\]\]>|\/\/<!\[CDATA\[/" => " ",
            "/ +/" => " ",
        );
        $buffer = preg_replace(array_keys($search), array_values($search), $buffer);
        return compress_styles($buffer);
    }
}

if (!function_exists('compress_styles')) { 
    function compress_styles($buffer) {
        // remove comments, tabs, spaces, newlines, etc.
        $search = array(
            "/\/\*(.*?)\*\/|[\t\r\n]/s" => "",
            "/ +\{ +|\{ +| +\{/" => "{",
            "/ +\} +|\} +| +\}/" => "}",
            "/ +;/" => ";",
            "/ +; +|; +/" => "; "
        );
        $buffer = preg_replace(array_keys($search), array_values($search), $buffer);
        return $buffer;
    }
}

if (!function_exists('can_register_when')) { 
    function can_register_when($prog_id, $month) {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'can_register_when';
        $target_key = md5($prog_id.$month);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end
        
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT op.*, oprel.option_two 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        WHERE oprel.relationship_name = 'programme sessions'  
                                AND oprel.option_one = ".sql_int($prog_id)." 
                                AND op.option_value = '".sql_string($month)."' 
                        ORDER BY oprel.relationship_id ASC 
                        LIMIT 1";//return $query_Actions;
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);
        
        if ($totalRows_Actions) {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, true, date("Y-m-d H:i:s", strtotime("+ 1 hour")));
            return true;
        }
        else {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, false, date("Y-m-d H:i:s", strtotime("+ 1 hour")));
            return false;
        }
    }
}

// These functions below are functions from the Website editor (composer) project.
if (!function_exists('get_page_content')) { 
    function get_page_content($group_id, $limit = true, $expected_no_of_result = 0) { // Get page contents using its group id
        // This function was modified to display contents based on whether it has an image or just texts. Contents which have $limit as "false" have image(s) in them.

        global $database_config, $config, $coresite_curr_state_name, $coresite_curr_state_slug, $coresite_curr_country_name, $coresite_curr_country_slug;

        // Cache check start
        if ($limit) {
            if (!$expected_no_of_result) { // i.e Just one result is expected.
                $cache_session_id = 'get_page_content_v6';
                $target_key = md5($group_id.$coresite_curr_state_name.$coresite_curr_state_slug.$coresite_curr_country_name.$coresite_curr_country_slug);
                $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
                if ($cached_value !== NULL) {
                    return $cached_value;
                }
            }
            elseif ($expected_no_of_result > 1)  { // If we want to return a certain number of result.
                $cache_session_id = 'get_page_content_multiple_v1';
                $target_key = md5($group_id.$coresite_curr_state_name.$coresite_curr_state_slug.$coresite_curr_country_name.$coresite_curr_country_slug);
                $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
                if ($cached_value !== NULL) {
                    return $cached_value;
                }
            }
        }
        // Cache check end
        
        if ($limit) {
            if ($expected_no_of_result > 1) {
                $query_limit = " LIMIT ".$expected_no_of_result;
            } 
            else {
                $query_limit = " LIMIT 1";
            }
        } else {
            $query_limit = "";
        }

        $country_id_sql = "'***'";

        if ($coresite_curr_state_name && $coresite_curr_state_slug) {
            $this_state_country_id = get_state_country($coresite_curr_state_slug, 'countryID'); // get the country for the state selected
            $country_id_sql = "'".$this_state_country_id."', '***'";
        }
        elseif ($coresite_curr_country_name && $coresite_curr_country_slug) {
            $this_country_id = is_country($coresite_curr_country_slug, 'countryID');

            $country_id_sql = "'".$this_country_id."', '***'";
        }
        
        // Describe_what_is_going_on
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT * , 
                        CASE 
                            WHEN country_id <> '***' THEN 3
                            ELSE 0
                        END as relevance
                        FROM composer_page_content cpc
                        WHERE cpc.group_id = ".sql_int($group_id)."
                            AND cpc.status = 'active'
                            AND country_id IN (".$country_id_sql.")
                        ORDER BY relevance DESC
                        ".$query_limit."";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $totalRows_Actions = mysqli_num_rows($Actions);
        
        if ($totalRows_Actions) {
            if ($limit) {                
                $final_response = mysqli_fetch_assoc($Actions);

                $dis_cache_result_set = array();

                if ($expected_no_of_result > 1) {
                    do {
                        $dis_cache_result_set[] = $final_response;
                    } while ($final_response = mysqli_fetch_assoc($Actions));
                }

                if (count($dis_cache_result_set)) {
                    $final_response = $dis_cache_result_set;
                }

                // Add to cache
                add_entry_cached_in_memory($cache_session_id, $target_key, $final_response, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

                return $final_response;
            }
            else {
                return $Actions;
            }
        }
        else return false;
    }
}

if (!function_exists('get_image_with_text')) { 
    function get_image_with_text($group_id, $limit = false){ // Get content of images with text/slug. get them randomly by using RAND()
        // Flexibility was added to this function to display certain number of or all images randomly. Contents which have $limit as "false" do not have limits i.e displays all images

        global $database_config, $config, $coresite_curr_state_name, $coresite_curr_state_slug, $coresite_curr_country_name, $coresite_curr_country_slug;

        // Cache check start
        if (!$limit) {
            $cache_session_id = 'get_image_with_text_v01';
            $target_key = md5($group_id.$coresite_curr_state_name.$coresite_curr_state_slug.$coresite_curr_country_name.$coresite_curr_country_slug);
            $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
            if ($cached_value !== NULL) {
                return $cached_value;
            }
        }
        // Cache check end

        if ($limit) {
            $query_limit = "LIMIT 8";
        } else {
            $query_limit = "";
        }

        $country_id_sql = "'***'";

        if ($coresite_curr_state_name && $coresite_curr_state_slug) {
            $this_state_country_id = get_state_country($coresite_curr_state_slug, 'countryID'); // get the country for the state selected
            $country_id_sql = "'".$this_state_country_id."', '***'";
        }
        elseif ($coresite_curr_country_name && $coresite_curr_country_slug) {
            $this_country_id = is_country($coresite_curr_country_slug, 'countryID');

            $country_id_sql = "'".$this_country_id."', '***'";
        }

        // Get componennts to be displayed
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT *, 
                        CASE 
                            WHEN country_id <> '***' THEN 3
                            ELSE 0
                        END as relevance
                        FROM composer_page_content
                        WHERE group_id = ".sql_int($group_id)."
                            AND `status` = 'active'
                            AND country_id IN (".$country_id_sql.")
                        ORDER BY relevance DESC
                        ".$query_limit."";                

        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $totalRows_Actions = mysqli_num_rows($Actions);


        if ($totalRows_Actions) {
            if (!$limit) {
                $final_response = mysqli_fetch_assoc($Actions);

                $dis_cache_result_set = array();

                do {
                    $dis_cache_result_set[] = $final_response;
                } while ($final_response = mysqli_fetch_assoc($Actions));

                if (count($dis_cache_result_set)) {
                    $final_response = $dis_cache_result_set;
                }

                // Add to cache
                add_entry_cached_in_memory($cache_session_id, $target_key, $final_response, date("Y-m-d H:i:s", strtotime("+ 1 day")));

                return $final_response;
            }
            else {
                return $Actions;
            }
        }
        else return false;
    }
}

if (!function_exists('generateToken')) {
    function generateToken($length=16) {
        $token = openssl_random_pseudo_bytes($length);  //Generate a random string.
        $token = bin2hex($token); //Convert the binary data into hexadecimal representation. P.S: This function doubles the number of strings specified in the function above.
        return $token;
    }
}

if (!function_exists('getDisApiProgrammeDetails')) {
    function getDisApiProgrammeDetails($programParameter, $program_id, $returnArray=true) {

        global $database_config, $config;

        // Get the chosen value for the programme
        mysqli_select_db($config, $database_config);
        $query_ItsOwn = "SELECT op.*, oprel.option_two 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        WHERE oprel.relationship_name = '".sql_string($programParameter)."'
                                AND oprel.option_one = ".sql_int($program_id)." 
                        ORDER BY oprel.relationship_id ASC";
        $ItsOwn = mysqli_query($config, $query_ItsOwn) or die(mysqli_error($config));
        $row_ItsOwn = mysqli_fetch_assoc($ItsOwn);
        $totalRows_ItsOwn = mysqli_num_rows($ItsOwn);

        if ($totalRows_ItsOwn) {
            if ($returnArray) { // return $thisFeatureArray
                $thisFeatureArray = array();

                do {
                    $thisFeatureArray[] = $row_ItsOwn['option_two'];
                } while($row_ItsOwn = mysqli_fetch_assoc($ItsOwn));

                return $thisFeatureArray;
            } 
            else { // return $thisActualValue
                if ($totalRows_ItsOwn) {
                    $thisActualValue = $row_ItsOwn['option_two'];
                    return $thisActualValue;
                } 
                else {
                    $thisActualValue = 0;
                    return $thisActualValue;
                }
            }
        }
        
    }
}

if (!function_exists('can_publisher_earn')) {
    function can_publisher_earn($prog_id) {
        global $database_config, $config;
        
        // Check if it is a course that publishers can promote, so that 
        // they don't accidentally get earnings on non-promotable courses like the MBA etc
        mysqli_select_db($config, $database_config);
        $query_DisProg = "SELECT np.*, oprel.option_two, op.option_value  
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                        WHERE oprel.relationship_name = 'programme type' 
                            AND op.option_id IN (883, 409) /* Only PCC and APCC courses */
                            AND np.programme_id = '".sql_int($prog_id)."' 
                            AND np.deleted = '0'
                        LIMIT 1";
        $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
        $row_DisProg = mysqli_fetch_assoc($DisProg);
        $totalRows_DisProg = mysqli_num_rows($DisProg);

        if ($totalRows_DisProg) {
            return true;
        }
        else return false;
    }
}

if (!function_exists('record_Api_Publisher_Earnings')) {
    function record_Api_Publisher_Earnings($api_Publisher_Id, $prog_id, $amount, $currency) {
        global $database_config, $config, $sess_user_id, $api_publisher_Earning_Percentage;

        $preferred_currency = "GBP"; // since GBP is what we're recording as the currency of the publisher earnings.

        // Convert the amount to GBP equivalent if the currency parameter of the function is not in GBP
        if ($currency != $preferred_currency) {
            $todays_conversion_rate = get_todays_conversion_rate($preferred_currency, $currency);

            // If today's rates for GBP has not been stored, try to get it and store it
            if (!$todays_conversion_rate) {
                // Get today's rate
                $api_conversion_rate = get_api_conversion_rate($currency, $preferred_currency); 

                if ($api_conversion_rate) { 
                    store_todays_conversion_rate($currency, $preferred_currency, $api_conversion_rate);

                    $todays_conversion_rate = $api_conversion_rate;
                }
                else { // If request failed, use last known rate
                    $todays_conversion_rate = get_last_conversion_rate($preferred_currency, $currency);
                }
            } 

            // If the rate is found because it is possible that FALSE is returned
            if ($todays_conversion_rate) {
                // Calculate the amount in GBP
                $amount = round($amount * $todays_conversion_rate, 2);

                // In case the commission is too small (so that the db doesn't record zero)
                if ($amount == 0.00) {
                    $amount = 0.01;
                }

                $currency = $preferred_currency;
            }
        }
        
        // If everything went well and the amount and courrency needed to proceed are valid
        if ($currency == $preferred_currency) {
            // Only record the earning if this user is making the first payment ever for this course. 
            // Don't record for subsequent payments incase the user has made a PART-PAYMENT before.
            // Publishers only get paid for the first instalment.
            mysqli_select_db($config, $database_config);
            
            $new_rule = true; // New rule: It doesn't matter who the publisher is, commission gets to be paid only once. (Incase the same person clicks on ads from different publishers, only the first instance gets acknowledged)

            if ($new_rule) {
                $additional_modifier = "";
            } 
            else {          
                $additional_modifier = "AND pub_id = '".sql_int($api_Publisher_Id)."'";
            }

            $query_Check_Percentage =   "SELECT *
                                        FROM api_course_purchases
                                        WHERE p_id = '".sql_int($sess_user_id)."'
                                            ".$additional_modifier."
                                        LIMIT 1";
            $Check_Percentage = mysqli_query($config, $query_Check_Percentage) or die(mysqli_error($config));
            $row_Check_Percentage = mysqli_fetch_assoc($Check_Percentage);
            $totalRows_Check_Percentage = mysqli_num_rows($Check_Percentage);

            if (!$totalRows_Check_Percentage) { // user has not made any payment through this publisher for this course before
                // check if the course is a degree course so as to use a different percentage for the publisher's earnings (15%)
                $query_Check_Prog_Type = "SELECT np.*, oprel.option_two, op.option_value  
                                        FROM option_relationships oprel 
                                        JOIN options op ON oprel.option_two = op.option_id 
                                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                        WHERE oprel.relationship_name = 'programme type' 
                                            AND op.option_id NOT IN (883, 409) /* Ignore PCC and APCC courses */
                                            AND np.programme_id = '".sql_int($prog_id)."' 
                                            AND np.deleted = '0'
                                        LIMIT 1";
                $Check_Prog_Type = mysqli_query($config, $query_Check_Prog_Type) or die(mysqli_error($config));
                $row_Check_Prog_Type = mysqli_fetch_assoc($Check_Prog_Type);
                $totalRows_Check_Prog_Type = mysqli_num_rows($Check_Prog_Type);

                if ($totalRows_Check_Prog_Type) { // programme is a degree course. Hence get the percentage (15) from the degree_course_percentage column in the api commission rank table
                    $api_publisher_Earning_Percentage = publisher_Earning_Percentage_From_Rank($api_Publisher_Id, true);
                } 
                else { // programme is not a degree course. Hence, get the normal percentage from api commission rank
                    $api_publisher_Earning_Percentage = publisher_Earning_Percentage_From_Rank($api_Publisher_Id);
                }

                $publisher_earning_from_dis_user = $amount * ($api_publisher_Earning_Percentage / 100);

                if ($publisher_earning_from_dis_user) {
                    $insertSQL = "INSERT INTO api_course_purchases (pub_id, purchase_date, p_id, prog_id, purchase_amount, currency, publisher_earning, earning_percentage)
                                VALUES (
                                    '".sql_int($api_Publisher_Id)."',
                                    '".sql_string(date("Y-m-d"))."',
                                    '".sql_int($sess_user_id)."',
                                    '".sql_int($prog_id)."',
                                    '".sql_float($amount)."',
                                    '".sql_string($currency)."',
                                    '".sql_float($publisher_earning_from_dis_user)."',
                                    '".sql_int($api_publisher_Earning_Percentage)."'
                                )";
                    mysqli_select_db($config, $database_config);
                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                    $insertStatus = mysqli_affected_rows($config);
                }
            }
        }
    }
}

if (!function_exists('publisher_Earning_Percentage_From_Rank')) { /* Check_for "publisher_Earning_Percentage_From_Rank" */
    function publisher_Earning_Percentage_From_Rank($pub_id, $get_degree_course_percentage=false) {
        global $database_config, $config;

        // get publisher's percentage
        mysqli_select_db($config, $database_config);
        $query_Publisher_Rank = "SELECT apu.*, arank.percentage, arank.degree_course_percentage 
                                 FROM api_users apu
                                 LEFT JOIN api_commission_rank arank ON apu.rank = arank.rank_id
                                 WHERE apu.api_user_id = ".sql_int($pub_id)." 
                                 LIMIT 1";
        $Publisher_Rank = mysqli_query($config, $query_Publisher_Rank) or die(mysqli_error($config));
        $row_Publisher_Rank = mysqli_fetch_assoc($Publisher_Rank);
        $totalRows_Publisher_Rank = mysqli_num_rows($Publisher_Rank);
 
        if ($totalRows_Publisher_Rank) {
            if ($get_degree_course_percentage) {
                return $row_Publisher_Rank['degree_course_percentage'];
            } else {
                return $row_Publisher_Rank['percentage'];
            }
        } else {
            return 0;
        }
    }
} /* End_check for "publisher_Earning_Percentage_From_Rank" */

if (!function_exists('detect_http_filename')) {
    function detect_http_filename($filename, $http_prefix) {
        if (preg_match('/^https?:\/\//i', $filename)) {
            return $filename;
        }
        else {
            return $http_prefix.$filename;
        }
    }
}

if (!function_exists('clear_user_cart_details_from_db')) {
    function clear_user_cart_details_from_db($programme_id) {
        global $database_config, $config, $sess_user_id;

        // check if programme is in cart
        mysqli_select_db($config, $database_config);
        $query_Check_Cart_Info = "SELECT *
                                FROM saved_to_cart
                                WHERE login_id = ".sql_int($sess_user_id)."
                                    AND prog_id = ".sql_int($programme_id)."
                                LIMIT 1";
        $Check_Cart_Info = mysqli_query($config, $query_Check_Cart_Info) or die(mysqli_error($config));
        $row_Check_Cart_Info = mysqli_fetch_assoc($Check_Cart_Info);
        $totalRows_Check_Cart_Info = mysqli_num_rows($Check_Cart_Info);

        if ($totalRows_Check_Cart_Info) {
            // Delete the programme from cart
            mysqli_select_db($config, $database_config);
            $deleteSQL = "DELETE FROM saved_to_cart
                        WHERE login_id = ".sql_int($sess_user_id)."
                            AND prog_id = ".sql_int($programme_id)."
                        LIMIT 1";
            $deleteResult = mysqli_query($config, $deleteSQL) or die(mysqli_error($config));
            $deleteStatus = mysqli_affected_rows($config);
        }
    }
}

if (!function_exists('show_add_to_cart_save_button')) {
    function show_add_to_cart_save_button($programme_id) {

        global $database_config, $config, $sess_user_id;

        mysqli_select_db($config, $database_config);
        $query_Cart_Info = "SELECT *
                            FROM saved_to_cart
                            WHERE login_id = ".sql_int($sess_user_id)."
                                AND prog_id = ".sql_int($programme_id)."
                            LIMIT 1";
                            
        $Cart_Info = mysqli_query($config, $query_Cart_Info) or die(mysqli_error($config));
        $row_Cart_Info = mysqli_fetch_assoc($Cart_Info);
        $totalRows_Cart_Info = mysqli_num_rows($Cart_Info);

        if ($totalRows_Cart_Info) {
            return false;
        }
        else return true;
    }
}

if (!function_exists('extract_number_of_months')) { /* Check_for "extract_number_of_months" */
    function extract_number_of_months($string) {
        if (preg_match('/(month|year)/i', $string)) {
            $no_months = 0;
            preg_match_all('/^\d+/', $string, $matches); 

            if (preg_match('/year/i', $string)) { // Get the number of months
                $no_months = (int)$matches[0][0] * 12;
            }
            elseif (preg_match('/month/i', $string)) { // Get the number of months
                $no_months = (int)$matches[0][0];
            }

            return $no_months;
        }
        
        return 1;
    }
} /* End_check for "extract_number_of_months" */

if (!function_exists('get_increment_percentage')) { /* Check_for "get_increment_percentage" */
    function get_increment_percentage($no_of_instalments) {
        global $database_config, $config;
        
        $enable_static_increment_fee_calculation = false;

        // Cache check start
        $cache_session_id = 'instalment_percentage_v3';
        $target_key = "";

        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);

        if ($cached_value !== NULL) {
            $percentage_highest_no_of_installment = 0;
            $found_the_value = false;
            $target_percentage = 0;

            foreach ($cached_value as $ins_cached_data) {
                $percentage_highest_no_of_installment = ($percentage_highest_no_of_installment < $ins_cached_data['percentage']) ? $ins_cached_data['percentage'] : $percentage_highest_no_of_installment;

                if ($ins_cached_data['no_of_instalments'] == $no_of_instalments) { // If this is the target instalment, store its percentage
                    $found_the_value = true;
                    $target_percentage = $ins_cached_data['percentage'];
                }
            }

            if (!$found_the_value) {
                $target_percentage = $percentage_highest_no_of_installment;
            }

            return $target_percentage / 100;
        }

        // Select the percentage from the database
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT no_of_instalments, percentage 
                            FROM instalment_percentages
                            WHERE status = 'active' 
                            ORDER BY no_of_instalments ASC";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);

        if (!$totalRows_Actions) {
            $enable_static_increment_fee_calculation = true;
        }

        if ($enable_static_increment_fee_calculation) { // Use the hard-coded instalment percentage.
            if ($no_of_instalments == 1) {
                return 0;
            }
            elseif ($no_of_instalments == 2) {
                return 0.20;
            }
            elseif ($no_of_instalments == 3) {
                return 0.30;
            }
            elseif ($no_of_instalments == 4) {
                return 0.40;
            }
            elseif ($no_of_instalments == 5) {
                return 0.50;
            }
            elseif ($no_of_instalments == 6) {
                return 0.60;
            }
            elseif ($no_of_instalments == 7) {
                return 0.70;
            }
            else return 0.80;
        } 
        else { // Select the percentage from the database
            $cached_array_value = array();
            $percentage_highest_no_of_installment = 0;
            $found_the_value = false;
            $target_percentage = 0;

            do {
                $cached_array_value[] = $row_Actions; // Just for caching

                $percentage_highest_no_of_installment = ($percentage_highest_no_of_installment < $row_Actions['percentage']) ? $row_Actions['percentage'] : $percentage_highest_no_of_installment;

                if ($row_Actions['no_of_instalments'] == $no_of_instalments) { // If this is the target instalment, store its percentage
                    $found_the_value = true;
                    $target_percentage = $row_Actions['percentage'];
                }
            } while($row_Actions = mysqli_fetch_assoc($Actions));

            if (!$found_the_value) {
                $target_percentage = $percentage_highest_no_of_installment;
            }

            if (count($cached_array_value)) { // Add to cache
                add_entry_cached_in_memory($cache_session_id, $target_key, $cached_array_value, date("Y-m-d H:i:s", strtotime("+30 minutes")));
            }

            return $target_percentage / 100;
        }

        return 0;
    }
} /* End_check for "get_increment_percentage" */

if (!function_exists('highest_instalment_amount')) { /* Check_for "highest_instalment_amount" */
    function highest_instalment_amount($programme_duration, $dis_programme_fee) {
        $no_months = extract_number_of_months($programme_duration);
        $instalmental_fee_percentage_increase = get_increment_percentage($no_months);
        return round(($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))/$no_months, 2);
    }
} /* End_check for "highest_instalment_amount" */

if (!function_exists('instalmental_fee')) { /* Check_for "instalmental_fee" */
    function instalmental_fee($programme_duration, $dis_programme_fee) {
        global $database_config, $config, $instalmental_fee_percentage_increase, $show_number_of_months_to_pay_instalmental_fee;

        $part_payment_allowed = 0;
        
        if (preg_match('/(month|year)/i', $programme_duration)) {
            $no_months = 0;
            preg_match_all('/^\d+/', $programme_duration, $matches); 

            if (preg_match('/year/i', $programme_duration)) { // Get the number of months
                $no_months = (int)$matches[0][0] * 12;
            }
            elseif (preg_match('/month/i', $programme_duration)) { // Get the number of months
                $no_months = (int)$matches[0][0];
            }
            
            // If the programme is more than two months then part payment is allowed
            if ($no_months > 2) {
                // Part payment should be two thirds of the total months (i.e payments must be completed before end of programme)
                //$part_payment_allowed = floor($no_months * (2/3));
                $part_payment_allowed = $no_months; 

                // Initialize variable to store details of available instalments
                $instalment_months = array();
                    
                // Get the possible number of instalments in the span of the instalment duration
                for ($x = $part_payment_allowed - 1; $x < $part_payment_allowed && $x > 0; $x = $x - 1) {
                    // If divisible without remainder 
                    if ($part_payment_allowed % $x == 0) { // If divisible without remainder 
                        $instalment_months[$x] = floor($part_payment_allowed/$x);
                    }
                }
            }
        }

        // If part payment is allowed
        if ($part_payment_allowed) {

            $profile_student_amount_incremented = round($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase), 2); // Increase by 20 percent
            $profile_monthly_amount = round($profile_student_amount_incremented / $part_payment_allowed, 2);

            /*if($show_number_of_months_to_pay_instalmental_fee) {
                return show_amount($profile_monthly_amount)." (".$part_payment_allowed." Instalments)";
            }
            else {
                return show_amount($profile_monthly_amount);
            }*/

            return array(
                $profile_monthly_amount, 
                $part_payment_allowed
            );
        }
    }
} /* End_check for "instalmental_fee" */

if (!function_exists('api_entity_reply')) { /* Check_for "api_entity_reply" */
    function api_entity_reply($value) {
        // return htmlentities($value, ENT_QUOTES);
        return $value;
    }
} /* End_check for "api_entity_reply" */

if (!function_exists('year_for_seo')) { /* Check_for "year_for_seo" */
    function year_for_seo() {
        $current_month = (int) date("m");
        $current_year = (int) date("Y");

        if ($current_month <= 10) {
            return $current_year;
        }
        else {
            return $current_year + 1;
        }
    }
} /* End_check for "year_for_seo" */

if (!function_exists('does_programme_have_modules')) { /* Check_for "does_programme_have_modules" */
    function does_programme_have_modules($prog_id) {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'does_programme_have_modules';
        $target_key = md5($prog_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        // Describe_what_is_going_on
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT np.programme_id, op.option_id
                            FROM option_relationships oprel 
                            JOIN options op ON oprel.option_one = op.option_id 
                            JOIN new_programmes np ON oprel.option_two = np.programme_id
                            JOIN modules m ON op.option_id = m.prog_id 
                            WHERE np.programme_id = '".sql_int($prog_id)."'
                                AND oprel.relationship_name = 'old prog to new prog'
                                AND m.deleted = '0' 
                                AND m.status <> 'inactive' 
                                AND np.deleted = '0'";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);

        if ($totalRows_Actions) {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, true, date("Y-m-d H:i:s", strtotime("+ 1 hour")));
            return true;
        }
        else {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, false, date("Y-m-d H:i:s", strtotime("+ 1 hour")));
            return false;
        }
    }
} /* End_check for "does_programme_have_modules" */

function get_user_cart_info() {
	global $database_config, $config, $sess_user_id;
    global $addToCartCookieDuration, $sessionRootDomain, $our_unique_site_domains;
	global $site_version, $coresite_curr_currency, $todays_conversion_rate, $fileCacheVersion, $hashed_user_mac_address; // Some additional variables needed for this purpose
    global $scholarship_cosmetics_is_on;

    $final_cart_values = array("no_of_courses_in_cart" => 0);
    
    $retrieved_Cart_Info = array();
    $no_of_courses_in_cart = 0;
    $total_fees_in_cart = 0;
    $payment_currency = PREFERRED_CURRENCY;
    $eachCartProgInfo = array();

    if ($sess_user_id > 0) { // If user is logged in
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student') {
            mysqli_select_db($config, $database_config);
            $query_Cart_Info = "SELECT *
                                FROM saved_to_cart
                                WHERE login_id = ".sql_int($sess_user_id)."";
            $Cart_Info = mysqli_query($config, $query_Cart_Info) or die(mysqli_error($config));
            $row_Cart_Info = mysqli_fetch_assoc($Cart_Info);
            $totalRows_Cart_Info = mysqli_num_rows($Cart_Info);

            if ($totalRows_Cart_Info) {
                do { // loop through the cart
                    $retrieved_Cart_Info[] = $row_Cart_Info['prog_id'];
                } while ($row_Cart_Info = mysqli_fetch_assoc($Cart_Info));
            }
        }
    }
    elseif ($theValueThatExists = $_COOKIE['s2cart']) { // Not signed in
        $retrieved_Cart_Info = explode(",", $theValueThatExists);

        // Make sure that only valid IDs are processed
        if ($initial_count = count($retrieved_Cart_Info)) {
            foreach ($retrieved_Cart_Info as $key => $value) {
                if (!$value) {
                    unset($retrieved_Cart_Info[$key]);
                }
            }
        }
    }

    if (count($retrieved_Cart_Info)) { // Fetch the cart programme details
        // Get all matching programmes 
        $query_DisProg = "SELECT np.*, oprel.option_two, op.option_id as subcategory_id, op.parent_option_id as parent_category_id, op.option_value 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                        JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                        JOIN options op_other ON option_prog_other.option_one = op_other.option_id 
                        WHERE oprel.relationship_name = 'programme type' 
                            AND np.programme_status = 'active' 
                            AND option_prog_other.relationship_name = 'old prog to new prog' 
                            AND op_other.status = 'active' 
                            AND np.programme_id IN (".implode(",", $retrieved_Cart_Info).")
                            AND np.deleted = '0'"; 
        $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
        $row_DisProg = mysqli_fetch_assoc($DisProg);
        $totalRows_DisProg = mysqli_num_rows($DisProg);

        $no_of_courses_in_cart = $totalRows_DisProg;
    }

    if ($no_of_courses_in_cart) {
        if ($totalRows_DisProg) {
            do {
                $prog_var_file_dir = array('', '../', '../../', '../../../', '../lrea/', '../new_portal/');
                $prog_var_file_name = 'inc.programme-variables.php';
            
                foreach ($prog_var_file_dir as $prog_var_file_path) {
                    if (@file_exists($prog_var_file_path.$prog_var_file_name)) {
                        require($prog_var_file_path.$prog_var_file_name);
                        break;
                    }
                }

                $total_fees_in_cart += $dis_programme_fee;
                $payment_currency = $dis_programme_currency;

                $eachCartProgInfo[] = array(
                    'prog_id' => $dis_programme_id, 
                    'prog_name' => $dis_programme_name, 
                    'prog_url' => $dis_programme_a_href,
                    'thumbnail' => $dis_programme_thumbnail, 
                    'prog_category' => strtolower($dis_programme_category),
                    'fee' => $dis_programme_fee,
                    'cosmetics_original_fee' => $cosmetics_dis_programme_original_fee
                );
            } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }

            $final_cart_values = array(
                "no_of_courses_in_cart" => $totalRows_DisProg, 
                "payment_currency" => $payment_currency, 
                "payment_total" => $total_fees_in_cart, 
                "programme_info" => $eachCartProgInfo
            );
        }
    }
    
    if (in_array($sess_user_id, array(2, 52476))) { // My student account user IDs on the online server
        // my_log_to_file(var_dump_ret($final_cart_values), 'cart_popup.txt');
    }

    return $final_cart_values;
}

if (!function_exists('get_user_wishlist_info')) { /* Check_for "get_user_wishlist_info" */
    function get_user_wishlist_info() {
        global $database_config, $config, $sess_user_id;
        global $addToCartCookieDuration, $sessionRootDomain, $our_unique_site_domains;
        global $site_version, $coresite_curr_currency, $todays_conversion_rate, $fileCacheVersion, $hashed_user_mac_address; // Some additional variables needed for this purpose

        $final_wishlist_values = array("no_of_courses_in_wishlist" => 0);
        
        $retrieved_Wishlist_Info = array();
        $no_of_courses_in_wishlist = 0;
        $total_fees_in_wishlist = 0;
        $payment_currency = PREFERRED_CURRENCY;
        $eachWishListProgInfo = array();

        if ($sess_user_id > 0) { // If user is logged in.
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student') { // Make sure the logged in user is a student.
                mysqli_select_db($config, $database_config);
                $query_Cart_Info = "SELECT *
                                    FROM saved_to_wishlist
                                    WHERE login_id = ".sql_int($sess_user_id)."";
                $Cart_Info = mysqli_query($config, $query_Cart_Info) or die(mysqli_error($config));
                $row_Cart_Info = mysqli_fetch_assoc($Cart_Info);
                $totalRows_Cart_Info = mysqli_num_rows($Cart_Info);

                if ($totalRows_Cart_Info) {
                    do { // loop through the cart
                        $retrieved_Wishlist_Info[] = $row_Cart_Info['prog_id'];
                    } while ($row_Cart_Info = mysqli_fetch_assoc($Cart_Info));
                }
            }
        }
        elseif ($theValueThatExists = $_COOKIE['wishlist']) {
            $retrieved_Wishlist_Info = explode(",", $theValueThatExists);

            // Make sure that only valid IDs are processed
            if ($initial_count = count($retrieved_Wishlist_Info)) {
                foreach ($retrieved_Wishlist_Info as $key => $value) {
                    if (!$value) {
                        unset($retrieved_Wishlist_Info[$key]);
                    }
                }
            }
        }

        if (count($retrieved_Wishlist_Info)) {
            // Get all matching programmes 
            $query_DisProg = "SELECT np.*, oprel.option_two, op.option_id as subcategory_id, op.parent_option_id as parent_category_id, op.option_value  
                            FROM option_relationships oprel 
                            JOIN options op ON oprel.option_two = op.option_id 
                            JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                            JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                            JOIN options op_other ON option_prog_other.option_one = op_other.option_id 
                            WHERE oprel.relationship_name = 'programme type' 
                                AND np.programme_status = 'active' 
                                AND option_prog_other.relationship_name = 'old prog to new prog' 
                                AND op_other.status = 'active' 
                                AND np.programme_id IN (".implode(",", $retrieved_Wishlist_Info).")
                                AND np.deleted = '0'"; 
            $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
            $row_DisProg = mysqli_fetch_assoc($DisProg);
            $totalRows_DisProg = mysqli_num_rows($DisProg);

            $no_of_courses_in_wishlist = $totalRows_DisProg;
        }

        if ($no_of_courses_in_wishlist) {
            if ($totalRows_DisProg) {
                do {
                    $prog_var_file_dir = array('', '../', '../../', '../../../', '../lrea/', '../new_portal/');
                    $prog_var_file_name = 'inc.programme-variables.php';
                
                    foreach ($prog_var_file_dir as $prog_var_file_path) {
                        if (@file_exists($prog_var_file_path.$prog_var_file_name)) {
                            require($prog_var_file_path.$prog_var_file_name);
                            break;
                        }
                    }

                    $total_fees_in_wishlist += $dis_programme_fee;
                    $payment_currency = $dis_programme_currency;

                    $eachWishListProgInfo[] = array(
                        'prog_id' => $dis_programme_id, 
                        'prog_name' => $dis_programme_name, 
                        'prog_url' => $dis_programme_a_href,
                        'thumbnail' => $dis_programme_thumbnail, 
                        'fee' => $dis_programme_fee
                    );
                } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }

                $final_wishlist_values = array(
                    "no_of_courses_in_wishlist" => $totalRows_DisProg, 
                    "payment_currency" => $payment_currency, 
                    "payment_total" => $total_fees_in_wishlist, 
                    "programme_info" => $eachWishListProgInfo
                );
            }
        }
        
        return $final_wishlist_values;
    }
} /* End_check for "get_user_wishlist_info" */

if (!function_exists('get_checkout_id')) {
    function get_checkout_id($uniq_id, $login_id) {
        global $database_config, $config;

        mysqli_select_db($config, $database_config);
        $query_Cart_Info = "SELECT checkout_id
                            FROM checkout
                            WHERE login_id = ".sql_int($login_id)."
                                AND uniq_id = '".sql_string($uniq_id)."'
                            LIMIT 1";
        $Cart_Info = mysqli_query($config, $query_Cart_Info) or die(mysqli_error($config));
        $row_Cart_Info = mysqli_fetch_assoc($Cart_Info);
        $totalRows_Cart_Info = mysqli_num_rows($Cart_Info);

        if ($totalRows_Cart_Info) {
            return $row_Cart_Info['checkout_id'];
        }
        else return 0;
    }
}

if (!function_exists('get_total_visible_reviews')) { /* Check_for "get_total_visible_reviews" */
    function get_total_visible_reviews() {
        global $database_config, $config;
    
        // Cache check start
        $cache_session_id = 'get_total_visible_reviews';
        $target_key = md5($cache_session_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        // total reviews
        mysqli_select_db($config, $database_config);
        $query_Total_Reviews = "SELECT *
                                FROM programme_rating
                                WHERE status = 'approved' 
                                    AND share_feedback = '1'
                                    AND deleted = '0'";
        $Total_Reviews = mysqli_query($config, $query_Total_Reviews) or die(mysqli_error($config));
        $row_Total_Reviews = mysqli_fetch_assoc($Total_Reviews);
        $totalRows_Total_Reviews = mysqli_num_rows($Total_Reviews);
        
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $totalRows_Total_Reviews);

        return $totalRows_Total_Reviews;
    }
} /* End_check for "get_total_visible_reviews" */

if (!function_exists('get_total_all_reviews')) { /* Check_for "get_total_all_reviews" */
    function get_total_all_reviews() {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'get_total_all_reviews';
        $target_key = md5($cache_session_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        // total reviews
        mysqli_select_db($config, $database_config);
        $query_Total_Reviews = "SELECT *
                                FROM programme_rating
                                WHERE deleted = '0'";
        $Total_Reviews = mysqli_query($config, $query_Total_Reviews) or die(mysqli_error($config));
        $row_Total_Reviews = mysqli_fetch_assoc($Total_Reviews);
        $totalRows_Total_Reviews = mysqli_num_rows($Total_Reviews);

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $totalRows_Total_Reviews);

        return $totalRows_Total_Reviews;
    }
} /* End_check for "get_total_all_reviews" */

if (!function_exists('calculate_review_percentage_by_rating')) { /* Check_for "calculate_review_percentage_by_rating" */
    function calculate_review_percentage_by_rating($score) {
        global $database_config, $config;
        // Cache check start
        $cache_session_id = 'calculate_review_percentage_by_rating';
        $target_key = md5($cache_session_id.$score);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        // review by rating score
        mysqli_select_db($config, $database_config);
        $query_Total_Rating = "SELECT *
                                FROM programme_rating
                                WHERE rating_score = '".sql_int($score)."'
                                    AND status = 'approved' 
                                    AND share_feedback = '1' 
                                    AND deleted = '0'";
        $Total_Rating = mysqli_query($config, $query_Total_Rating) or die(mysqli_error($config));
        $row_Total_Rating = mysqli_fetch_assoc($Total_Rating);
        $totalRows_Total_Rating = mysqli_num_rows($Total_Rating);

        // total reviews
        $totalRows_Total_Reviews = get_total_visible_reviews();

        $percentage = ($totalRows_Total_Rating / $totalRows_Total_Reviews) * 100;
        $percentage = round($percentage);

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $percentage);
        return $percentage;
    }
} /* End_check for "calculate_review_percentage_by_rating" */

if (!function_exists('get_average_visible_rating_score')) { /* Check_for "get_average_visible_rating_score" */
    function get_average_visible_rating_score() {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'get_average_visible_rating_score';
        $target_key = md5($cache_session_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        // review by rating score
        mysqli_select_db($config, $database_config);
        $query_Total_Rating = "SELECT AVG(rating_score) as this_average
                                FROM programme_rating
                                WHERE status = 'approved' 
                                    AND share_feedback = '1' 
                                    AND deleted = '0'";
        $Total_Rating = mysqli_query($config, $query_Total_Rating) or die(mysqli_error($config));
        $row_Total_Rating = mysqli_fetch_assoc($Total_Rating);
        $totalRows_Total_Rating = mysqli_num_rows($Total_Rating);

        $averageRatingScore = round($row_Total_Rating['this_average'], 1);
        
        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $averageRatingScore);
        
        return $averageRatingScore;
    }
} /* End_check for "get_average_visible_rating_score" */

if (!function_exists('add_new_email_subscriber')) { /* Check_for "add_new_email_subscriber" */
    function add_new_email_subscriber($email, $modified_date, $title='', $first_name='', $last_name='', $phone='') {
        global $database_config, $config;

        // check if the email has been subscribed before so as to prevent multiple entries of the same email.
        mysqli_select_db($config, $database_config);
        $query_Check_Email = "SELECT email
                            FROM email_subscribers
                            WHERE email = '".sql_string($email)."'
                            LIMIT 1";
        $Check_Email = mysqli_query($config, $query_Check_Email) or die(mysqli_error($config));
        $totalRows_Check_Email = mysqli_num_rows($Check_Email);

        if ($totalRows_Check_Email) { // return a message if email had been registered for subscription.
            $response = array(
                'status' => 'subscribed',
                'message' => '<p class="text-warning text-center">You have already subscribed to our newsletter. <br>You will receive news and updates from us.</p>'
            );
            return json_encode($response);
        } else { 
            $token = generateToken(); 
            
            $insertSQL = "INSERT INTO email_subscribers (title, first_name, last_name, phone, email, token, status, modified_date)
                        VALUES (
                            '".sql_string($title)."',
                            '".sql_string($first_name)."',
                            '".sql_string($last_name)."',
                            '".sql_string($phone)."',
                            '".sql_string($email)."',
                            '".sql_string($token)."',
                            'sub',
                            '".sql_string($modified_date)."'
                        )";
            mysqli_select_db($config, $database_config);
            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
            $insertStatus = mysqli_affected_rows($config);

            if ($insertStatus >= 0) {
                $response = array(
                    'status' => 'success'
                );
                return json_encode($response);
            } 
            else {
                $response = array(
                    'status' => 'failed',
                    'message' => "<p class='text-danger'>Subscription failed. Please try again.</p>"
                );
                return json_encode($response);
            }
        }
    }
} /* End_check for "add_new_email_subscriber" */

if (!function_exists('get_number_nationalities_in_portal')) { /* Check_for "get_number_nationalities_in_portal" */
    function get_number_nationalities_in_portal($use_manual_value = 0) {
        global $database_config, $config, $student_number_stages;

        if ($use_manual_value) return $use_manual_value;
        
        // Cache check start
        $cache_session_id = 'get_number_nationalities_in_portal_v2';
        $target_key = md5($cache_session_id.$student_number_stages);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end
        
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT COUNT(DISTINCT nationality) as no_of_nationalities
                        FROM prospects p
                        WHERE p.deleted IN ('0')
                            AND p.sess_start_date <> '0000-00-00'
                            AND p.prog_interest NOT IN (
                                SELECT op_other.option_id 
                                FROM options op 
                                JOIN option_relationships oprel ON op.option_id = oprel.option_two 
                                JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                                JOIN options op_other ON option_prog_other.option_one = op_other.option_id 
                                WHERE oprel.relationship_name = 'programme type' 
                                    AND op.option_id = '617' /* Free courses only */
                                    AND option_prog_other.relationship_name = 'old prog to new prog' 
                                    AND np.deleted = '0'
                            )
                            /*AND p.stage IN (".implode(", ", $student_number_stages).")*/";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);

        if ($totalRows_Actions) {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, $row_Actions['no_of_nationalities']);
            return $row_Actions['no_of_nationalities'];
        }
        else {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, '185');
            return '185';
        }
    }
} /* End_check for "get_number_nationalities_in_portal" */

if (!function_exists('get_number_employers_in_portal')) { /* Check_for "get_number_employers_in_portal" */
    function get_number_employers_in_portal($use_manual_value = 0) {
        global $database_config, $config, $student_number_stages;

        if ($use_manual_value) return $use_manual_value;

        // Cache check start
        $cache_session_id = 'get_number_employers_in_portal';
        $target_key = md5($cache_session_id.$student_number_stages);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end
        
         //Count the number of distinct work company from student experience table
        mysqli_select_db($config, $database_config);
        $query_company_count = "SELECT COUNT(DISTINCT work_company) as companies_count
                                    FROM users_students_experience";
        $company_count = mysqli_query($config, $query_company_count) or die(mysqli_error($config));
        $row_company_count = mysqli_fetch_assoc($company_count);
        $totalRows_company_count = mysqli_num_rows($company_count);

        if ($totalRows_company_count) {
            return $row_company_count['companies_count'];
        }
        else return 18106;
    }
} /* End_check for "get_number_employers_in_portal" */

function get_number_courses_in_portal() {
    global $database_config, $config;
    
    // Cache check start
    $cache_session_id = 'get_number_courses_in_portal';
    $target_key = md5($cache_session_id);
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
    if ($cached_value !== NULL) {
        return $cached_value;
    }
    // Cache check end
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT option_id 
                    FROM options 
                    WHERE option_group = 'courses' 
                        AND option_visibility = '1'
                    ORDER BY option_name ASC, option_value ASC";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $totalRows_Actions = mysqli_num_rows($Actions);
    
    // Add to cache
    add_entry_cached_in_memory($cache_session_id, $target_key, $totalRows_Actions, date("Y-m-d H:i:s", strtotime('+90 minutes')));

    return $totalRows_Actions;
}

if (!function_exists('round_number_to_add_plus')) { /* Check_for "round_number_to_add_plus" */
    function round_number_to_add_plus($value) {
        global $database_config, $config;

		if ($value <= 10) {
			return 10;
		} elseif ($value < 100) {
			return floor($value / 10) * 10;
		} elseif ($value < 1000) {
			return floor($value / 100) * 100;
		} elseif ($value >= 1000) {
			return floor($value / 1000) * 1000;
		} else {
			return $value;
		}
    }
} /* End_check for "round_number_to_add_plus" */

if (!function_exists('check_if_programme_matches_coupon')) { /* Check_for "check_if_programme_matches_coupon" */
    function check_if_programme_matches_coupon($coupon_id, $prog_id) {
        global $database_config, $config;
        // check if prog is applicable for coupon provided
        mysqli_select_db($config, $database_config);
        $query_checkifProgHasCoupon = "SELECT *
                                    FROM coupon_programmes
                                    WHERE coupon_id = ".sql_int($coupon_id)."
                                        AND prog_id = ".sql_int($prog_id)."
                                    LIMIT 1";
        $checkifProgHasCoupon = mysqli_query($config, $query_checkifProgHasCoupon) or die(mysqli_error($config));
        $totalRows_checkifProgHasCoupon = mysqli_num_rows($checkifProgHasCoupon);

        if ($totalRows_checkifProgHasCoupon) {
            return true;
        }

        return false;

    }

} /* End_check for "check_if_programme_matches_coupon" */

if (!function_exists('generate_facebook_event_id')) {  /* Check_for "generate_facebook_event_id" */
    function generate_facebook_event_id() {
        $random_number = mt_rand(111, 999);
        // return substr(md5($random_number.date('His')), 0, 10);
        return md5($random_number.date('YmdHis'));
    }
} /* End_check for "round_number_to_add_plus" */

if (!function_exists('get_email_from_checkout_id')) { /* Check_for "get_email_from_checkout_id" */
    function get_email_from_checkout_id($checkout_id) {
        global $database_config, $config;

        // Get the login_id for this checkout_id
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT login_id 
                        FROM checkout
                        WHERE checkout_id = ".sql_int($checkout_id)." 
                        LIMIT 1";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);

        if ($totalRows_Actions) {
            // Get the email that matches the login_id
            mysqli_select_db($config, $database_config);
            $query_Actions = "SELECT email 
                            FROM users_students 
                            WHERE login_id = ".sql_int($row_Actions['login_id'])." 
                            LIMIT 1";
            $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
            $row_Actions = mysqli_fetch_assoc($Actions);
            $totalRows_Actions = mysqli_num_rows($Actions);

            if ($totalRows_Actions) {
                if ($row_Actions['email']) {
                    return $row_Actions['email'];
                }
            }
        }

        return '';
    }

} /* End_check for "get_email_from_checkout_id" */

if (!function_exists('get_speed_enhanced_info_based_on_request')) {  /* Check_for "get_speed_enhanced_info_based_on_request" */
    function get_speed_enhanced_info_based_on_request($requested_info_name) {
        global $database_config, $config;

        // Describe_what_is_going_on
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT option_value
                        FROM options
                        WHERE option_name =  '".sql_string($requested_info_name)."'
                        LIMIT 1";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);

        return $row_Actions['option_value'];
    }
} /* End_check for "get_speed_enhanced_info_based_on_request" */

if (!function_exists('get_passed_currency_symbol')) {  /* Check_for "get_passed_currency_symbol" */
    function get_passed_currency_symbol($currency_code) {
        global $database_config, $config, $array_of_available_currency_symbols;

        $symbol_for_programme_currency = $currency_code;

        if (count($array_of_available_currency_symbols)) {
            if (in_array($currency_code, array_keys($array_of_available_currency_symbols))) {
                $symbol_for_programme_currency = $array_of_available_currency_symbols[$currency_code];
            }
        }
        else {
            // Get this programme symbol if it exists
            mysqli_select_db($config, $database_config);
            $query_disProgCurrencySymbol = "SELECT api_currency_symbol
                                            FROM myip_ms_countries
                                            WHERE api_currency_code = '".sql_string($currency_code)."'
                                            LIMIT 1";
            $disProgCurrencySymbol = mysqli_query($config, $query_disProgCurrencySymbol) or die(mysqli_error($config));
            $row_disProgCurrencySymbol = mysqli_fetch_assoc($disProgCurrencySymbol);
            $totalRows_disProgCurrencySymbol = mysqli_num_rows($disProgCurrencySymbol);

            if ($totalRows_disProgCurrencySymbol && $row_disProgCurrencySymbol['api_currency_symbol']) {
                $symbol_for_programme_currency = $row_disProgCurrencySymbol['api_currency_symbol'];
            }
        }

        return $symbol_for_programme_currency;
    }
} /* End_check for "get_passed_currency_symbol" */


/* ------------- Caching Functions Start ------------- */
    // DYNAMIC CACHE BUSTING FILE
    if ($db_config_file_dir) { // Use this to try to locate file that holds the cache fingerprint variable.
        if (defined("ROOT_PATH_TO_PORTAL")) {
            $cache_variable_file_name = ROOT_PATH_TO_PORTAL.'cache-fingerprint-file.php';
        }
		else {
        	$cache_variable_file_name = 'cache-fingerprint-file.php';
		}

        foreach ($db_config_file_dir as $db_config_file_path) {
            if (@file_exists($db_config_file_path.$cache_variable_file_name)) {
                if ($req_file_contents = @file_get_contents($db_config_file_path.$cache_variable_file_name)) { // If the file's content can be read
                    include_once($db_config_file_path.$cache_variable_file_name);
                    break;
                }
            }
        }
    }

    if (isset($file_cache_fingerprint) && $file_cache_fingerprint) { // Use the fingerprint in the file above. The cache is bursted when a content is changed from composer or if triggered manaully from the portal.
        $cache_name_busting = $file_cache_fingerprint;
    }
    else { // Use the hard-coded fingerprint.
        $cache_name_busting = 'fingerprint18_';
    }

    $disable_cache_for_localhost = false;

    if (!function_exists('get_entry_cached_in_session')) {  /* Check_for "get_entry_cached_in_session" */
        function get_entry_cached_in_session($cache_session_id, $target_key) {
            global $time_now, $cache_name_busting;

            $cache_session_id = md5($cache_name_busting.$cache_session_id);

            if ($_SESSION[$cache_session_id]) {
                if ($unserialized_entries = unserialize($_SESSION[$cache_session_id])) {
                    if ($unserialized_entries[$target_key]['expires'] >= $time_now) { // Make sure it hasn't expired
                        $final_value_needed = $unserialized_entries[$target_key]['value'];

                        if ($final_value_needed) {
                            return $final_value_needed;
                        }
                    }
                }
            }

            return NULL;
        }
    } /* End_check for "get_entry_cached_in_session" */

    if (!function_exists('add_entry_cached_in_session')) {  /* Check_for "add_entry_cached_in_session" */
        function add_entry_cached_in_session($cache_session_id, $target_key, $value, $expiration_date = '') {
            global $cache_name_busting;
            
            $cache_session_id = md5($cache_name_busting.$cache_session_id);

            if (!preg_match('/^\d{4}-\d{2}-\d{2}/i', $expiration_date)) {
                $expiration_date = date("Y-m-d H:i:s", strtotime("+ 6 hours")); // Every 6 hours from the time it was first cached
            }

            if ($_SESSION[$cache_session_id]) {
                if ($unserialized_entries = unserialize($_SESSION[$cache_session_id])) {
                    $unserialized_entries[$target_key] = array('value'=>$value, 'expires'=>$expiration_date);

                    $_SESSION[$cache_session_id] = serialize($unserialized_entries);
                }
                else {
                    $_SESSION[$cache_session_id] = serialize(array($target_key => array('value'=>$value, 'expires'=>$expiration_date)));
                }
            }
            else {
                $_SESSION[$cache_session_id] = serialize(array($target_key => array('value'=>$value, 'expires'=>$expiration_date)));
            }
            
            return true;
        }
    } /* End_check for "add_entry_cached_in_session" */

    // Initialize status of Memcache
    $memcache_availability = "cacheOff";

    if (class_exists('Memcached')) {
        $memcache_availability = "cacheOn";

        /* MAC:
            To start memcached:
                brew services start memcached
            Or, if you don't want/need a background service you can just run:
                /usr/local/opt/memcached/bin/memcached -l localhost
        */

        /* ======== Memcached Server Connection ========= */
        $memcache = new Memcached();
        $memcache->addServer('localhost', 11211);

        // Test the connection by trying to perform a dummy operation
        if (!$memcache->getStats()) {
            $memcache_availability = "cacheOff";
            // die("Could not connect to the Memcached server");
        }
        /* ======== Memcached Server Connection ========= */

        if ($disable_cache_for_localhost && ($site_version == 'localhost')) $memcache = false;
    }
    
    if ($memcache_availability == "cacheOn") { // Use memcache
        if (!function_exists('get_entry_cached_in_memory')) {  /* Check_for "get_entry_cached_in_memory" */
            function get_entry_cached_in_memory($cache_mem_key, $target_key) {
                global $time_now, $cache_name_busting, $memcache;

                if ($memcache) {
                    $cache_mem_key = md5($cache_name_busting.$cache_mem_key.$target_key);

                    $getCacheDetail = $memcache->get($cache_mem_key);

                    if ($getCacheDetail) {
                        if ($unserialized_entries = unserialize($getCacheDetail)) {
                            if ($unserialized_entries[$target_key]['expires'] >= $time_now) { // Make sure it hasn't expired
                                $final_value_needed = $unserialized_entries[$target_key]['value'];

                                if ($final_value_needed) {
                                    return $final_value_needed;
                                }
                            }
                        }
                    }
                }

                return NULL;
            }
        } /* End_check for "get_entry_cached_in_memory" */

        if (!function_exists('add_entry_cached_in_memory')) {  /* Check_for "add_entry_cached_in_memory" */
            function add_entry_cached_in_memory($cache_mem_key, $target_key, $value, $expiration_date = '') {
                global $time_now, $cache_name_busting, $memcache;

                if ($memcache) {
                    $cache_mem_key = md5($cache_name_busting.$cache_mem_key.$target_key); // Cache bursting
                    
                    $getCacheDetail = $memcache->get($cache_mem_key); // Check for existing records

                    $TTL = (60*60*12); // Currently set to 12 hours but if set to 0, then it never expires. 
                    // Concept explained better here https://stackoverflow.com/questions/6027517/can-the-time-to-live-ttl-for-a-memcached-key-be-set-to-infinite/6027559

                    if (!preg_match('/^\d{4}-\d{2}-\d{2}/i', $expiration_date)) {
                        $expiration_date = date("Y-m-d H:i:s", strtotime("+ 6 hours")); // Every 6 hours from the time it was first cached
                    }
                    else {
                        $TTL = (60*60*24); // Now set to 24 hours
                    }

                    if ($getCacheDetail) { // If existing record is found
                        if ($unserialized_entries = unserialize($getCacheDetail)) { // If the record CAN be un-serialized and is NOT empty
                            $unserialized_entries[$target_key] = array('value'=>$value, 'expires'=>$expiration_date); // Store the new value together with the existing values
                            $serialized_entries = serialize($unserialized_entries); // Serialize everything
                        }
                        else { // If the record CANNOT be un-serialized OR is empty
                            $serialized_entries = serialize(array($target_key => array('value'=>$value, 'expires'=>$expiration_date))); // Serialize and store the new value
                        }
                    }
                    else { // If no existing record is found
                        $serialized_entries = serialize(array($target_key => array('value'=>$value, 'expires'=>$expiration_date))); // Serialize and store the new value
                    }

                    $memcache->set($cache_mem_key, $serialized_entries, $TTL);
                    
                    return true;
                }

                return false;
            }
        } /* End_check for "add_entry_cached_in_memory" */
    }
    else { // Otherwise, use session
        if (!function_exists('get_entry_cached_in_memory')) {  /* Check_for "get_entry_cached_in_memory" */
            function get_entry_cached_in_memory($cache_mem_key, $target_key) {
            return get_entry_cached_in_session($cache_mem_key, $target_key);
            }
        } /* End_check for "get_entry_cached_in_memory" */

        if (!function_exists('add_entry_cached_in_memory')) {  /* Check_for "add_entry_cached_in_memory" */
            function add_entry_cached_in_memory($cache_mem_key, $target_key, $value, $expiration_date = '') {
                return add_entry_cached_in_session($cache_mem_key, $target_key, $value, $expiration_date);
            }
        } /* End_check for "add_entry_cached_in_memory" */
    }
/* ------------- Caching Functions End ------------- */

if (!function_exists('get_programme_partner_schools')) { /* Check_for "get_programme_partner_schools" */
    function get_programme_partner_schools($new_prog_id) {
        global $database_config, $config;
        
        // Cache check start
        $cache_session_id = 'get_programme_partner_schools_v2';
        $target_key = md5($cache_session_id.$new_prog_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end
        
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT op.*, ps.partner_name, oprel.option_two 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        JOIN partner_schools ps ON oprel.option_two = ps.partner_id
                        WHERE oprel.relationship_name = 'programme partner schools'  
                                AND oprel.option_one = ".sql_int($new_prog_id)." 
                        ORDER BY oprel.relationship_id ASC";//return $query_Actions;
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);
        
        if ($totalRows_Actions) {
            $final_value = array();
            
            do {
                $final_value[$row_Actions['option_two']] = ucwords($row_Actions['partner_name']);
            } while($row_Actions = mysqli_fetch_assoc($Actions));
            
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, $final_value, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

            return $final_value;
        }
        else {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, false, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

            return false;
        }
    }
} /* End_check for "get_programme_partner_schools" */

if (!function_exists('get_partner_school_info')) { /* Check_for "get_partner_school_info" */
    function get_partner_school_info($partner_id, $requested_column) {
        global $database_config, $config;
        
        if (is_array($requested_column)) {
			$official_award = array();
            $target = implode("", $requested_column);
		}
		else {
			$official_award = "";
            $target = $requested_column;
		}

        // Cache check start
        $cache_session_id = 'get_partner_school_info';
        $target_key = md5($cache_session_id.$partner_id.$target);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end
        
        mysqli_select_db($config, $database_config);
        $query_getThisPartnerSchool = "SELECT * 
                                        FROM partner_schools
                                        WHERE deleted = '0'
                                            AND partner_id = ".sql_int($partner_id)."
                                        LIMIT 1";
        $getThisPartnerSchool = mysqli_query($config, $query_getThisPartnerSchool) or die(mysqli_error($config));
        $row_getThisPartnerSchool = mysqli_fetch_assoc($getThisPartnerSchool);
        $totalRows_getThisPartnerSchool = mysqli_num_rows($getThisPartnerSchool);

        if ($totalRows_getThisPartnerSchool) {
            if (is_array($requested_column)) {
				foreach ($requested_column as $value) {
					$official_award[$value] = $row_getThisPartnerSchool[$value];
				}
			}
			else {
				$official_award = $row_getThisPartnerSchool[$requested_column];
			}
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $official_award, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

        return $official_award;
    }
} /* End_check for "get_partner_school_info" */

function get_latest_fb_info_from_db($search_column, $search_value, $needed_column) {
    global $database_config, $config;

    $fingerprint = sha1($search_column.$search_value.$needed_column);

    if (!isset($_SESSION['fb_param_expires']) || ($_SESSION['fb_param_expires'] <= date("Y-m-d H:i:s"))) {
        $_SESSION['fb_param_expires'] = date("Y-m-d H:i:s", strtotime('+60 minutes'));

        // Clear all existing values
        foreach ($_SESSION as $key => $value) {
            if (preg_match('/^fb_param_/i', $key)) {
                unset($_SESSION[$key]);
            }
        }
    }
    
    if (!isset($_SESSION['fb_param_'.$fingerprint])) {
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT ".$needed_column."
                        FROM fb_tracking  
                        WHERE ".$search_column." = '".sql_string($search_value)."' 
                                AND ".$needed_column." <> '' 
                        ORDER BY added_date DESC
                        LIMIT 1";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);
        
        if ($totalRows_Actions) {
            $_SESSION['fb_param_'.$fingerprint] = $row_Actions[$needed_column];

            return $row_Actions[$needed_column];
        }
        else {
            $_SESSION['fb_param_'.$fingerprint] = false;

            return false;
        }
    }
    else {
        return $_SESSION['fb_param_'.$fingerprint];
    }
}

function insert_latest_fb_info_into_db($fbp, $fbc, $email_hash, $ip_address, $user_agent) {
    global $database_config, $config;

    $availability_check = $fbp.$fbc.$email_hash;

    if (!$availability_check) {
        return false;
    }

    $fingerprint = sha1($fbp.$fbc.$email_hash.$ip_address.$user_agent);
    
    if (!$_SESSION['fb_track_'.$fingerprint]) {
        // Check for an existing value
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT tracking_id
                        FROM fb_tracking 
                        WHERE email_hash = '".sql_string($email_hash)."' 
                                AND ip_address = '".sql_string($ip_address)."' 
                                AND fbp = '".sql_string($fbp)."' 
                                AND fbc = '".sql_string($fbc)."' 
                        LIMIT 1";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $totalRows_Actions = mysqli_num_rows($Actions);//echo '<pre>'; var_dump($query_Actions); echo '</pre>'; exit();

        if (!$totalRows_Actions) {
            $insertSQL = "INSERT IGNORE INTO fb_tracking (fbp, fbc, email_hash, ip_address, user_agent, added_date) 
                        VALUES (
                            '".sql_string($fbp)."', 
                            '".sql_string($fbc)."', 
                            '".sql_string($email_hash)."', 
                            '".sql_string($ip_address)."', 
                            '".sql_string($user_agent)."', 
                            '".date("Y-m-d H:i:s")."'
                        )";
            mysqli_select_db($config, $database_config);
            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
            $insertStatus = mysqli_affected_rows($config);
    
            if ($insertStatus >= 0) { // If INSERT is successful
                $_SESSION['fb_track_'.$fingerprint] = true;
    
                return true;
            }
            else { // If INSERT failed
                return false;
            }
        }
        else {
            $_SESSION['fb_track_'.$fingerprint] = true;
        }
    }

    return true;
}

if (!function_exists('remove_item_from_cart_or_wishlist_session')) { /* Check_for "remove_item_from_cart_or_wishlist_session" */
    function remove_item_from_cart_or_wishlist_session($session_name, $item_to_remove) {
        if (isset($_SESSION[$session_name])) {
            $item_Key_In_Session = array_search($item_to_remove, $_SESSION[$session_name]['items']['prog_ids']);

            if ($item_Key_In_Session !== false) {
                unset($_SESSION[$session_name]['items']['prog_ids'][$item_Key_In_Session]);

                // Empty the prog_details array so that the programme details get re-populated into the array when the cart/wishlist is read again.
                $_SESSION[$session_name]['items']['prog_details'] = array();
            }
        }
    }
} /* End_check for "remove_item_from_cart_or_wishlist_session" */

if (!function_exists('set_cookie_for_our_unique_site_domains')) { /* Check_for "set_cookie_for_our_unique_site_domains" */
    function set_cookie_for_our_unique_site_domains($cookie_key, $cookie_value, $expires) {
        global $our_unique_site_domains;

        if (is_array($our_unique_site_domains)) {
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie($cookie_key, $cookie_value, $expires, "/", $uniqueDomainRoot);
                }
            }
        }
    }
} /* End_check for "set_cookie_for_our_unique_site_domains" */

if (!function_exists('our_geoplugin_details')) { /* Check_for "our_geoplugin_details" */
    function our_geoplugin_details($ip) {
        global $site_version;

        if ($site_version == 'online') {
            if ($db_ip_location = fetch_db_ip_address_location($ip)) {
                return array(
                    "country_code" => $db_ip_location
                );
            }
            else {
                if (defined("IP_API_ACCESS_KEY")) {
                    $host = 'http://api.ipapi.com/'.$ip.'?access_key='.IP_API_ACCESS_KEY;
                }
                else {
                    $host = 'http://www.geoplugin.net/php.gp?ip='.$ip.'&base_currency=USD'; // No longer in use since it fails intermittently
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $host);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close ($ch);

                if ($response) {
                    $country_code = "";
                    
                    if (defined("IP_API_ACCESS_KEY")) {
                        if ($decoded_values = json_decode($response, TRUE)) {
                            $retrieved_values = $decoded_values;
                            $country_code = $retrieved_values['country_code'];
                        }
                    }
                    else {
                        if ($unserialized_values = unserialize($response)) {
                            $retrieved_values = $unserialized_values;
                            $country_code = $retrieved_values['geoplugin_countryCode'];
                        }
                    }

                    if ($country_code) {
                        save_db_ip_address_location($ip, $country_code);
                        return $retrieved_values;
                    }
                }
            }
        }

        return array();
    }
} /* End_check for "our_geoplugin_details" */

if (!function_exists('record_payment_transaction_reference')) { /* Check_for "record_payment_transaction_reference" */
    function record_payment_transaction_reference($checkout_id, $trans_ref, $payment_processor) {
        global $database_config, $config, $sess_user_id;

        // Record the transaction reference passed
        mysqli_select_db($config, $database_config);
        $updateSQL = "UPDATE checkout
                    SET transaction_ref = '".sql_string($trans_ref)."',
                        tr_modified_date = '".sql_string(date("Y-m-d H:i:s"))."',
                        tr_payment_processor = '".sql_string($payment_processor)."'
                    WHERE login_id = '".sql_int($sess_user_id)."'
                        AND checkout_id = '".sql_int($checkout_id)."'
                    LIMIT 1";
        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
        $updateStatus = mysqli_affected_rows($config);

        my_log_to_file('TxRef='.$trans_ref.', Processor='.$payment_processor.', LoginID='.$sess_user_id.', CheckoutID='.$checkout_id, date("Y-m").'.txt', 'TxHistoryLogs');
    }
}/** End_check for record_payment_transaction_reference */

if (!function_exists('fetch_db_ip_address_location')) { /* Check_for "fetch_db_ip_address_location" */
    function fetch_db_ip_address_location($ipAddress, $requested_column = 'country_code') {
        global $config, $database_config;

        // Retrieve already saved ip information
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT *
                        FROM recorded_ip_addresses
                        WHERE ip_address = '".sql_string($ipAddress)."' 
                        LIMIT 1";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);

        if ($totalRows_Actions) {
            if ($row_Actions['date_added'] > date("Y-m-d H:i:s", strtotime("-12 months"))) {
                return $row_Actions[$requested_column];
            }
        }

        return '';
    }
} /* End_ Check_for "fetch_db_ip_address_location */

if (!function_exists('save_db_ip_address_location')) { /* Check_for "save_db_ip_address_location" */
    function save_db_ip_address_location($ipAddress, $country_code) {
        global $config, $database_config, $time_now;

        // Insert passed IP Address information retrieved
        $insertSQL = "INSERT INTO `recorded_ip_addresses` ( `ip_address` , `country_code` , `date_added`) 
                    VALUES (
                        '".sql_string($ipAddress)."' , 
                        '".sql_string($country_code)."' , 
                        '".sql_string($time_now)."'
                    )
                    ON DUPLICATE KEY UPDATE country_code = VALUES(country_code),
                                            date_added = VALUES(date_added)";
        mysqli_select_db($config, $database_config);
        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));

        return true;
    }
} /* End_ Check_for "save_db_ip_address_location */

if (!function_exists('my_log_to_file')) {
    function my_log_to_file($string, $log_target_filename = 'my-log.txt', $log_folder_name = 'my_debug_logs') {
        if ($log_folder_name && $log_target_filename) {
            $final_file_path = $log_folder_name.'/'.$log_target_filename;
            $directory_exists = true;
    
            if (!is_dir($log_folder_name)) { // If it doesn't exist, try to create it
                if (!mkdir($log_folder_name, 0777, TRUE)) { 
                    // If the folder(s) can't be created
                    $directory_exists = false;
                }
            }

            if ($directory_exists) {
                file_put_contents($final_file_path, '['.date("Y-m-d H:i:s").'] '.PHP_EOL.$string.PHP_EOL, FILE_APPEND);
            }
        }
    }
}

if (!function_exists('var_dump_ret')) { /* Check_for "var_dump_ret" */
	function var_dump_ret($mixed = null) {
		ob_start();
		var_dump($mixed);
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}  /* End_ Check_for "var_dump_ret */


if (!function_exists('convert_from_utc')) { /* Check_for "convert_from_utc" */
    function convert_from_utc($date = '0000-00-00 00:00:00', $timezone = 'WAT'){
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone($timezone));

        return $date->format('Y-m-d H:i:s');
    }
}  /* End_ Check_for "convert_from_utc */

if (!function_exists('convert_to_utc')) { /* Check_for "convert_to_utc" */
    function convert_to_utc($date = '0000-00-00 00:00:00', $timezone = 'WAT'){
        $date = new DateTime($date, new DateTimeZone($timezone));
        $date->setTimezone(new DateTimeZone('UTC'));

        return $date->format('Y-m-d H:i:s');
    }
}  /* End_ Check_for "convert_to_utc */

if (!function_exists('date_to_timezone')) { /* Check_for "date_to_timezone" */
    function date_to_timezone($date, $from_timezone, $to_timezone){
        $date = new DateTime($date, new DateTimeZone($from_timezone));
        $date->setTimezone(new DateTimeZone($to_timezone));

        return $date->format('Y-m-d H:i:s');
    }
}  /* End_ Check_for "date_to_timezone */

if (!function_exists('has_student_requested_course_from_business')) { /* Check_for "has_student_requested_course_from_business" */
    function has_student_enrolled_or_requested_course_from_business($biz_id, $course_id, $login_id) {
        global $config, $database_config, $prevent_multiple_registration_stages;

        // Check if student had requested for this course with the current business account.
        mysqli_select_db($config, $database_config);
        $query_CheckRequests = "SELECT `status`
                                FROM business_account_course_requests
                                WHERE biz_id = ".sql_int($biz_id)."
                                    AND course_id = ".sql_int($course_id)."
                                    AND login_id = ".sql_int($login_id)."
                                    AND status IN ('pending', 'approved')
                                LIMIT 1";
        $CheckRequests = mysqli_query($config, $query_CheckRequests) or die(mysqli_error($config));
        $row_CheckRequests = mysqli_fetch_assoc($CheckRequests);
        $totalRows_CheckRequests = mysqli_num_rows($CheckRequests);

        if ($totalRows_CheckRequests) { // Has requested the course from the business account before.
            if ($row_CheckRequests["status"] == "pending") {
                return "pending";
            }
            else { // Request had been approved
                return "unavailable";
            }
        }
        else { // Hasn't requested from the business before.
            // Check if student had requested for the course with any business account before.
            mysqli_select_db($config, $database_config);
            $query_CheckRequests = "SELECT `status`
                                    FROM business_account_course_requests
                                    WHERE course_id = ".sql_int($course_id)."
                                        AND login_id = ".sql_int($login_id)."
                                        AND status IN ('pending', 'approved')
                                    LIMIT 1";
            $CheckRequests = mysqli_query($config, $query_CheckRequests) or die(mysqli_error($config));
            $row_CheckRequests = mysqli_fetch_assoc($CheckRequests);
            $totalRows_CheckRequests = mysqli_num_rows($CheckRequests);

            if ($totalRows_CheckRequests) { // Has requested the course from the business account before.
                return "unavailable";
            }
            else {
                // Get old programme id for prog interest
                $dis_student__prog_id = get_old_programme_info($course_id, "option_id");

                // Check if this user doesn't already have the course with us.
                mysqli_select_db($config, $database_config);
                $query_userAlreadyHasCourse = "SELECT p.p_id
                                            FROM prospects p
                                            JOIN users_students us ON us.email = p.email
                                            WHERE us.login_id = ".sql_int($login_id)."
                                                AND p.prog_interest = ".sql_int($dis_student__prog_id)."
                                                AND p.stage IN (".implode(',', $prevent_multiple_registration_stages).")
                                                AND p.deleted = '0'
                                            LIMIT 1";
                                            // echo '<pre>'; var_dump($query_userAlreadyHasCourse); echo '</pre>'; exit();
                $userAlreadyHasCourse = mysqli_query($config, $query_userAlreadyHasCourse) or die(mysqli_error($config));
                $row_userAlreadyHasCourse = mysqli_fetch_assoc($userAlreadyHasCourse);
                $totalRows_userAlreadyHasCourse = mysqli_num_rows($userAlreadyHasCourse);

                if ($totalRows_userAlreadyHasCourse) { // Student is already enrolled in the programme with us.
                    return "unavailable";
                }
                else { // Student does not have the course before.
                    return false;
                }
            }
        }
    }
} /* End_ Check_for "has_student_requested_course_from_business */

if (!function_exists('encrypt_cellulant_payload')) { /* Check_for "encrypt_cellulant_payload" */
    function encrypt_cellulant_payload($payload, $country_code) {
        //The encryption method to be used
        $encrypt_method = "AES-256-CBC";

        // Hash the secret key
        $key = hash('sha256', CELLULANT_SECRET_KEY);

        // Hash the iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', CELLULANT_IV_KEY), 0, 16);
        $encrypted = openssl_encrypt(
            json_encode($payload, true), $encrypt_method, $key, 0, $iv
        );

        //Base 64 Encode the encrypted payload
        $encryptedPayload = base64_encode($encrypted);

        $result = [
            'params' => $encryptedPayload,
            'accessKey' => CELLULANT_ACCESS_KEY,
            'countryCode' => $country_code
        ];

        return json_encode($result);
    }
} /* End_ Check_for "encrypt_cellulant_payload */

if (!function_exists('get_programme_subcategory_id')) { /* Check_for "get_programme_subcategory_id" */
    function get_programme_subcategory_id($programme_id) {
        global $config, $database_config;

        // Get the subscategory ID of this programme
        mysqli_select_db($config, $database_config);
        $query_Subcategory = "SELECT op.option_id  
                            FROM option_relationships oprel 
                            JOIN options op ON oprel.option_two = op.option_id 
                            WHERE oprel.relationship_name = 'programme type' 
                                AND oprel.option_one = ".sql_int($programme_id)." 
                            LIMIT 1";
        $Subcategory = mysqli_query($config, $query_Subcategory) or die(mysqli_error($config));
        $row_Subcategory = mysqli_fetch_assoc($Subcategory);
        $totalRows_Subcategory = mysqli_num_rows($Subcategory);

        if ($totalRows_Subcategory) {
            return $row_Subcategory['option_id'];
        }

        return 0;
    }
} /* End_ Check_for "get_programme_subcategory_id */

if (!function_exists('get_training_programme_take_aways')) { /* Check_for "get_training_programme_take_aways" */
    function get_training_programme_take_aways($new_prog_id) {
        global $database_config, $config;
        
        // Cache check start
        $cache_session_id = 'get_programme_take_aways_v1';
        $target_key = md5($cache_session_id.$new_prog_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end
        
        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT op.*, oprel.option_two 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        WHERE oprel.relationship_name = 'programme take aways'  
                            AND oprel.option_one = ".sql_int($new_prog_id)." 
                        ORDER BY oprel.relationship_id ASC";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);
        
        if ($totalRows_Actions) {
            $final_value = array();
            
            do {
                $image_to_use = "";

                if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'images/uploads/'.$row_Actions['alt_option_value'])) {
                    $image_to_use = ROOT_DIR_PORTAL.'images/uploads/'.$row_Actions['alt_option_value'];
                }
                elseif (file_exists('images/uploads/'.$row_Actions['alt_option_value'])) {
                    $image_to_use = ROOT_DIR.'images/uploads/'.$row_Actions['alt_option_value'];
                }
                elseif (!empty($row_Actions['alt_option_value'])) {
                    $image_to_use = $row_Actions['alt_option_value'];
                }

                $final_value[$row_Actions['option_two']] = array(
                    "title" => $row_Actions['option_value'],
                    "image" => $image_to_use
                );
            } while($row_Actions = mysqli_fetch_assoc($Actions));
            
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, $final_value, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

            return $final_value;
        }
        else {
            // Add to cache
            add_entry_cached_in_memory($cache_session_id, $target_key, false, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

            return false;
        }
    }
} /* End_check for "get_training_programme_take_aways" */

if (!function_exists('replace_contents_containing_shortcodes')) { /* Check_for "replace_contents_containing_shortcodes" */
    function replace_contents_containing_shortcodes(string $content, array $shortcodes) {
        $replacements = prepare_shortcodes_for_regex($shortcodes);

        return preg_replace(array_keys($replacements), array_values($replacements), $content);
    }
} /* End_check for "replace_contents_containing_shortcodes" */

if (!function_exists('prepare_shortcodes_for_regex')) { /* Check_for "prepare_shortcodes_for_regex" */
    function prepare_shortcodes_for_regex(array $shortcodes) {
        $new_array = array();

        foreach ($shortcodes as $key => $value) {
            $prepared_key = "/\{\{".$key."\}\}/i";

            $new_array[$prepared_key] = $value;
        }

        return $new_array;
    }
} /* End_check for "prepare_shortcodes_for_regex" */

if (!function_exists('get_location_continent')) { /* Check_for "get_location_continent" */
    function get_location_continent($slug, $slug_is_state=false) {
        global $database_config, $config;

        mysqli_select_db($config, $database_config);
        
        if ($slug_is_state) { // Get the continent from the state URL
            $query_Actions = "SELECT myip_con.continent 
                            FROM myip_ms_states myip_state 
                            JOIN myip_ms_countries myip_con ON myip_state.countryID = myip_con.countryID 
                            WHERE myip_state.url_name = '".sql_string($slug)."' 
                            LIMIT 1";
            $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
            $row_Actions = mysqli_fetch_assoc($Actions);
            $totalRows_Actions = mysqli_num_rows($Actions);
        }
        else { // Get the continent from the country URL.
            $query_Actions = "SELECT continent 
                            FROM myip_ms_countries 
                            WHERE url_name = '".sql_string($slug)."' 
                            LIMIT 1";
            $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
            $row_Actions = mysqli_fetch_assoc($Actions);
            $totalRows_Actions = mysqli_num_rows($Actions);
        }

        if ($totalRows_Actions) {
            return $row_Actions['continent'];
        }
        else {
            return false;
        }
    }
} /* End_check for "get_location_continent" */

if (!function_exists('there_is_an_unmasked_partner_school')) { /* Check_for "there_is_an_unmasked_partner_school" */
    function there_is_an_unmasked_partner_school() {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'unmasked_partner_school';
        $target_key = md5($cache_session_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }
        // Cache check end

        $result = false;
        
        mysqli_select_db($config, $database_config);
        $query_getAPartnerSchool = "SELECT partner_id 
                                    FROM partner_schools ps 
                                    WHERE ps.banner <> '' 
                                        AND content <> '' 
                                        AND slug <> '' 
                                        AND ps.partner_status = 'current' 
                                        AND is_masked = '0' 
                                        AND ps.deleted = '0' 
                                    LIMIT 1";
        $getAPartnerSchool = mysqli_query($config, $query_getAPartnerSchool) or die(mysqli_error($config));
        $row_getAPartnerSchool = mysqli_fetch_assoc($getAPartnerSchool);
        $totalRows_getAPartnerSchool = mysqli_num_rows($getAPartnerSchool);

        if ($totalRows_getAPartnerSchool) {
            $result = true;
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $result, date("Y-m-d H:i:s", strtotime("+30 Minutes")));

        return $result;
    }
} /* End_check for "there_is_an_unmasked_partner_school" */

if (!function_exists('scholarship_time_left')) { /* Check_for "scholarship_time_left" */
    /**
     * $deadline_day should be a day in the following month. For example, 7 is the 7th day of next month.
     */
    function scholarship_time_left(Int $deadline_day=7) {
        // Cache check start
        $cache_session_id = 'scholarship_timer';
        $target_key = md5($cache_session_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        // Get the current date
        $current_date = time();

        // Get the next month
        $next_month = strtotime('next month');

        // Get the year and month of the next month
        $year = date('Y', $next_month);
        $month = date('m', $next_month);

        // Set the deadline date to the 7th day of the next month
        $deadline_date = strtotime($year . '-' . $month . '-'.$deadline_day . ' 00:00:00');

        // If the deadline date has already passed this month, add one month to it
        if ($deadline_date < $current_date) {
            $deadline_date = strtotime('+1 month', $deadline_date);
        }

        // Calculate the number of days left
        $days_left = round(($deadline_date - $current_date) / (60 * 60 * 24));

        if ($days_left > 1) {
            $days_text = 'days';
        }
        else {
            $days_text = 'day';
        }

        $result = $days_left . " ".$days_text;

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $result, date("Y-m-d 00:00:00", strtotime("+1 Day")));

        // Display the number of days left
        return $result;
    }
} /* End_check for "scholarship_time_left" */

if (!function_exists('compareArraySortingValues')) { /* Check_for "compareArraySortingValues" */
    function compareArraySortingValues($a, $b) {
        return $b["sorting"] - $a["sorting"];
    }
} /* End_check for "compareArraySortingValues" */

if (!function_exists('formatPhoneNumber')) { /* Check_for "formatPhoneNumber" */
    function formatPhoneNumber($phoneNumber) {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    
        // Add the plus sign at the beginning
        $formattedPhoneNumber = '+' . $phoneNumber;
    
        // Define the country code and format rules
        $formats = [
            '234' => [3, 3, 4], // Nigeria
            '44'  => [2, 4, 4], // UK
            '971' => [3, 3, 4], // UAE
            '92'  => [3, 3, 4]  // Pakistan
        ];
    
        // Find the matching format rule
        $format = null;
        foreach ($formats as $code => $rule) {
            if (strncmp($phoneNumber, $code, strlen($code)) === 0) {
                $format = $rule;
                break;
            }
        }
    
        // If no matching format rule is found, return the original phone number
        if ($format === null) {
            return $formattedPhoneNumber;
        }
    
        // Apply the format rule
        $position = 1 + strlen($code);
        foreach ($format as $chunkSize) {
            $formattedPhoneNumber = substr_replace($formattedPhoneNumber, ' ', $position, 0);
            $position += $chunkSize + 1;
        }
    
        return $formattedPhoneNumber;
    }
    
} /* End_check for "formatPhoneNumber" */

if (!function_exists('subjectHasSubMenu')) { /* Check_for "subjectHasSubMenu */
    /**
     * Checks if a subject has at lease 3 sub-subjects that have been attached to a programme. 
     * If the criteria is met, the ID of the parent subject is returned while false is returned if the criteria is not met.
     */
    function subjectHasSubMenu($slug) {
        global $database_config, $config;

        // Cache check start
        $cache_session_id = 'subject_sub_menu_v1';
        $target_key = md5($cache_session_id.$slug);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $result = false;

        mysqli_select_db($config, $database_config);
        $query_ParentSubject = "SELECT op.option_id 
                                FROM options op
                                WHERE op.option_name = 'programme subjects' 
                                    AND op.status = 'active' 
                                    AND op.url_name = '".sql_string($slug)."' 
                                LIMIT 1";
        $ParentSubject = mysqli_query($config, $query_ParentSubject) or die(mysqli_error($config));
        $row_ParentSubject = mysqli_fetch_assoc($ParentSubject);
        $totalRows_ParentSubject = mysqli_num_rows($ParentSubject);

        if ($totalRows_ParentSubject) {
            // Get all programme sub subjects.
            mysqli_select_db($config, $database_config);
            $query_CourseSubjects = "SELECT op.*
                                    FROM options op
                                    WHERE op.option_name = 'programme sub subjects' 
                                        AND op.parent_option_id = ".sql_int($row_ParentSubject['option_id'])."
                                        AND op.status = 'active' 
                                        AND op.deleted = '0' 
                                        AND EXISTS (
                                            SELECT oprel.option_two 
                                            FROM option_relationships oprel 
                                            WHERE oprel.relationship_name = 'programme sub subjects' 
                                                AND oprel.option_two = op.option_id 
                                        )
                                    LIMIT 3";
            $CourseSubjects = mysqli_query($config, $query_CourseSubjects) or die(mysqli_error($config));
            $row_CourseSubjects = mysqli_fetch_assoc($CourseSubjects);
            $totalRows_CourseSubjects = mysqli_num_rows($CourseSubjects);

            if ($totalRows_CourseSubjects && ($totalRows_CourseSubjects == 3)) {
                $result = $row_ParentSubject['option_id'];
            }
        }

        add_entry_cached_in_memory($cache_session_id, $target_key, $result, date("Y-m-d H:i:s", strtotime("+1 hour")));

        return $result;
    }
} /* End_check for "subjectHasSubMenu" */

if (!function_exists('getSubjectFeatureProgramme')) { /* Check_for "getSubjectFeatureProgramme */
    function getSubjectFeatureProgramme($query_type, $subject_of_focus="") {
        global $database_config, $config, $db_config_file_dir;

        $cache_session_id = 'get_subject_feature_progs';
        $target_key = md5($cache_session_id.$query_type.$subject_of_focus);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $prog_box_display_type = $query_type;
        $prog_query_limit = "LIMIT 1";

        if ($query_type != 'training') {
            $prog_query_order_by = "ORDER BY RAND()";
        }

        if ($subject_of_focus) {
            $prog_query_where_additional = "AND np.programme_id IN (
                                                SELECT np.programme_id  
                                                FROM option_relationships option_prog_subject 
                                                JOIN options op_subject ON option_prog_subject.option_two = op_subject.option_id 
                                                JOIN new_programmes np ON option_prog_subject.option_one  = np.programme_id 
                                                WHERE option_prog_subject.relationship_name = 'programme subjects' 
                                                    AND op_subject.url_name = '".sql_string($subject_of_focus)."'  
                                                    AND np.deleted = '0'
                                            )";
        }

        if ($prog_box_display_type == 'training') {
            $target_date_period = date('Y-m-d');
            $prog_query_where_additional .= "AND pd.start_date > '".$target_date_period."'";
            $prog_query_order_by = "ORDER BY pd.start_date ASC, myctry.countryName DESC";
        }

        if ($db_config_file_dir) { // Use this to try to locate file that holds the cache fingerprint variable.
            if (defined("FILE_PATH_TO_UK")) {
                $prog_query_include_file = FILE_PATH_TO_UK.'inc.programme-query.php';
            }
            else {
                $prog_query_include_file = 'inc.programme-query.php';
            }

            foreach ($db_config_file_dir as $db_config_file_path) {
                if (@file_exists($db_config_file_path.$prog_query_include_file)) {
                    if ($req_file_contents = @file_get_contents($db_config_file_path.$prog_query_include_file)) { // If the file's content can be read
                        include($db_config_file_path.$prog_query_include_file);
                        break;
                    }
                }
            }
        }

        if (!$totalRows_DisProg) { // If the query in the programme-query.php file does not have a result
            if ($subject_of_focus) { // Fallback incase the subject does not have a programme. Just use a programme category instead so that the featured programme segment does not remain empty.
                getSubjectFeatureProgramme($query_type);
            }
            else { // return false.
                $row_DisProg = false;
            }
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $row_DisProg, date("Y-m-d H:i:s", strtotime("+ 1 hour")));

        return $row_DisProg;
    }
} /* End_check for "getSubjectFeatureProgramme" */

if (!function_exists('getSubjectChildren')) { /* Check_for "getSubjectChildren */
    function getSubjectChildren($parent_subject_id) {
        global $database_config, $config;

        $cache_session_id = 'get_subject_children';
        $target_key = md5($cache_session_id.$parent_subject_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $subject_content = array();

        // Get all sub-subjects that have at least a programme
        mysqli_select_db($config, $database_config);
        $query_CourseSubjects = "SELECT op.*
                                FROM options op
                                WHERE op.option_name = 'programme sub subjects' 
                                    AND op.parent_option_id = ".sql_int($parent_subject_id)." 
                                    AND op.status = 'active' 
                                    AND op.deleted = '0' 
                                    AND EXISTS (
                                        SELECT oprel.option_two 
                                        FROM option_relationships oprel 
                                        WHERE oprel.relationship_name = 'programme sub subjects' 
                                            AND oprel.option_two = op.option_id 
                                    )
                                LIMIT 10";
        $CourseSubjects = mysqli_query($config, $query_CourseSubjects) or die(mysqli_error($config));
        $row_CourseSubjects = mysqli_fetch_assoc($CourseSubjects);
        $totalRows_CourseSubjects = mysqli_num_rows($CourseSubjects);

        if ($totalRows_CourseSubjects) {
            do {
                $subject_content[] = $row_CourseSubjects;
            } while ($row_CourseSubjects = mysqli_fetch_assoc($CourseSubjects));
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $subject_content, date("Y-m-d H:i:s", strtotime("+1 hour")));

        return $subject_content;
    }
} /* End_check for "getSubjectChildren" */

if (!function_exists('subject_total_courses')) { /* Check_for "subject_total_courses" */
    function subject_total_courses($subject_url, $category='all') {
        global $database_config, $config;

        $cache_session_id = 'subject_total_courses_v2';
        $target_key = md5($cache_session_id.$subject_url.$category);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $totaL_programmes_in_subject = 0;

        switch ($category) {
            case 'pcc':
                $category_to_check = " AND op_other.option_id IN (409)";
                break;
            case 'apcc':
                $category_to_check = " AND op_other.option_id IN (883)";
                break;
            case 'masters':
                $category_to_check = " AND op_other.option_id IN (406)";
                break;
            case 'ipgd':
                $category_to_check = " AND op_other.option_id IN (880)";
                break;
            default:
                $category_to_check = " AND op_other.option_id IN (409, 883, 406, 880)"; // PCC, APCC, Masters and IPGD
                break;
        }

        mysqli_select_db($config, $database_config);
        $query_Subject = "SELECT * 
                        FROM options
                        WHERE option_name = 'programme subjects' 
                            AND url_name = '".sql_string($subject_url)."' 
                        LIMIT 1";
        $Subject = mysqli_query($config, $query_Subject) or die(mysqli_error($config));
        $row_Subject = mysqli_fetch_assoc($Subject);
        $totalRows_Subject = mysqli_num_rows($Subject);

        if ($totalRows_Subject) {
            mysqli_select_db($config, $database_config);
            $query_ProgrammeCount = "SELECT COUNT(oprel.option_one) as total_progs
                                    FROM option_relationships oprel 
                                    JOIN options op ON oprel.option_two = op.option_id 
                                    JOIN option_relationships option_prog_other ON option_prog_other.option_one = oprel.option_one 
                                    JOIN options op_other ON option_prog_other.option_two = op_other.option_id 
                                    WHERE oprel.relationship_name = 'programme subjects' 
                                        AND option_prog_other.relationship_name = 'programme type' 
                                        AND oprel.option_two = ".sql_int($row_Subject['option_id'])." 
                                        ".$category_to_check."
                                        AND op_other.status = 'active'";
            $ProgrammeCount = mysqli_query($config, $query_ProgrammeCount) or die(mysqli_error($config));
            $row_ProgrammeCount = mysqli_fetch_assoc($ProgrammeCount);
            $totalRows_ProgrammeCount = mysqli_num_rows($ProgrammeCount);

            $totaL_programmes_in_subject = $row_ProgrammeCount['total_progs'];

            add_entry_cached_in_memory($cache_session_id, $target_key, $totaL_programmes_in_subject, date("Y-m-d H:i:s", strtotime("+1 hour")));            
        }

        return $totaL_programmes_in_subject;
    }
} /* End_check for "subject_total_courses" */

if (!function_exists('find_active_student_programme_subscription')) { /* Check_for "find_active_student_programme_subscription */
    function find_active_student_programme_subscription($programme_id) {
        global $database_config, $config, $sess_user_id, $date_now;

        $cache_session_id = 'find_active_student_programme_subscription';
        $target_key = md5($cache_session_id.$sess_user_id.$programme_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $subscription_content = array();

        // Check for the student's active subscriptions
        mysqli_select_db($config, $database_config);
        $query_ActiveSubscription = "SELECT *
                                    FROM users_subscriptions 
                                    WHERE login_id = ".sql_int($sess_user_id)." 
                                        AND sub_status = 'active' 
                                        AND end_date > '".sql_string($date_now)."'";
        $ActiveSubscription = mysqli_query($config, $query_ActiveSubscription) or die(mysqli_error($config));
        $row_ActiveSubscription = mysqli_fetch_assoc($ActiveSubscription);
        $totalRows_ActiveSubscription = mysqli_num_rows($ActiveSubscription);

        if ($totalRows_ActiveSubscription) {
            // Check for the target programme in the list of programmes of each subscription
            do {
                mysqli_select_db($config, $database_config);
                $query_SubscriptionProgramme = "SELECT *
                                                FROM subscriptions_programmes  
                                                WHERE sub_id = ".sql_int($row_ActiveSubscription['sub_id'])." 
                                                    AND prog_id IN (".sql_int($programme_id).", 7233000)
                                                LIMIT 1";
                $SubscriptionProgramme = mysqli_query($config, $query_SubscriptionProgramme) or die(mysqli_error($config));
                $row_SubscriptionProgramme = mysqli_fetch_assoc($SubscriptionProgramme);
                $totalRows_SubscriptionProgramme = mysqli_num_rows($SubscriptionProgramme);

                if ($totalRows_SubscriptionProgramme) {
                    $subscription_content[$programme_id] = $row_ActiveSubscription['user_sub_id'];
                    
                    break; // Stop the loop.
                }
            } while ($row_ActiveSubscription = mysqli_fetch_assoc($ActiveSubscription));
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $subscription_content, date("Y-m-d H:i:s", strtotime("+10 minutes")));

        return $subscription_content;
    }
} /* End_check for "find_active_student_programme_subscription" */

if (!function_exists('is_student_and_has_programme_subscription')) { /* Check_for "is_student_and_has_programme_subscription */
    function is_student_and_has_programme_subscription($programme_id) {
        global $sess_user_id;

        // Must be signed in as a student
        if (($sess_user_id > 0) && isset($_SESSION['user_type']) &&  in_array($_SESSION['user_type'], array('student'))) {
            $subscription_content = find_active_student_programme_subscription($programme_id);
            
            // Is the programme covered by any subscription that the student has
            if (is_array($subscription_content) && count($subscription_content)) {
                return true;
            }
        }

        return false;
    }
} /* End_check for "is_student_and_has_programme_subscription" */

if (!function_exists('has_already_subscribed')) { /* Check_for "has_already_subscribed */
    function has_already_subscribed($login_id, $old_programme_id) {
        global $database_config, $config;

        $cache_session_id = 'has_already_subscribed';
        $target_key = md5($cache_session_id.$login_id.$old_programme_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $status = false;

        mysqli_select_db($config, $database_config);
        $query_Actions = "SELECT p.p_id 
                        FROM prospects p 
                        JOIN users_students_prospect usp ON p.p_id = usp.p_id
                        WHERE p.deleted = '0' 
                              AND p.prog_interest = '".sql_int($old_programme_id)."' 
                              AND usp.login_id = '".sql_int($login_id)."' 
                              AND usp.user_sub_id <> 0 
                        LIMIT 1";
        $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
        $row_Actions = mysqli_fetch_assoc($Actions);
        $totalRows_Actions = mysqli_num_rows($Actions);

        if ($totalRows_Actions) {
            $status = true;
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $status, date("Y-m-d H:i:s", strtotime("+5 minutes")));

        return $status;
    }
} /* End_check for "has_already_subscribed" */

if (!function_exists('get_subscription_courses_breakdown')) {
    function get_subscription_courses_breakdown(string $sub_id) {
        global $database_config, $config;

        $cache_session_id = 'get_subscription_courses_breakdown';
        $target_key = md5($cache_session_id.$sub_id);
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
        if ($cached_value !== NULL) {
            return $cached_value;
        }

        $subscription_is_for_all_programmes = false;
        $all_subscription_programmes = array();
        $final_return_value = array();

        // Get the programmes of this subscription.
        mysqli_select_db($config, $database_config);
        $query_SubscriptionProgrammes = "SELECT * 
                                        FROM subscriptions_programmes 
                                        WHERE sub_id = ".sql_int($sub_id)."";
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

        // Prepare the condition for targeting specific subjects
        $prog_query_limit = "LIMIT 4000";
    
        if (!$subscription_is_for_all_programmes && count($all_subscription_programmes)) { // The subscription is not for all programmes, filter the query to only show the selected programmes.
            $prog_query_where_additional = "AND np.programme_id IN (".implode(", ", $all_subscription_programmes).")";

            $possible_courses = [
                [
                    "type"         => "short course",
                    "heading"      => '<span class="d-sm-inline-block d-none">Professional</span> Certificate',
                    "accordion_id" => "pcc"
                ],
                [
                    "type"         => "advanced professional",
                    "heading"      => '<span class="d-sm-inline-block d-none">Advanced Professional</span> Certificate',
                    "accordion_id" => "apcc"
                ],
                [
                    "type"         => "semp online",
                    "heading"      => '<span class="d-sm-inline-block d-none">Special Executive</span> Masters',
                    "accordion_id" => "sempon"
                ],
                [
                    "type"         => "masters",
                    "heading"      => '<span class="d-sm-inline-block d-none">Master\'s</span> Degree',
                    "accordion_id" => "masters"
                ],
                [
                    "type"         => "postgraduate diploma",
                    "heading"      => '<span class="d-sm-inline-block d-none">Postgraduate</span> Diploma',
                    "accordion_id" => "pgdip"
                ],
                [
                    "type"         => "intl diploma",
                    "heading"      => '<span class="d-sm-inline-block d-none">International</span> Postgraduate Diploma',
                    "accordion_id" => "intdip"
                ],
                [
                    "type"         => "bachelors",
                    "heading"      => '<span class="d-sm-inline-block d-none">Bachelor\'s</span> Degree',
                    "accordion_id" => "bachelors"
                ],
                [
                    "type"         => "diplomas regulated",
                    "heading"      => '<span class="d-sm-inline-block d-none">Diploma</span>',
                    "accordion_id" => "dipreg"
                ],
                [
                    "type"         => "intl higher diploma",
                    "heading"      => '<span class="d-sm-inline-block d-none">International</span> Higher Diploma',
                    "accordion_id" => "inthighdip"
                ],
                [
                    "type"         => "doctorate",
                    "heading"      => '<span class="d-sm-inline-block d-none">Doctorate</span> Degree ',
                    "accordion_id" => "doctorate"
                ],
                [
                    "type"         => "postgraduate research",
                    "heading"      => '<span class="d-sm-inline-block d-none">Postgraduate</span> Research',
                    "accordion_id" => "pgres"
                ],
            ];
        }

        foreach ($possible_courses as $current_category) {// Get the matching programmes
            $prog_query_preserve_params_for_next_run = true;
            $prog_box_display_type = $current_category['type'];

            include('inc.programme-query.php');

            if ($totalRows_DisProg) {
                $final_return_value[] = [
                    'course_type' => ucwords(strip_tags($current_category['heading'])), 
                    'num_of_courses' => $totalRows_DisProg, 
                    'multiples_wording' => ($totalRows_DisProg > 1) ? ' Courses' : ' Course', 
                ];
            }
        }

        // Add to cache
        add_entry_cached_in_memory($cache_session_id, $target_key, $final_return_value, date("Y-m-d H:i:s", strtotime("+3 hours")));

        return $final_return_value;
    }
}

// Safe show_amount function (PHP 8+ compatible)
function show_amount($amount) {
    // Remove commas, ensure float
    $float_amount = is_numeric(str_replace(',', '', $amount)) ? (float) str_replace(',', '', $amount) : 0.0;
    return number_format($float_amount, 2);
}