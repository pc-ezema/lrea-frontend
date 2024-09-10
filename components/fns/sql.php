<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists("sql_float")) {
	  function sql_float($theValue) {
		  $theValue = preg_replace("/[^0-9\.]/", "", $theValue);
		  if (my_is_float($theValue)) {
		  	return $theValue;
		  }
		  else return 0;
	  }
}

if (!function_exists("sql_int")) {
	  function sql_int($theValue) {
		  if (my_is_int($theValue)) {
		  	return $theValue;
		  }
		  else return 0;
	  }
}

if (!function_exists("sql_string")) {
	  function sql_string($theValue, $trim='', $queryish=true) {
		  global $database_config, $config;
		  mysqli_select_db($config, $database_config);
		  $theValue = my_strip_tags($theValue);
		  $theValue = my_stripslashes($theValue);
		  
		  if ($trim == 'start') {
			  $theValue = ltrim($theValue);
		  } elseif ($trim == 'end') {
			  $theValue = rtrim($theValue);
		  } elseif ($trim == 'both') {
			  $theValue = trim($theValue);
		  }
		  
		  if ($queryish) $theValue = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($theValue) : our_mysqli_real_escape_string($theValue);
		  
		  return $theValue;
	  }
}

if (!function_exists("sql_string_br")) {
	  function sql_string_br($theValue, $trim='', $queryish=true) {
		  global $database_config, $config;
		  mysqli_select_db($config, $database_config);
		  $theValue = textarea_strip_tags($theValue);
		  $theValue = my_stripslashes($theValue);
		  
		  if ($trim == 'start') {
			  $theValue = ltrim($theValue);
		  } elseif ($trim == 'end') {
			  $theValue = rtrim($theValue);
		  } elseif ($trim == 'both') {
			  $theValue = trim($theValue);
		  }
		  
		  if ($queryish) $theValue = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($theValue) : our_mysqli_real_escape_string($theValue);
		  
		  return $theValue;
	  }
}

if (!function_exists("sql_string_br_extended")) {
	  function sql_string_br_extended($theValue, $trim='', $queryish=true) {
		  global $database_config, $config;
		  mysqli_select_db($config, $database_config);
		  $theValue = textarea_strip_tags_extended($theValue);
		  $theValue = my_stripslashes($theValue);
		  
		  if ($trim == 'start') {
			  $theValue = ltrim($theValue);
		  } elseif ($trim == 'end') {
			  $theValue = rtrim($theValue);
		  } elseif ($trim == 'both') {
			  $theValue = trim($theValue);
		  }
		  
		  if ($queryish) $theValue = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($theValue) : our_mysqli_real_escape_string($theValue);
		  
		  return $theValue;
	  }
}

if (!function_exists("sql_string_tags")) {
	  function sql_string_tags($theValue, $trim='', $queryish=true) {
		  global $database_config, $config;
		  mysqli_select_db($config, $database_config);
		  $theValue = textarea_strip_tags($theValue);
		  $theValue = my_stripslashes($theValue);
		  
		  if ($trim == 'start') {
			  $theValue = ltrim($theValue);
		  } elseif ($trim == 'end') {
			  $theValue = rtrim($theValue);
		  } elseif ($trim == 'both') {
			  $theValue = trim($theValue);
		  }
		  
		  if ($queryish) $theValue = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($theValue) : our_mysqli_real_escape_string($theValue);
		  
		  return $theValue;
	  }
}

if (!function_exists('query_like')) { /* Check_for "query_like" */
function query_like($search_words, $column, $operator) {
    if (is_array($search_words)) {
		$query_string = "";
		foreach ($search_words as $value) {
			$query_string .= "$column LIKE '%".$value."%' ".strtoupper($operator)." ";
		}
        $query_string = substr($query_string, 0, strlen($query_string)-(strlen($operator)+2));
	} else {
		$query_string = "$column LIKE '%".$search_words."%'";
	}
    
    return $query_string;
}
} /* End_check for "query_like" */

if (!function_exists('relevance')) { /* Check_for "relevance" */
function relevance($column = '', $criteria = array()) {
	if (!$column) return '';
	
	if (!is_array($criteria)) {
		if (!$criteria) return ''; else $words = explode(' ', $criteria);
	}
	else {
		if (empty($criteria)) return ''; else $words = $criteria;
	}
	
	$compilation = '(';
	foreach ($words as $value) {
		$value2 = trim($value);
		if ((!preg_match("/^[^a-z0-9+_\-]$/i", $value2) && (strlen($value2) > 1)) || (!preg_match("/[^a-z0-9+_\-]/i", $value2) && (strlen($value2) == 1))) {
			$compilation .= "( CASE WHEN ".$column." LIKE '%".$value2."%' THEN 1 ELSE 0 END ) + ";
		}
	}
	$compilation = substr($compilation,0,strlen($compilation)-3);
	$compilation .= ') DESC, ';
	
	return $compilation;
}
} /* End_check for "relevance" */

