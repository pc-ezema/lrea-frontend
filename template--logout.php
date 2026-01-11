<?php
session_start();

/* Include your config if needed to get $link_root_path */
if (!isset($link_root_path)) {
    include_once('./connections/config.php'); // adjust path to your config
}

/* Clear all frontend auth flags */
unset($_SESSION['logged_in']);
unset($_SESSION['backend_logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_type']);
unset($_SESSION['redirect_counter']);

/* Destroy session */
$_SESSION = [];

/* Remove session cookie */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

/* Redirect to homepage or login page */
if (!isset($link_root_path)) {
    $link_root_path = "cart"; // fallback if not set
}

header("Location: " . $link_root_path);
exit;
