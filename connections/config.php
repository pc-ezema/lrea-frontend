<?php
// CSS & JS Version for cache breaking
// Only numbers, no dots or symbols
$fileCacheVersion = '14'; 
$hostname_config = null; // Initialize with a default value
$invokedFromCLI = null;

if (preg_match('/^cli/i', php_sapi_name()) || !isset($_SERVER['REQUEST_METHOD'])) { // If current invocation of PHP is from the command line (CLI)
	$desiredServerName = php_uname("n"); 
	$invokedFromCLI = true;
} 
else { // If not from CLI
	$desiredServerName = $_SERVER["SERVER_NAME"];
}

$whether_https_for_localhost = ''; $staging_server_mode = false;
if ((strpos($desiredServerName, "ngrok") !== false) || (strpos($desiredServerName, "stagingburgv1") !== false)) {
	$staging_server_mode = true;
	$whether_https_for_localhost = 's';
}

if (preg_match("/^localhost/", $desiredServerName) || preg_match("/^(\d{1,3}\.){2,}/", $desiredServerName) || $staging_server_mode) { // Allows ip address on localhost
	define("ROOT_DIR", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/lrea/");
	$sessionRootDomain = "";

	define("ROOT_DIR_PORTAL", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/lrea_new_portal/");
	define("PORTAL_FOLDER", "new_portal");
	define("ROOT_PATH_TO_PORTAL", "../new_portal/");

	
	// print_r(ROOT_PATH_TO_PORTAL);
	// exit();

	define("ASSETS_ROOT_DIR", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/lrea/");
	define("MEDIA_ROOT_DIR", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/lrea/");

	define("SITE_NAME_LOWER", "lrea");
	define("COOKIE_SITE_NAME", "localhost");

	// Used in MoxieManager Config located inside (lrea/js/tinymce4.2.5/plugins/moxiemanager/config.php)
	if (is_dir("C:/wamp/www/lrea/")) {
		define("DOC_ROOT", "C:/wamp/www/lrea/"); // Windows
	}
	else {
		// define("DOC_ROOT", "/Applications/MAMP/htdocs/lrea/"); // MAC
		define("DOC_ROOT", "/Applications/XAMPP/xamppfiles/htdocs/lrea/"); // MAC
	}

	define("MOXMAN_BROWSE_PATH", "/lrea/uploads/");

	define("PAYPAL_VERSION", "sandbox");

	$fileCacheVersion = 'ty'.$fileCacheVersion; 
	$site_version = "localhost";
} else {
	define("ROOT_DIR", "https://lrea.co.uk/");
	define("SITE_NAME_LOWER", "lrea.co.uk");
	define("COOKIE_SITE_NAME", "lrea.co.uk");

	$sessionRootDomain = ".lrea.co.uk"; // Make sure it is the root domain not a subdomain

	define("ROOT_DIR_PORTAL", "https://portal.lrea.co.uk/");
	define("PORTAL_FOLDER", "portal");
	define("ROOT_PATH_TO_PORTAL", "portal/"); // "../portal/"

	define("ASSETS_ROOT_DIR", "https://lrea.co.uk/");
	define("MEDIA_ROOT_DIR", "https://lrea.co.uk/");

	if (!$invokedFromCLI) define("DOC_ROOT", $_SERVER['DOCUMENT_ROOT'].'/'); // Make sure it is configured properly. Determine using echo $_SERVER['DOCUMENT_ROOT']

	define("MOXMAN_BROWSE_PATH", "/public_html/uploads/"); // Make sure it is configured properly relative to the DOC_ROOT

	define("PAYPAL_VERSION", "production");

	$fileCacheVersion = ''.$fileCacheVersion; 
	$site_version = "online";
}

//header("location: http://lrea.co.uk/maintenance.html");

// Use this only when working on paytabs v2 implementation
// This is because the TEST and LIVE keys are inside db_my_config.php
if ($site_version == 'localhost') {
	$test_paytabs_api = true;
}

// This allows testing the flutterwave implementation on localhost
// This is because the TEST and LIVE keys are inside db_my_config.php
if ($site_version == 'localhost') {
	$test_flutterwave_api = true;
}

if (!function_exists('get_real_ip_addr')) { /* Check_for "get_real_ip_addr" */
	function get_real_ip_addr() {
		if(!empty($_SERVER['HTTP_CLIENT_IP']))   //check if IP Address is from shared internet
		{ $ip=$_SERVER['HTTP_CLIENT_IP']; }
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check if IP Address is passed from proxy
		{ $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
		else
		{ $ip=$_SERVER['REMOTE_ADDR']; }
		
		if (!preg_match('/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/i',$ip)) $ip = '127.0.0.1';
		
		return $ip;
	}
} /* End_check for "get_real_ip_addr" */

/*if (get_real_ip_addr() == '185.169.233.92') {
	$debug_router_timing = true;
	$time_now = date("Y-m-d H:i:s");
}*/
	
if (!function_exists('display_router_debug')) {
	function display_router_debug() {
		global $debug_router_timing;

		if ($debug_router_timing) {
			$_SESSION['router_timings']['Ended'] = date("Y-m-d H:i:s");
			echo '<pre>'; var_dump($_SESSION['router_timings']); echo '</pre>';
			unset($_SESSION['router_debug_started']);
			exit();
		}
	}
}

if (!function_exists('record_router_debug_action')) {
	function record_router_debug_action($description) {
		global $debug_router_timing;

		if ($debug_router_timing) {
			$current_router_debug_timing = microtime(TRUE);
			$_SESSION['router_timings'][$description] = number_format($current_router_debug_timing - $_SESSION['last_router_timing_start'], 8).' seconds';
			$_SESSION['last_router_timing_start'] = $current_router_debug_timing;
		}
	}
}

if (!$hostname_config) { // I use this to prevent having to change db config across each device
	$db_config_file_dir = array('', '../', '../../', '../../../', '../../../../', '../../../../../');
	$db_config_file_name = 'db_new_config.php';

	foreach ($db_config_file_dir as $db_config_file_path) {
		if (@file_exists($db_config_file_path.$db_config_file_name)) {
			require($db_config_file_path.$db_config_file_name);
			break;
		}
	}

	if (!$hostname_config) { // Check once more to be sure that the db config was found otherwise stop
		exit('Could not find the config file');
	}
}
$config = @mysqli_connect($hostname_config, $username_config, $password_config, $database_config) or trigger_error(mysqli_error($config),E_USER_ERROR); 
//mysql_set_charset("UTF8", $config);

// Start Timing of Script
$start_timing = microtime(TRUE);

// Setup site nomenclature constants
define("SITE_NAME", "London Real Estate Academy (LREA)");
define("SITE_NAME_WWW", "lrea.co.uk");
define("MAIN_SITE_DOMAIN", "lrea.co.uk");

// Currency
define("BASE_CURRENCY", "AED"); // Never change this
define("PREFERRED_CURRENCY", "GBP"); // Never change this
if (BASE_CURRENCY == 'GBP') {
	define("DEFAULT_BASE_CURRENCY_CONVERSION_RATE", 1.23);
}
else {
	define("DEFAULT_BASE_CURRENCY_CONVERSION_RATE", 1.00);
}

// google captcha credentials
if ($site_version == "localhost") {
	define("CAPTCHA_SITE_KEY", "");
	define("CAPTCHA_SECRET_KEY", "");

	define("PUSH_NOTIF_API_KEY", "");
	define("PUSH_NOTIF_AUTH_DOMAIN", "");
	define("PUSH_NOTIF_DATABASE_URL", "");
	define("PUSH_NOTIF_PROJECT_ID", "");
	define("PUSH_NOTIF_STORAGE_BUCKET", "");
	define("PUSH_NOTIF_SENDER_ID", "");
	define("PUSH_NOTIF_APP_ID", "");
	define("PUSH_NOTIF_PUBLIC_VAPID_KEY", "");
}
else {
	define("CAPTCHA_SITE_KEY", "");
	define("CAPTCHA_SECRET_KEY", "");
	define("WEBHOOK_PAGE_ACCESS_TOKEN", "");
	
	define("PUSH_NOTIF_API_KEY", "");
	define("PUSH_NOTIF_AUTH_DOMAIN", "");
	define("PUSH_NOTIF_DATABASE_URL", "");
	define("PUSH_NOTIF_PROJECT_ID", "");
	define("PUSH_NOTIF_STORAGE_BUCKET", "");
	define("PUSH_NOTIF_SENDER_ID", "");
	define("PUSH_NOTIF_APP_ID", "");
	define("PUSH_NOTIF_PUBLIC_VAPID_KEY", "");
}

$push_notifications_activated = false;
$show_contact_us_feature = true;

// Set the timezone
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('Europe/London');
} else {
	ini_set('date.timezone', 'Europe/London');
}

// For cookies
$alt_cookie_lifetime = time() + (24*60*60*7); // 7 days
$location_cookie_name = 'pref_ctry';

// Accept cookie
$cookie_acceptance_is_needed = true;

$show_number_of_months_to_pay_instalmental_fee = false;

$api_publisher_cookie_lifetime = time() + (3600 * 24 * 30); // 1 month

$allow_multiple_of_each_course_at_checkout = false;
$allow_discount_code = true;

$enable_add_to_cart_btn = $site_version == 'online' ? true : true;

$show_seo_alternative_title_label = true;

$use_new_review = true;

$use_new_domains_google_tags = true;

$use_new_criteo_codes = true;

$displayPeopleCountOnNavbar = false;

$thisIsTheMainWebsiteSegment = true;

$criteo_partner_id = 0; // Replace with your criteo partner ID

$enable_part_payment_in_cart = true;
define("INSTALMENT_PLAN_OBFUSCATION", 59404);
$display_new_add_to_cart_button = false; // Used to make sure the new add to cart button in the programme boxes only show on the main website and not on portal.

$debug_flutterwave_payment_process = true; // The payment processes will be logged to a file.

$da_prog_is_active = true; // This is used in determining whether to hide the enrol button on the programme individual page and exclude a course from cart or not.

$staff_directory_user_id_mask = 2735687;

$use_new_website_look = true; // Latest version.

$temporarily_use_database_for_email_sending_process = true;

$ignore_seo_params = true;

$use_composer_for_individual_programmes_youtube_video = true; // If it is false, it will use the video set for the particular programme from the backend.

$download_trainings_calendar_as_pdf = true; // If set to false, it downloads as CSV.
$this_is_calendar_page = false; // This variable is used majorly in the inc.business-form.php file to handle the bahaviour of the form fields.

$include_past_trainings = false;

$univibes_click_id_cookie_duration = time() + (3600 * 24 * 7); // 7 days 

$order_progs_by_partner_schools = true;

$enable_tuition_breakdown_for_long_duration_courses = false;

$show_blog_on_individual_programme_page = false;

$show_monthly_fee_on_prog_boxes = false;

$short_course_subject_variables = array(
	'business-management' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-business-management.jpg',		
		'subTitle' => 'Elevate Your Leadership Skills and Unlock Success in Business Management with Expert-Led Courses'
	),
	'business-law' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-business-law.jpg',		
		'subTitle' => 'Boost Your Career with Comprehensive Business Law Courses and Expand Your Legal Expertise Today!'
	),
	'business-research' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-business-research.jpg',		
		'subTitle' => 'Unleash the Power of Data, Master Business Research Strategies and Propel Your Decision-Making Skills'
	),
	'health-and-psychology' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-health-and-psychology.jpg',		
		'subTitle' => 'Nurture Minds, Transform Lives, Explore the Intersection of Health and Psychology with Short Professional Courses'
	),
	'information-technology' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-information-technology.jpg',
		'subTitle' => 'Unlock Your IT Superpowers, Ignite Your Skills and Inspire Innovation'
	),
	'personal-development' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-personal-development.jpg',		
		'subTitle' => 'Maximize Your Growth and Accelerate Personal Development with Expert Guidance'
	),
	'teaching-and-education' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-teaching-and-education.jpg',		
		'subTitle' => 'Empower Your Teaching Journey and Enroll in our Cutting-Edge Professional Short Course in Teaching & Education'
	),
	'health-and-safety' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-health-and-safety.jpg',		
		'subTitle' => 'Build a Secure Future and Master the Essentials of Health and Safety through Comprehensive Professional Courses'
	),
	'finance-and-accounting' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-finance-and-accounting.jpg',		
		'subTitle' => 'Unlock the Secrets of Financial Success, Master the Art of Finance and Accounting through Comprehensive Courses'
	),
	'office-productivity' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-office-productivity.jpg',		
		'subTitle' => 'Boost Your Workplace Efficiency and Master Office Productivity Skills with Our Professional Short Course!'
	),
	'sales-and-marketing' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-sales-and-marketing.jpg',		
		'subTitle' => 'Cultivate Your Sales and Marketing Prowess with Our Professional Short Course Mastering Strategies for Success!'
	),
	'leadership-and-entrepreneurship' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-leadership-and-entrepreneurship.jpg',		
		'subTitle' => 'Unlock Your Leadership Potential and Master Entrepreneurship with Our Exclusive Short Course'
	),
	'human-resources' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-human-resources.jpg',		
		'subTitle' => 'HR Excellence Unleashed: Amplify Your Skills for Organizational Success'
	),
	'hotel-travel-and-hospitality-management' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-hotel-travel-and-hospitality-management.jpg',		
		'subTitle' => 'Experience the Hospitality Advantage: Transforming Careers in Hotel, Travel, and Hospitality Management!'
	),
	'logistics-and-supply-chain-management' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-logistics-and-supply-chain-management.jpg',		
		'subTitle' => 'Gain a Competitive Edge in Logistics and Supply Chain Management, accelerate Your Career with our Results-Driven Professional Short Course!'
	),
	'project-and-operations-management' => array(
		'image' => MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/pcc-project-and-operations-management.jpg',		
		'subTitle' => 'Accelerate Your Career with Project & Operations Management Expertise in Our Professional Short Course'
	),
);

$advanced_professional_course_subject_variables = $short_course_subject_variables;

define("INFO_SUPPORT_EMAIL", "tech-support@lrea.co.uk");
define("TECH_SUPPORT_EMAIL", "tech-support@lrea.co.uk");

$least_expected_total_reviews = 9;

define("SITE_EMAIL_DOMAIN", "lrea.co.uk");