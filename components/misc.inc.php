<?php 
/*
** @author Olusiyan Olutayo
** @copyright 2020
*/

/*header('location: '.ROOT_DIR.'under-maintenance.php');
exit();*/

$time_now = date("Y-m-d H:i:s");
$date_now = date("Y-m-d");

// Determine browser type	
$mobile_data_compression = false;
if (!function_exists('detectMobileCompression')) { /* Check_for "detectMobileCompression" */
function detectMobileCompression(){
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (stripos($user_agent,'opera mini') !== false) return true;
	if (stripos($user_agent,'ucweb') !== false) return true;
	if (stripos($user_agent,'ucbrowser') !== false) return true;
	if (stripos($user_agent,'skyfire') !== false) return true;
	return false;
}
} /* End_check for "detectMobileCompression" */

// URL Naming exclusion list
$alldir = array('home', 'index', 'email', 'follow', 'connect', 'network', 'portal', 'lrea', 'topic', 'topics', 'help', 'faq', 'disclaimer', 'feedback', 'bug-report', 'privacy-policy', 'about', 'forum', 'resources', 'extra', 'extras', 'advert', 'surveys', 'reviews', 'fun', 'links', 'store', 'buy', 'sponsor', 'sponsors', 'timeline', 'game', 'games', 'community', 'communities', 'member', 'members', 'search', 'find', 'nigeria', 'group', 'groups', 'message', 'messages', 'inbox', 'chat', 'developer', 'ceo', 'admin', 'points', 'reputation', 'trophy', 'trophies', 'section', 'sections', 'category', 'categories', 'ads', 'adverts', 'advertise', 'moderator', 'moderators', 'mod', 'mods', 'premium', 'business', 'share', 'ask', 'interact', 'post', 'posts', 'question', 'questions', 'im', 'instant-message', 'answer', 'answers', 'like', 'likes', 'dislike', 'dislikes', 'quote', 'comment', 'comments', 'score', 'trending', 'recent', 'updated', 'updates', 'all', 'mentions', 'stats', 'statistics', 'profile', 'account');

// Sessions
if (!$invokedFromCLI) {
	if (!isset($custom_session_name)) {
		session_name("LREASESSID"); // Customize the session name instead of PHPSESSID. Must be called before session_start()

		$sessionRootDomain = $site_version == "online" ? ".lrea.co.uk" : "";

		session_start();
		$cookie_lifetime = 24*60*60*2; // 2 days
		foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
			if ($key == COOKIE_SITE_NAME) { // If this is the current domain
				setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/", $uniqueDomainRoot);
			}
		}
	}
}

if (!isset($sess_user_id)) {
	$sess_user_id = -1;
	if (isset($_SESSION['user_id'])) {
		$sess_user_id = $_SESSION['user_id'];
	}
} 

if(!isset($_COOKIE[$location_cookie_name])) {
	foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
		if ($key == COOKIE_SITE_NAME) { // If this is the current domain
    		setcookie($location_cookie_name, 'united-kingdom', $alt_cookie_lifetime, "/", $uniqueDomainRoot);
		}
	}
}

// Please NOTE that the line of code below is meant to start a db connection on any 
// page where this file is included. This is meant to allow the usage of custom SQL 
// functions that implement mysql functions eg mysqli_real_escape.

mysqli_select_db($config, $database_config);
// Please Never Remove This From This File, It will not work elsewhere. I don't know why though.
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

////////////////////////////////////////////////////////////

// Load in the portal functions
$should_i_use_other_functions = false;
if ($should_i_use_other_functions) {
	if ($db_config_file_dir) { // Use this to try to locate needed functions folder
		$files_of_fns_needed = array(
			'fns/custom.php', 'fns/user-mgt.php', 'fns/url.php', 'fns/url_names.php', 'fns/helpers.php', 'fns/sql.php', 'fns/accounts.php', 'fns/images.php', 
			'fns/posts.php', 'fns/forms.php', 'pagination.inc.php', 'fns/dynamic-db-tables.php', 'fns/no_need.php'
		);

		if (count($files_of_fns_needed)) {
			foreach ($files_of_fns_needed as $functions_file_name) {
				$other_fns_file_name = ROOT_PATH_TO_PORTAL.'components/'.$functions_file_name;

				foreach ($db_config_file_dir as $db_config_file_path) {
					if (@file_exists($db_config_file_path.$other_fns_file_name)) {
						if ($req_file_contents = @file_get_contents($db_config_file_path.$other_fns_file_name)) { // If the file's content can be read
							require_once($db_config_file_path.$other_fns_file_name);
							break;
						}
					}
				}
			}
		}
	}
}
else {
	if ($db_config_file_dir) { // Use this to try to locate needed functions folder
		$other_fns_file_name = ROOT_PATH_TO_PORTAL.'components/fns/custom.php';

		foreach ($db_config_file_dir as $db_config_file_path) {
			if (@file_exists($db_config_file_path.$other_fns_file_name)) {
				if ($req_file_contents = @file_get_contents($db_config_file_path.$other_fns_file_name)) { // If the file's content can be read
					// Check for something that should exist only inside the target file
					if (stripos($req_file_contents, '#iAmThePortal') !== false) {
						require_once($db_config_file_path.$other_fns_file_name);
						break;
					}
				}
			}
		}
	}
}

// Load in current site functions
require_once('fns/custom.php');
require_once('fns/user-mgt.php');
require_once('fns/url.php');
require_once('fns/url_names.php');
require_once('fns/helpers.php');
require_once('fns/sql.php');
require_once('fns/accounts.php');
require_once('fns/images.php');
require_once('fns/posts.php');
require_once('fns/forms.php');
require_once('pagination.inc.php');
require_once('fns/no_need.php');

// Site Maintenance Handling
$site_maintenance = false;
$maintenance_people = array(1);

// Sanitize the add to cart cookie
if ($theValueThatExists = $_COOKIE['s2cart']) {
	$retrieved_Cart_Info = explode(",", $theValueThatExists);

	// Make sure that only valid IDs are processed
	if ($initial_count = count($retrieved_Cart_Info)) {
		foreach ($retrieved_Cart_Info as $key => $value) {
			if (!$value) {
				unset($retrieved_Cart_Info[$key]);
			}
		}
	}

	// Updates the cookie if needed
	if ($initial_count != count($retrieved_Cart_Info)) {
		if (count($retrieved_Cart_Info)) {
			$add_New_Prgramme_To_Cookie = implode(",", $retrieved_Cart_Info);

			if ($add_New_Prgramme_To_Cookie) {
				foreach ($our_unique_site_domains as $Key => $uniqueDomainRoot) {
					if ($key == COOKIE_SITE_NAME) { // If this is the current domain
						setcookie("s2cart", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
					}
				}
			}
		}
	}
}