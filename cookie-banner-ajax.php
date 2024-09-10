<?php 
	require_once('connections/config.php');
  
    if (isset($_POST['cookieButton']) == "approveCookie") {
    
        date_default_timezone_set("Europe/London");

        // if 1 hour = 3600
        // 24 hours = 3600 * 24 => 1 day
        // 24 hours = 3600 * 24 * 365 * 2 i.e 2 years

        $expiry_date = time() + (3600 * 24 * 365 * 2);

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //whether ip is from share internet
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //whether ip is from proxy
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else { //whether ip is from remote address
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        // setcookie(name, value, expiration, path, domain)
        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                setcookie("site_cookie_policy", $ip_address, $expiry_date, "/", $uniqueDomainRoot);
            }
        }

        echo "ok";
    }
?>
