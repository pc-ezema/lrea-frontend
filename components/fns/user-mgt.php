<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!isset($sess_user_id)) {
	$sess_user_id = -1;
	if (isset($_SESSION['user_id'])) {
		$sess_user_id = $_SESSION['user_id'];
	}
}

if (!function_exists('confirm_backend_access')) { /* Check_for "confirm_backend_access" */
function confirm_backend_access() {
	if (!$_SESSION['logged_in']) {
		authorize_redirect();
	} elseif (!$_SESSION['backend_logged_in']) {
		redirect(ROOT_DIR);
	} else {
		session_check();
	}
}
} /* End_check for "confirm_backend_access" */

if (!function_exists('confirm_student_access')) { /* Check_for "confirm_student_access" */
function confirm_student_access() {
	confirm_logged_in();
	
	if (!in_array($_SESSION['user_type'], array('student'))) {
		redirect(ROOT_DIR);
	}
}
} /* End_check for "confirm_student_access" */

if (!function_exists('confirm_admin_rights')) { /* Check_for "confirm_admin_rights" */
function confirm_admin_rights($allowed_users=array('staff')) {
	// User Types: 'admin', 'sub-admin', 'lecturer', 'business development manager', 'staff', 'agent', 'temporary admin'
	
	confirm_logged_in();
	
	if (!in_array($_SESSION['user_type'], $allowed_users)) {
		redirect(ROOT_DIR);
	}
}
} /* End_check for "confirm_admin_rights" */

if (!function_exists('confirm_logged_in')) { /* Check_for "confirm_logged_in" */
function confirm_logged_in() {
	if (!$_SESSION['logged_in']) {
		authorize_redirect();
	} else {
		session_check();
	}
}
} /* End_check for "confirm_logged_in" */

if (!function_exists('logged_in')) { /* Check_for "logged_in" */
function logged_in() {
	if (!$_SESSION['logged_in']) {
		return false;
	} else {
		return true;
	}
}
} /* End_check for "logged_in" */

if (!function_exists('is_allowed_user')) { /* Check_for "is_allowed_user" */
function is_allowed_user($this_user_id) {
	global $sess_user_id;
	if ($sess_user_id > 0) {
		if ($sess_user_id != $this_user_id) {
			redirect(ROOT_DIR."access/unauthorized");
		}
	} else {
		redirect(ROOT_DIR."access/unauthorized");
	}
}
} /* End_check for "is_allowed_user" */

if (!function_exists('your_own')) { /* Check_for "your_own" */
function your_own($this_user_id) {
	global $sess_user_id;
	if ($sess_user_id > 0) {
		if ($sess_user_id != $this_user_id) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
} /* End_check for "your_own" */

if (!function_exists('session_check')) { /* Check_for "session_check" */
function session_check() {
	global $sess_user_id;
	if ($sess_user_id < 1) {
		authorize_redirect_expired();
	}
}
} /* End_check for "session_check" */

if (!function_exists('authorize_redirect')) { /* Check_for "authorize_redirect" */
function authorize_redirect() {
	$to = ROOT_DIR."user/access";
	$back = curPageURL();
	
	redirect($to."?return=".urlencode($back)."&init=".urlencode(link_back("")));
}
} /* End_check for "authorize_redirect" */

if (!function_exists('authorize_redirect_expired')) { /* Check_for "authorize_redirect_expired" */
function authorize_redirect_expired() {
	$to = ROOT_DIR."?signout=true";
	$back = curPageURL();
	
	redirect($to."?expired=true&return=".urlencode($back));
}
} /* End_check for "authorize_redirect_expired" */

if (!function_exists('redirect')) { /* Check_for "redirect" */
	function redirect($page) {
		// Try to prevent too many redirects
		if (!isset($_SESSION['redirect_counter'])) {
			$_SESSION['redirect_counter'] = 1;
		}
		else {
			$_SESSION['redirect_counter']++;
	
			if ($_SESSION['redirect_counter'] > 3) {
				$_SESSION = array();
				setcookie(session_name(), '', time() - 42000, '/');
				session_destroy();
	
				if (headers_sent()) {
					die("... Something went wrong. Please <a href=\"".ROOT_DIR."\">Click Here</a> to go to the homepage.");
				}
				else {
					header("location: ".ROOT_DIR);
					exit();
				}
			}
		}
	
		if (headers_sent()) {
			die("... Please <a href=\"".$page."\">Click Here</a> to Continue");
		}
		else {
			header("location: ".$page);
			exit();
		}
	}
} /* End_check for "redirect" */

