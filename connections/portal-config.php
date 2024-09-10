<?php 

	if (preg_match("/^localhost/", $desiredServerName) || preg_match("/^(\d{1,3}\.){2,}/", $desiredServerName) || $staging_server_mode) { // Allows ip address on localhost
		define("ROOT_DIR", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/".$uriPart."/");
		$sessionRootDomain = "";

		define("ROOT_DIR_UK", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/".$uriPartPrefix."/");
		define("FILE_PATH_TO_UK", "../".$uriPartPrefix."/");
		define("ROOT_DIR_PORTAL", "http".$whether_https_for_localhost."://".$_SERVER["HTTP_HOST"]."/".$uriPart."/");
		define("PORTAL_FOLDER", "new_portal");

		define("COOKIE_SITE_NAME", "localhost");

		// Used in MoxieManager Config located inside (new_portal/js/tinymce4.2.5/plugins/moxiemanager/config.php)
		if (is_dir("C:/wamp/www/new_portal/")) {
			define("DOC_ROOT", "C:/wamp/www/new_portal/"); // Windows
		}
		else {
			// define("DOC_ROOT", "/Applications/MAMP/htdocs/new_portal/"); // MAC
            define("DOC_ROOT", "/Applications/XAMPP/xamppfiles/htdocs/new_portal/"); // MAC
		}

		define("MOXMAN_BROWSE_PATH", "/new_portal/uploads/");

		define("PAYPAL_VERSION", "sandbox");

		$fileCacheVersion = 'ty'.$fileCacheVersion; 
		$site_version = "localhost";

		define('PUSH_NOTIFICATION_SERVER_API_KEY', 'AAAAWHPXaEs:APA91bGqH7YifKgsLTuVthbKZWLi6XHDh3Y8F0iH2XJ-CoqO08wsqXBsJ3zQ0xZ2hgPY_6TVXFx65tF8rQ970SYdqaJfjX-_v6s5JCMzibtI1-9QMdgAocssQoC8ezc13bqnxOTjICZv');
		define("NGROK_URL", "https://a4c0-102-89-47-247.ngrok-free.app/new_portal/"); 
	} else {
		define("ROOT_DIR", "https://portal.lrea.co.uk/");
		$sessionRootDomain = ".lrea.co.uk"; // Make sure it is the root domain not a subdomain

		define("ROOT_DIR_UK", "https://lrea.co.uk/");
		define("FILE_PATH_TO_UK", "../"); // "../version2/"
		define("ROOT_DIR_PORTAL", "https://portal.lrea.co.uk/");
		define("PORTAL_FOLDER", "portal");

		define("COOKIE_SITE_NAME", "lrea.co.uk");

		if (!$invokedFromCLI) define("DOC_ROOT", $_SERVER['DOCUMENT_ROOT'].'/'); // Make sure it is configured properly. Determine using echo $_SERVER['DOCUMENT_ROOT']
		define("MOXMAN_BROWSE_PATH", "/public_html/portal/uploads/"); // Make sure it is configured properly relative to the DOC_ROOT

		define("PAYPAL_VERSION", "production");

		$fileCacheVersion = ''.$fileCacheVersion; 
		$site_version = "online";

		define('PUSH_NOTIFICATION_SERVER_API_KEY', '');
	}

	// Color Scheme


	// Brand Images


    // Start Timing of Script
    $start_timing = microtime(TRUE);

    // Setup site nomenclature constants
    define("SITE_NAME", "lrea");
    define("SITE_NAME_LOWER", "portal.lrea.co.uk");
    define("SITE_NAME_WWW", "portal.lrea.co.uk");
	define("MAIN_SITE_DOMAIN", "lrea.co.uk");
	define("FOUR_LETTERS", "LREA");

    define("WHITE_LOGO_URL", ROOT_DIR_UK."images/main-logo-3.png"); // Must have transparent background
    define("COLOR_LOGO_URL", ROOT_DIR_UK."images/main-logo-2.png"); // Must have transparent background

    define("SITE_NAME_LOGO", '<img src="'.ROOT_DIR_UK.'img/mini-logo.png?v=1.0" style="margin-top:-5px;" /> <strong>lrea</strong>');

	define("TRANSCRIPT_FOOTER", "");

    define("SERVER_EMAIL_MAX_PER_HOUR", 480);
    define("SERVER_EMAIL_TIMEOUT_MINS", 50); //Minutes

    define("LOGIN_ID_MASK_INCREMENT", 98769);
    define("TICKET_ID_MASK", "1405");

    // define("PUBLISHER_INCREMENT", 994405952);

    // Currency
    define("BASE_CURRENCY", "AED"); // Never change this
    define("PREFERRED_CURRENCY", "GBP"); // Never change this
    if (BASE_CURRENCY == 'GBP') {
        define("DEFAULT_BASE_CURRENCY_CONVERSION_RATE", 1.23);
    }
    else {
        define("DEFAULT_BASE_CURRENCY_CONVERSION_RATE", 1.00);
    }

    // Set the timezone
    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set('Europe/London');
    } else {
        ini_set('date.timezone', 'Europe/London');
    }

    $show_profile_completion = true;

    $push_notifications_activated = false;

    $allow_lecture_for_expired_courses = true;

    $use_old_module_look = false;

    $use_page_preloader = true;

    $remove_preloader = false;

    $enable_student_id_download =true;

    $allow_report_csv_download = true;

    $disableFooterCookieDuration = time() + (16 * 365 * 24 * 60 * 60); // 16 years

    if (isset($_GET['mapwa'])) { // if the pwa parameter is passed. This is here for the app
        if ($_GET['mapwa'] == 'v1') {
            // set cookie to hide footer for app. If the cookie exists, it will extend its lifespan
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie("disable_web_params", "true", $disableFooterCookieDuration, "/", $uniqueDomainRoot);
                }
            }
        }
        elseif ($_GET['mapwa'] == 'v2') {
            // set cookie to hide footer for app but allows website navigation
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie("disable_web_footer", "true", $disableFooterCookieDuration, "/", $uniqueDomainRoot);
                }
            }

            if (isset($_COOKIE['disable_web_params'])) { // if the previous cookie exists, unset the cookie
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('disable_web_params', '', time() - $disableFooterCookieDuration, '/', $uniqueDomainRoot);
                    }
                }
            }
        }
        
        // redirect(root_dir_portal()); // So that we can then use the cookie parameter This function does not exist and it is failing
        header("location: ".ROOT_DIR);
        exit();
    }

    if (isset($_COOKIE['disable_web_params'])) {
        define("DISABLE_WEB_PARAMS", 'true');
    }
    elseif (isset($_COOKIE['disable_web_footer'])) {
        define("DISABLE_WEB_FOOTER", 'true');
    }

    if (!defined("OTHM_USER_LOGIN_NAME")) {
        define('OTHM_USER_LOGIN_NAME', '');
        define('OTHM_USER_LOGIN_PASSWORD', '');
    }

    $hide_the_download_btn_on_pwa = false;
    $display_pdf_inline = false;
    $renderPaymentReminder = true;
    $allow_staff_management = true;
    $show_staff_leave_options = true;
    $allow_autosuggesteditor = true;
    $allow_signature_download = true;
    $allow_idcard_download = true;
    $allow_other_permitted_staff_to_forward_tickets = true;
    $StaffIDCardDesignLook = "v2";
    $thisIsThePortalSegment = true;
    $enable_video_call = true;
    $enable_webinar_management = true;
    $show_payment_notification = true;
    $study_reminder_test = true;
    $enable_study_reminder = true;
    $study_reminder_users = array(''); // Emails of users who will test this feature.
    $enable_reference_management = true;
    $staff_unpaid_max_leave_request_days = 12;

    $allow_uk_branch_for_students = true;

    $salt_for_cart_emails_token = "do not change this value please";

    // vimeo keys needed for vimeo uploading videos
    if (!defined("VIMEO_CLIENT_TOKEN")) {
        define("VIMEO_CLIENT_TOKEN", "");
        define("VIMEO_CLIENT_ID", "");
        define("VIMEO_CLIENT_SECRET_KEY", "");
        define("VIMEO_USER_ID", "");
    }

    $duration_eligible_for_paid_leave = "1 year";

    $prevent_automated_emailing = false; // Added when sending mails via Mailgun was hanging the server

    $total_library_articles = "31,000,000+";

    $test_live_scorm_webview_on_localhost = false; // Only turn this to false on localhost if you want to test live scorm lecture on the mobile app.

    $enable_app_debug = false;

    $compulsory_welcome = $site_version == 'online' ? true : true;

    $minimum_profile_completion = 50;

    $use_azure_custom_mailer = false;

    $use_test_keys_for_app_payment_confirmation = false;

    define("PORTAL_STUDENT_SUPPORT_EMAIL", "");

    define("SCHOOL_NAME", "lrea");

    $use_new_logo_for_staff_portal = true;

    $allow_certificate_and_transcript_download = true;

    $subscriptions_are_available = true;

    $membership_is_available = true;