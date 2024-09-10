<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('remove_http_https')) { /* Check_for "remove_http_https" */
function remove_http_https($link) {
	$new_link = preg_replace(array("@^(http)s?:\/\/.+?\/@i", "@^(http)s?:\/\/.+?\/lrea.co.uk\/@i"), array("http://lrea.co.uk", "http://lrea.co.uk"), $link);
	
	return $new_link;
}
} /* End_check for "remove_http_https" */

if (!function_exists('outbound')) { /* Check_for "outbound" */
function outbound($url) {
	if (!preg_match('/^(ftp|http)s?:\/\//i', $url)) {
		$url = "http://".$url;
	}
	return $url;
}
} /* End_check for "outbound" */

if (!function_exists('append_query_link')) { /* Check_for "append_query_link" */
function append_query_link($destination_url=''){
    if ($destination_url == '') {
        $destination_url = $_SERVER['PHP_SELF'];
    }
	$query_char = "?";
    if (stripos($destination_url, "?")) {
        $query_char = "&";
    }
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
            $destination_url .= $query_char.$_SERVER['QUERY_STRING'];
            $destination_url.="&";
    } else {
            $destination_url.="?";
    }
    return $destination_url;
}
} /* End_check for "append_query_link" */

if (!function_exists('append_to_url')) { /* Check_for "append_to_url" */
function append_to_url($url='', $string=''){
    if ($url == '') {
        $url = $_SERVER['PHP_SELF'];
    }
	$query_char = "?";
	if (stripos($url, "?")) {
        $query_char = "&";
    }
    
    return $url."".$query_char."".$string;
}
} /* End_check for "append_to_url" */

if (!function_exists('link_back')) { /* Check_for "link_back" */
function link_back($page) {
	if ($_GET['init']) {
		return $_GET['init'];
	}
	elseif (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST'])) {
		$url = parse_url($_SERVER['HTTP_REFERER']);
		// find if visitor was referred from a different domain
		if ($url['host'] == $_SERVER['HTTP_HOST']) {
		  	// if same domain, use referring URL
		  	return $_SERVER['HTTP_REFERER'];
		  } 
	    else // if diff domain, use specified page
	    { 
		  return $page; 
	    }
	}
	else {
	// if http_referer and http_host are not set
	// send to specified page
	
	return $page;
	}	
}
} /* End_check for "link_back" */

if (!function_exists('curPageURL')) { /* Check_for "curPageURL" */
function curPageURL($remove_query_strings=false, $show_non_default_port=false) {
	//Gotten from http://webcheatsheet.com/php/get_current_page_url.php
	   $pageURL = 'http';
	   if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	   $pageURL .= "://";
	   if ($_SERVER["SERVER_PORT"] != "80") {
			if ($show_non_default_port) {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			}
			else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
	   } else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	   }
	   $current_url = str_replace("www.", "", $pageURL);

	   if ($remove_query_strings) {
			$current_url = preg_replace(array("/\?.*$/i"), array(""), $current_url);
	   }

	   return $current_url;
}
} /* End_check for "curPageURL" */

if (!function_exists('illegal_url')) { /* Check_for "illegal_url" */
function illegal_url($type = 'string', $slug = '') {
	// Acceptable types are string, array, checkit
	global $alldir;
	$inneralldir = $alldir;
	
	$dir = '.';
	$files = scandir($dir, 1);
	
	foreach ($files as $value) {
		if ($value === '.' || $value === '..') continue;
		
		if (is_dir($dir.'/'.$value)) {
			$inneralldir[] = $value;
		}
	}
	
	if ($type == 'string') { // If returned value should be a string separated by commas
		return implode(", ", $inneralldir);
	}
	elseif ($type == 'checkit') { // If returned value should be result of comparison
		foreach ($inneralldir as $val) {
			if (strtolower($slug) == strtolower($val)) {
				return true;
			} 
		}
		return false;
	}
	elseif ($type == 'array') { // If returned value should be an array
		return $inneralldir;
	}
}
} /* End_check for "illegal_url" */

