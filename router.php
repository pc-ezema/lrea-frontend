<?php 
require_once('connections/config.php');
require_once('components/misc.inc.php');

record_router_debug_action("Began router execution");

//echo '<pre>'; var_dump($_GET); echo '</pre>'; 

// Before anything, check for the existence of the cookie disable_web_params
if (isset($_COOKIE['disable_web_params'])) {
    redirect(ROOT_DIR_PORTAL."?mapwa=v1");
}
elseif (isset($_COOKIE['disable_web_footer'])) {
    define("DISABLE_WEB_FOOTER", 'true');
}

if (isset($_GET['utm_source']) || isset($_GET['utm_medium'])) {
    $there_is_ad_tracking = true;    
    $_SESSION['location_of_choice'] = 'united-kingdom';
    setcookie($location_cookie_name, 'united-kingdom', $alt_cookie_lifetime, "/", '.'.SITE_NAME_LOWER);
}

// Array od URLs with first part only
$arrayOfUrlRedirects = array(
    "lrea-uae" => "http://lrea.ae/",
    "lrea-nigeria" => "http://lrea.edu.ng/",
    "mba-advert" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-uk.php",
    "mba" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-uk.php",
    "masters" => "https://".SITE_NAME_LOWER."/category/masters-degrees",
    "semp" => "https://".SITE_NAME_LOWER."/category/executive-masters-online",
    "diploma" => "https://".SITE_NAME_LOWER."/category/international-postgraduate-diplomas",
    "sempmba" => "https://".SITE_NAME_LOWER."/uae/training/special-executive-master-programme-in-business-administration-semp-mba-in-dubai",
    "one-week-mba" => "https://".SITE_NAME_LOWER."/uae/training/special-executive-master-programme-in-business-administration-semp-mba-in-dubai",
    "semp-mba" => "https://".SITE_NAME_LOWER."/uae/training/special-executive-master-programme-in-business-administration-semp-mba-in-dubai",
    "5-day-MBA" => "https://".SITE_NAME_LOWER."/uae/training/special-executive-master-programme-in-business-administration-semp-mba-in-dubai",
    "5dayMBA" => "https://".SITE_NAME_LOWER."/uae/training/special-executive-master-programme-in-business-administration-semp-mba-in-dubai",
);

// Array od URLs with first and second parts
$arrayOfUrlWithPrefixRedirects = array(
    "programmes" => array(
        "request-information-bscmba" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-uk.php",
        "request-information-bscmba-uae" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-uae.php",
        "request-information-bscmba-nigeria" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-nigeria.php",
        "semp-intl-bus-uk" => "http://".SITE_NAME_LOWER."/landing-page/semp-intl-bus-uk.php",
        "request-information" => "http://e.lreaonline.com/wp/request-information/",
    ),
    "landing-page" => array(
        "othm-degree.php" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-uk.php",
        "othm-degree-nigeria.php" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-nigeria.php",
        "lsm-degree.php" => "http://".SITE_NAME_LOWER."/landing-page/mba-degree-uk.php",
        "semp-llm" => "https://".SITE_NAME_LOWER."/uae/trainings",
        "one-week-llm" => "https://".SITE_NAME_LOWER."/uae/trainings",
    ),
);

// If first part exists, perform redirect
if ($arrayOfUrlRedirects[$_GET['part_1']]) {
    // Permanent 301 redirection
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$arrayOfUrlRedirects[$_GET['part_1']]);
    exit();
}

// If second part exists, check before performing redirect
if ($_GET['part_2']) {
    if ($arrayOfUrlWithPrefixRedirects[$_GET['part_1']]) {
        if ($arrayOfUrlWithPrefixRedirects[$_GET['part_1']][$_GET['part_2']]) {
            // Permanent 301 redirection
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$arrayOfUrlWithPrefixRedirects[$_GET['part_1']][$_GET['part_2']]);
            exit();
        }
    }
}

// Initialize variables
$coresite_curr_location_name_full = '';
$coresite_curr_country_name = '';
$coresite_curr_state_name = '';

$coresite_curr_country_slug = '';
$coresite_curr_state_slug = '';
$location_slug = '';

$coresite_page_segment_1_slug = '';
$coresite_page_segment_2_slug = '';
$coresite_page_segment_3_slug = ''; // This will only have a value if it is subject sub-topic

// Process what type of link was requested
if (isset($_GET['part_4'])) {
    if ($coresite_curr_country_name = is_country($_GET['part_1']) && $coresite_curr_state_name = is_state($_GET['part_2'])) {
        $coresite_curr_country_slug = $_GET['part_1'];
        $coresite_curr_state_slug = $_GET['part_2'];
        $coresite_page_segment_1_slug = $_GET['part_3'];
        $coresite_page_segment_2_slug = $_GET['part_4'];

        redirect(ROOT_DIR.$coresite_curr_state_slug.'/'.$coresite_page_segment_1_slug.'/'.$coresite_page_segment_2_slug);
    }
    else { // For the sake of the new subject menu that could have four parts e.g https://domain/nigeria/subject/business-law/ethics-of-law-and-order
        if (($coresite_curr_country_name = is_country($_GET['part_1'])) && ($_GET['part_2'] == "subject")) {
            $coresite_curr_country_slug = $_GET['part_1'];
            $coresite_page_segment_1_slug = $_GET['part_2'];
            $coresite_page_segment_2_slug = $_GET['part_3'];
            $coresite_page_segment_3_slug = $_GET['part_4'];
        }
    }
}
elseif (isset($_GET['part_3'])) {
    if ($coresite_curr_country_name = is_country($_GET['part_1'])) {
        if ($coresite_curr_state_name = is_state($_GET['part_2'])) {
            $coresite_curr_country_slug = $_GET['part_1'];
            $coresite_curr_state_slug = $_GET['part_2'];
            $coresite_page_segment_1_slug = $_GET['part_3'];

            redirect(ROOT_DIR.$coresite_curr_state_slug.'/'.$coresite_page_segment_1_slug);
        }
        else {
            $coresite_curr_country_slug = $_GET['part_1'];
            $coresite_page_segment_1_slug = $_GET['part_2'];
            $coresite_page_segment_2_slug = $_GET['part_3'];
        }
    }
    elseif ($coresite_curr_state_name = is_state($_GET['part_1'])) {
        // Generate state specific page
        $coresite_curr_state_slug = $_GET['part_1'];
        $coresite_page_segment_1_slug = $_GET['part_2'];
        $coresite_page_segment_2_slug = $_GET['part_3'];
    }
    else { // For the sake of the new subject menu that could have three parts e.g https://domain/subject/business-law/ethics-of-law-and-order
        if ($_GET['part_1'] == "subject") {
            $coresite_page_segment_1_slug = $_GET['part_1'];
            $coresite_page_segment_2_slug = $_GET['part_2'];
            $coresite_page_segment_3_slug = $_GET['part_3'];
        }
    }
}
elseif ($_GET['part_2']) {
    if ($coresite_curr_country_name = is_country($_GET['part_1'])) {
        if ($coresite_curr_state_name = is_state($_GET['part_2'])) {
            $coresite_curr_country_slug = $_GET['part_1'];
            $coresite_curr_state_slug = $_GET['part_2'];

            redirect(ROOT_DIR.$coresite_curr_state_slug);
        }
        else {
            $coresite_curr_country_slug = $_GET['part_1'];
            $coresite_page_segment_1_slug = $_GET['part_2'];
        }
    }
    elseif ($coresite_curr_state_name = is_state($_GET['part_1'])) {
        // Generate state specific page
        $coresite_curr_state_slug = $_GET['part_1'];
        $coresite_page_segment_1_slug = $_GET['part_2'];
    }
    else {
        $coresite_page_segment_1_slug = $_GET['part_1'];
        $coresite_page_segment_2_slug = $_GET['part_2'];
    }
}
elseif ($_GET['part_1']) {
    if ($coresite_curr_country_name = is_country($_GET['part_1'])) {
        $coresite_curr_country_slug = $_GET['part_1'];
    }
    elseif ($coresite_curr_state_name = is_state($_GET['part_1'])) {
        // Generate state specific page
        $coresite_curr_state_slug = $_GET['part_1'];
    }
    else {
        $coresite_page_segment_1_slug = $_GET['part_1'];
    }
}

if (count($_GET)) { // Prepare the values to be attached to the modified url
    $dis_query_string_parts = array();
    $there_is_cross_domain_session = false;

    foreach ($_GET as $key => $value) { 
        if (in_array($key, array('our_trackvisit', 'our_uuid'))) {
            $there_is_cross_domain_session = true;
        }

        if (!preg_match('/^part_/i', $key) && (!in_array($key, array('our_trackvisit', 'our_uuid')))) {
            $dis_query_string_parts[] = $key.'='.$value;
        }
    }

    if (count($dis_query_string_parts)) {
        $dis_query_string = '?'.implode('&', $dis_query_string_parts);
    }
}

if ($coresite_page_segment_1_slug && $coresite_page_segment_2_slug) {
    // This is for knowing which page to redirect to when visitor switches country
    $coresite_page_segment_slug_combined = $coresite_page_segment_1_slug.'/'.$coresite_page_segment_2_slug;
}
elseif ($coresite_page_segment_1_slug) {
    // This is for knowing which page to redirect to when visitor switches country
    $coresite_page_segment_slug_combined = $coresite_page_segment_1_slug;
}
else {
    $coresite_page_segment_slug_combined = '';
}

if (!$there_is_ad_tracking) {
    // Try to make sure that the user's last country of preference is noted
    if ($_SESSION['location_of_choice']) {
        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                setcookie($location_cookie_name, $_SESSION['location_of_choice'], $alt_cookie_lifetime, "/", $uniqueDomainRoot);
            }
        }
    }
    elseif ($_COOKIE[$location_cookie_name]) {
        $_SESSION['location_of_choice'] = $_COOKIE[$location_cookie_name];
    }

    if ($coresite_curr_country_slug == 'united-kingdom') { record_router_debug_action("Permanent redirect to remove UK from the URL");
        $_SESSION['location_of_choice'] = 'united-kingdom';

        // Permanent 301 redirection
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".ROOT_DIR.$coresite_page_segment_slug_combined.$dis_query_string);
        exit();
    }
}

if ($overwrite_domain && $_GET['part_1']) {
    $perform_seo_correction = false;

    if ($coresite_curr_state_slug) {
        $this_state_country_slug = get_state_country($coresite_curr_state_slug, 'url_name');

        if ($coresite_curr_country_slug !== $this_state_country_slug) {
            $perform_seo_correction = true;
        }
    }
    elseif (is_country($_GET['part_1'])) {
        if ($coresite_curr_country_slug !== $_GET['part_1']) {
            $perform_seo_correction = true;
        }
    }

    if ($perform_seo_correction) {
        // Prepare the slugs
        $prepared_redirect_slugs = '';
        foreach (array('part_1', 'part_2', 'part_3', 'part_4') as $value) {
            if ($_GET[$value]) {
                $prepared_redirect_slugs .= $_GET[$value].'/';
            }
        }

        // Permanent 301 redirection
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: https://".SITE_NAME_LOWER."/".$prepared_redirect_slugs.$dis_query_string);
        exit();
    }
}

// IP address domain switching
if (!$_SESSION['location_of_choice'] && ($_GET['part_1'] != 'ads') && !$there_is_ad_tracking && true == false) { // Currently disabled
    $current_ip_addr = get_real_ip_addr(); // get the user's current IP address
    // $current_ip_addr = '88.208.221.33'; // UK IP Address for testing
    // $current_ip_addr = '154.113.66.206'; // Nigeria IP Address for testing
    
    // The below check to validate the IP Address passed is necessary to avoid unnecessary api and database calls.
    if (preg_match('/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/i',$current_ip_addr)) {
        $geoplugin = our_geoplugin_details($current_ip_addr);

        // Check their current country of interest i.e. the current country that the visitor is viewing
        $currentCountryOfInterest = $coresite_curr_country_slug ? $coresite_curr_country_slug : 'united-kingdom';
        $current_country_code = "";

        if ($geoplugin['geoplugin_countryCode']) {
            $current_country_code = $geoplugin['geoplugin_countryCode'];
        }
        elseif ($geoplugin['country_code']) {
            $current_country_code = $geoplugin['country_code'];
        }

        if ($current_country_code) {
            mysqli_select_db($config, $database_config);
            $query_ipAddressCountryInfo = "SELECT url_name
                                        FROM myip_ms_countries 
                                        WHERE webCode = '".$current_country_code."' 
                                        LIMIT 1";
            $ipAddressCountryInfo = mysqli_query($config, $query_ipAddressCountryInfo) or die(mysqli_error($config));
            $row_ipAddressCountryInfo = mysqli_fetch_assoc($ipAddressCountryInfo);
            $totalRows_ipAddressCountryInfo = mysqli_num_rows($ipAddressCountryInfo);
            
            if ($totalRows_ipAddressCountryInfo) {
                // Check if the country they are viewing is not already the current they are in
                // i.e. it only matters if the country they are in is different from the country they are viewing.
                if ($currentCountryOfInterest != $row_ipAddressCountryInfo['url_name']) { 
                    $countryToRedirectTo = $row_ipAddressCountryInfo['url_name'];
                    
                    // Permanent 301 redirection to the country their IP address suggests they are in.
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".ROOT_DIR.$countryToRedirectTo.'/'.$coresite_page_segment_slug_combined.$dis_query_string);
                    exit();
                }
            } 
        } 
    }
}

record_router_debug_action("About to prepare currency details");

// Prepare country-specific info
if ($coresite_curr_state_slug) {
    if (!$coresite_curr_state_name) {
        $coresite_curr_state_name = is_state($coresite_curr_state_slug);
    }

    if (!$coresite_curr_country_name) {
        $coresite_curr_country_name = get_state_country($coresite_curr_state_slug, 'countryName');
    }

    if (!$coresite_curr_location_name_full) {
        $coresite_curr_location_name_full = $coresite_curr_state_name.', '.$coresite_curr_country_name;
    }

    $_SESSION['location_of_choice'] = $coresite_curr_state_slug;
    $location_slug = $coresite_curr_state_slug.'/';

    // START PROCESSING CURRENCY
    $coresite_curr_currency = get_state_currency($_SESSION['location_of_choice']);
    include('inc.currency-processing.php');
    // END PROCESSING CURRENCY

    $curr_branch_and_study_center = get_curr_branch_and_study_center($coresite_curr_state_slug); // Returns an array
}
elseif ($coresite_curr_country_slug) {
    if (!$coresite_curr_country_name) {
        $coresite_curr_country_name = is_country($coresite_curr_country_slug);
    }

    if (!$coresite_curr_location_name_full) {
        $coresite_curr_location_name_full = $coresite_curr_country_name;
    }

    $_SESSION['location_of_choice'] = $coresite_curr_country_slug;

    if (!$overwrite_domain) { // If the domain for this location has NOT been changed from UK
        $location_slug = $coresite_curr_country_slug.'/';
    }

    // START PROCESSING CURRENCY
    $coresite_curr_currency = get_country_currency($_SESSION['location_of_choice']);
    include('inc.currency-processing.php');
    // END PROCESSING CURRENCY

    $curr_branch_and_study_center = get_curr_branch_and_study_center('', $coresite_curr_country_slug); // Returns an array
}
else {
    if (!$_SESSION['location_of_choice'] || $_SESSION['location_of_choice'] == 'united-kingdom') {
        $coresite_curr_location_name_full = 'London, United Kingdom';
        $coresite_curr_country_name = 'United Kingdom';
        $coresite_curr_state_name = 'London';

        //$_SESSION['location_of_choice'] = 'united-kingdom';
    
        // START PROCESSING CURRENCY
        $coresite_curr_currency = get_country_currency('united-kingdom');
        include('inc.currency-processing.php');
        // END PROCESSING CURRENCY

        $curr_branch_and_study_center = get_curr_branch_and_study_center('london', 'united-kingdom'); // Returns an array
    }
    elseif ($_SESSION['location_of_choice'] && $_SESSION['location_of_choice'] != 'united-kingdom') {
        $dis_query_string ="";
        
        if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
            for ($i = 1; $i <= 6; $i++) { // Remove seo url segments but leave other GET values
                $targeted_part = 'part_'.$i;

                if (array_key_exists($targeted_part, $_GET)) {
                    unset($_GET[$targeted_part]); 
                }
            }
        } 
        
        if (!count($_POST)) { // Only if no form was submitted
            if ($coresite_page_segment_2_slug) {
                $coresite_page_complete_url_slugs = ROOT_DIR.$_SESSION['location_of_choice'].'/'.$coresite_page_segment_1_slug.'/'.$coresite_page_segment_2_slug;
            }
            elseif ($coresite_page_segment_1_slug) {
                $coresite_page_complete_url_slugs = ROOT_DIR.$_SESSION['location_of_choice'].'/'.$coresite_page_segment_1_slug;
            }
            else {
                $coresite_page_complete_url_slugs = ROOT_DIR.$_SESSION['location_of_choice'];
            }

            // At this point, the $dis_query_string is empty. We need to populate it with the rest of the contents in the $_GET variable.
            if (count($_GET)) { 
                $available_get_values = array();

                foreach ($_GET as $key => $value) {
                    $available_get_values[] = $key.'='.$value;
                }

                if (count($available_get_values)) {
                    $dis_query_string = implode("&", $available_get_values);
                }
            }

            // Show the visitor the version for the previous chosen locations
            redirect(append_to_url($coresite_page_complete_url_slugs, $dis_query_string));
        }
    }
}

record_router_debug_action("Finished processing currency details");

function my_is_https() {
    // Based off https://stackoverflow.com/a/16076965
    // because the standard conditions [$_SERVER['HTTPS'] == 'on'] do not work on servers behind a load balancer

    $isSecure = false;

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    }
    elseif ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) {
        $isSecure = true;
    }

    return $isSecure;
}

if (!$there_is_ad_tracking) {
    // Redirecting to other domain where applicable
    if (!$overwrite_domain && $site_version == 'online') { // If the domain for this location has NOT been changed from UK
        // TIP: Don't compare locations below using "msbm_curr_country_slug" because that variable won't exist for states

        $state_related_url_segment = $coresite_curr_state_slug ? $coresite_curr_state_slug.'/' : '';

        $domainSwitchBaseURL = '';
        
        if ($coresite_curr_country_name == 'Nigeria') { // But the country expected is Nigeria
            // $domainSwitchBaseURL = 'https://lrea.edu.ng/';
        }
        elseif ($coresite_curr_country_name == 'United Arab Emirates') { // But the country expected is United Arab Emirates
            // $domainSwitchBaseURL = 'https://lrea.ae/';
        }
        elseif ($coresite_curr_country_name == 'USA') { // But the country expected is USA
            // $domainSwitchBaseURL = 'https://lrea.us/';
        }

        if ($domainSwitchBaseURL) { record_router_debug_action("Permanent redirect to alternative domain");
            // Permanent 301 redirection
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$domainSwitchBaseURL.$state_related_url_segment.$coresite_page_segment_slug_combined.$dis_query_string);
            exit();
        }
    }

    record_router_debug_action("Checking if HTTPS request");

    // Force https or redirect after stripping cross-domain values from url
    if (($site_version == 'online' && !my_is_https()) || $there_is_cross_domain_session) { record_router_debug_action("Permanent redirect to HTTPS version of domain");
        // Permanent 301 redirection
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".ROOT_DIR.$coresite_page_segment_slug_combined.$dis_query_string);
        exit();
    }

    record_router_debug_action("Checking for cookies from other domain");

    // Manage cookies from the previous domain
    if (array_key_exists(COOKIE_SITE_NAME, $our_unique_site_domains)) {
        $should_page_redirect_after_setting_cookies = false;

        // Set the affiliate cookie on the current domain
        if ($_GET['affPubId']) {
            setcookie('a_pub_id', $_GET['affPubId'], $api_publisher_cookie_lifetime, "/", $our_unique_site_domains[COOKIE_SITE_NAME]);
            $should_page_redirect_after_setting_cookies = true;
        }

        // Set the cart cookie on the current domain
        if ($_GET['crtCntntIDs']) {
            setcookie('s2cart', $_GET['crtCntntIDs'], $addToCartCookieDuration, "/", $our_unique_site_domains[COOKIE_SITE_NAME]);
            $should_page_redirect_after_setting_cookies = true;
        }

        // Set the cookie for tracking Univibes click id on the current domain
        if (isset($_GET['univibes-click-id']) && $_GET['univibes-click-id']) {
            setcookie('univibes-click-id', $_GET['univibes-click-id'], $univibes_click_id_cookie_duration, "/", $our_unique_site_domains[COOKIE_SITE_NAME]);
        }

        // One last redirect to clear out the GET values
        if ($should_page_redirect_after_setting_cookies) { record_router_debug_action("Redirect after setting publisher cookie on new domain");
            redirect(ROOT_DIR.$coresite_page_segment_slug_combined);
        }
    }
}

// Dynamic pages based on what the first url segment is
$dynamic_content_pages = array(
	'category' => 'dynamic-programme-category.php', 
	'programme' => 'dynamic-programme.php', 
	'business' => 'dynamic-business-page.php', 
	'business-courses' => 'dynamic-business-course-page.php', 
	'training' => 'dynamic-programme.php', 
	'training-calendar' => 'dynamic-training-calendar.php', 
	'subject' => 'dynamic-subject.php', 
	'enrol' => 'dynamic-enrolment.php', 
	'checkout' => 'dynamic-enrolment.php', 
	'instalment' => 'dynamic-enrolment.php',  
	'staff-profile' => 'dynamic-staff-profile-page.php',
    'ad' => 'dynamic-advert.php',
    'partners' => 'dynamic-universities.php',
    'partner' => 'dynamic-university-details.php',
    'ads-landing' => 'universal-advert-page.php',
    'short-courses' => 'dynamic-short-course-subjects.php',
    'advanced-professional-certificate-courses' => 'dynamic-advanced-professional-certificate-course-subjects.php',
    'subscription-courses' => 'dynamic-subscriptions.php',
	'subscription-enrol' => 'dynamic-enrolment.php',
    'masters-degrees' => 'dynamic-masters-degrees-subjects.php'
);

record_router_debug_action("About to load the actual file to render the page");

// Determine what content to show
if ($coresite_page_segment_1_slug && $coresite_page_segment_2_slug) {
    $page_file = 'template--'.$coresite_page_segment_1_slug.'__'.$coresite_page_segment_2_slug.'.php';
    
    if (file_exists($page_file)) {
        record_router_debug_action("Loaded template php file");
        display_router_debug();
        include_once($page_file);
    }
    elseif (array_key_exists($coresite_page_segment_1_slug, $dynamic_content_pages)) {
        $dynamic_page_file = $dynamic_content_pages[$coresite_page_segment_1_slug];
        
        if (file_exists($dynamic_page_file)) {
            record_router_debug_action("Loaded dynamic content page");
            display_router_debug();
            include_once($dynamic_page_file);
        }
        else {
            record_router_debug_action("Loaded the 404 page");
            display_router_debug();
            include_once('404.php');
        }
    }
    else {
        record_router_debug_action("Loaded the 404 page");
        display_router_debug();
        include_once('404.php');
    }
}
elseif ($coresite_page_segment_1_slug) {
    $page_file = 'template--'.$coresite_page_segment_1_slug.'.php';
    
    if (file_exists($page_file)) {
        record_router_debug_action("Loaded template php file");
        display_router_debug();
        include_once($page_file);
    }
    elseif (array_key_exists($coresite_page_segment_1_slug, $dynamic_content_pages)) {
        $dynamic_page_file = $dynamic_content_pages[$coresite_page_segment_1_slug];
        
        if (file_exists($dynamic_page_file)) {
            record_router_debug_action("Loaded dynamic content page");
            display_router_debug();
            include_once($dynamic_page_file);
        }
        else {
            record_router_debug_action("Loaded the 404 page");
            display_router_debug();
            include_once('404.php');
        }
    }
    else {
        record_router_debug_action("Loaded the 404 page");
        display_router_debug();
        include_once('404.php');
    }
}
else {
    record_router_debug_action("Loaded the homepage");
    display_router_debug();
    include_once('index.php');
}

exit(); 

// Generate URL SLUG for countries
/*mysqli_select_db($config, $database_config);
$query_Actions = "SELECT *
                  FROM loccountries 
                  ORDER BY countryID ASC";
$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
$row_Actions = mysqli_fetch_assoc($Actions);
$totalRows_Actions = mysqli_num_rows($Actions);

if ($totalRows_Actions) {
	do {
        // Generate SEO friendly URL
        $seo_url_name = make_slug($row_Actions['countryName'], 'loccountries', 'url_name');

        mysqli_select_db($config, $database_config);
        $updateSQL = "UPDATE loccountries 
                      SET 
                            url_name = '".sql_string($seo_url_name)."'
                      WHERE 
                            countryID = '".sql_int($row_Actions['countryID'])."'
                      LIMIT 1";
        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));

        echo $row_Actions['countryName'].' -> '.$seo_url_name.'<br/>';
	} while($row_Actions = mysqli_fetch_assoc($Actions));
} 

// Generate URL SLUG for branches
mysqli_select_db($config, $database_config);
$query_Actions = "SELECT *
                  FROM branches 
                  ORDER BY branch_id ASC";
$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
$row_Actions = mysqli_fetch_assoc($Actions);
$totalRows_Actions = mysqli_num_rows($Actions);

if ($totalRows_Actions) {
	do {
        // Generate SEO friendly URL
        $seo_url_name = make_slug($row_Actions['branch_name'], 'branches', 'url_name');

        mysqli_select_db($config, $database_config);
        $updateSQL = "UPDATE branches 
                      SET 
                            url_name = '".sql_string($seo_url_name)."'
                      WHERE 
                      branch_id = '".sql_int($row_Actions['branch_id'])."'
                      LIMIT 1";
        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));

        echo $row_Actions['branch_name'].' -> '.$seo_url_name.'<br/>';
	} while($row_Actions = mysqli_fetch_assoc($Actions));
} 











// Generate URL SLUG for myip_ms_countries
mysqli_select_db($config, $database_config);
$query_Actions = "SELECT *
                  FROM myip_ms_countries 
                  ORDER BY countryID ASC";
$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
$row_Actions = mysqli_fetch_assoc($Actions);
$totalRows_Actions = mysqli_num_rows($Actions);

if ($totalRows_Actions) {
	do {
        // Generate SEO friendly URL
        $seo_url_name = make_slug($row_Actions['countryName'], 'myip_ms_countries', 'url_name');

        mysqli_select_db($config, $database_config);
        $updateSQL = "UPDATE myip_ms_countries 
                      SET 
                            url_name = '".sql_string($seo_url_name)."'
                      WHERE 
                            countryID = '".sql_string($row_Actions['countryID'])."'
                      LIMIT 1";
        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));

        echo $row_Actions['countryName'].' -> '.$seo_url_name.'<br/>';
	} while($row_Actions = mysqli_fetch_assoc($Actions));
} 

// Generate URL SLUG for myip_ms_states
mysqli_select_db($config, $database_config);
$query_Actions = "SELECT *
                  FROM myip_ms_states 
                  ORDER BY stateID ASC";
$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
$row_Actions = mysqli_fetch_assoc($Actions);
$totalRows_Actions = mysqli_num_rows($Actions);

if ($totalRows_Actions) {
	do {
        // Generate SEO friendly URL
        $seo_url_name = make_slug($row_Actions['stateName'], 'myip_ms_states', 'url_name');

        mysqli_select_db($config, $database_config);
        $updateSQL = "UPDATE myip_ms_states 
                      SET 
                            url_name = '".sql_string($seo_url_name)."'
                      WHERE 
                            stateID = '".sql_int($row_Actions['stateID'])."'
                      LIMIT 1";
        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));

        echo $row_Actions['stateName'].' -> '.$seo_url_name.'<br/>';
	} while($row_Actions = mysqli_fetch_assoc($Actions));
} 
*/
