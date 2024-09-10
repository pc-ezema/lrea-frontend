<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

// Commence Form Validation functions
$form_errors = array();

if (!function_exists('validate')) { /* Check_for "validate" */
function validate($field="gdchdhj", $type="string", $string_length=2, $max_length=0) {
/* Do NOT forget to initialize $form_errors = array() outside this function */
	
	global $form_errors;
	//valid types are string, int, email, float, password, strlen, phone, amount, date
    
    if (!array_key_exists($field, $_POST)) {
        return FALSE;
    }
    
	$_POST[$field] = preg_replace( 
        array( 
          // Remove invisible content 
            '@<head[^>]*?>.*?</head>@siu', 
            '@<style[^>]*?>.*?</style>@siu', 
            '@<script[^>]*?.*?</script>@siu', 
            '@<object[^>]*?.*?</object>@siu', 
            '@<embed[^>]*?.*?</embed>@siu', 
            '@<applet[^>]*?.*?</applet>@siu', 
            '@<noframes[^>]*?.*?</noframes>@siu', 
            '@<noscript[^>]*?.*?</noscript>@siu', 
            '@<noembed[^>]*?.*?</noembed>@siu',
        ), 
        array( 
			'', '', '', '', '', '', '', '', ''), $_POST[$field] );
			
	$no_tags = strip_tags($_POST[$field]);
	
	// Trim white spaces if not numeric value
	if (!preg_match('/^\d+(\.\d+)?$/', $_POST[$field])) {
		$field_value = trim($no_tags);
	}
	else $field_value = $no_tags;
	//echo '<pre>'.$field.' -> '.$field_value.'</pre>';
	
	if (($field_value == "") || ($field_value != "0" && empty($field_value))) {
		$form_errors[$field] = '<p class="help-block text-danger">Required</p>';
		return FALSE;
	}
	
	if ($type == "string") {
		if (!is_string($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "int") {
		if (!my_is_int($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "float") {
		if (!my_is_float($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "email") {
		$pattern = "/^[a-zA-Z0-9]+(([_+.-]{1})?[a-zA-Z0-9]+)*@[a-zA-Z0-9]+((-{1})?[a-zA-Z0-9]+)*(\.[a-zA-Z0-9]{2,})+$/i";
		if (!preg_match($pattern, $_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Email</p>';
			return FALSE;
		}
	}
	elseif ($type == "password") {
		if (strlen($field_value) < 8) {
			$form_errors[$field] = '<p class="help-block text-danger">Password should be at least 8 chars</p>';
			return FALSE;
		}
	}
	elseif ($type == "strlen") {
		if (strlen($field_value) < $string_length) {
			$form_errors[$field] = '<p class="help-block text-danger">At least '.$string_length.' chars</p>';
			return FALSE;
		}
		elseif (($max_length > $string_length) && (strlen($field_value) > $max_length)) {
			$form_errors[$field] = '<p class="help-block text-danger">Not more than '.$max_length.' chars</p>';
			return FALSE;
		}
	}
	elseif ($type == "phone") {
		$phone_number_is_invalid = false;
		
		$pattern = "/\d{3,}/i";
		if (!preg_match($pattern, $_POST[$field])) {
			$phone_number_is_invalid = true;
		}

		if (!$phone_number_is_invalid) {
			// Remove anything that is not a digit
			$string = preg_replace(array("/[^\d]/i"), array(""), $_POST[$field]);
			
			// Get the number of digits
			$length = mb_strlen($string);

			// If less than 5 or more than 15, it is invalid
			if ($length < 5 || $length > 15) {
				$phone_number_is_invalid = true;
			}
		}

		if ($phone_number_is_invalid) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Phone Number.</p>';
			return FALSE;
		}
	}
	elseif ($type == "amount") {
		if (!my_is_amount($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "date") {
		$pattern = "/^\d{4}-\d{2}-\d{2}$/";
		if (!preg_match($pattern, $_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Date Format</p>';
			return FALSE;
		}
		else {
			$dt = $_POST[$field];
			
			$arr = explode("-", $dt); // splitting the array
			$yy = $arr[0]; // first element of the array is year
			$mm = $arr[1]; // second element is month
			$dd = $arr[2]; // third element is day
			if (!checkdate($mm,$dd,$yy)) {
				$form_errors[$field] = '<p class="help-block text-danger">Incorrect Date</p>';
			}
			return FALSE;
		}
	}
	
	return true;
}
} /* End_check for "validate" */

if (!function_exists('validate_norequired')) { /* Check_for "validate_norequired" */
function validate_norequired($field="gdchdhj", $type="string", $string_length=2, $max_length=0) {
/* Do NOT forget to initialize $form_errors = array() outside this function */
	
	global $form_errors;
	//valid types are string, int, email, float, password, strlen, phone, amount, date
    
    if (!array_key_exists($field, $_POST)) {
        return FALSE;
    }
    
	$_POST[$field] = preg_replace( 
        array( 
          // Remove invisible content 
            '@<head[^>]*?>.*?</head>@siu', 
            '@<style[^>]*?>.*?</style>@siu', 
            '@<script[^>]*?.*?</script>@siu', 
            '@<object[^>]*?.*?</object>@siu', 
            '@<embed[^>]*?.*?</embed>@siu', 
            '@<applet[^>]*?.*?</applet>@siu', 
            '@<noframes[^>]*?.*?</noframes>@siu', 
            '@<noscript[^>]*?.*?</noscript>@siu', 
            '@<noembed[^>]*?.*?</noembed>@siu',
        ), 
        array( 
			'', '', '', '', '', '', '', '', ''), $_POST[$field] );
			
    $no_tags = strip_tags($_POST[$field]);
	
	// Trim white spaces if not numeric value
	if (!preg_match('/^\d+(\.\d+)?$/', $_POST[$field])) {
		$field_value = trim($no_tags);
	}
	else $field_value = $no_tags;
	
	if (($field_value == "") || ($field_value != "0" && empty($field_value))) {
		return FALSE;
	}
	
	if ($type == "string") {
		if (!is_string($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "int") {
		if (!my_is_int($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "float") {
		if (!my_is_float($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "email") {
		$pattern = "/^[a-zA-Z0-9]+(([_+.-]{1})?[a-zA-Z0-9]+)*@[a-zA-Z0-9]+((-{1})?[a-zA-Z0-9]+)*(\.[a-zA-Z0-9]{2,})+$/i";
		if (!preg_match($pattern, $_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Email</p>';
			return FALSE;
		}
	}
	elseif ($type == "password") {
		if (strlen($field_value) < 8) {
			$form_errors[$field] = '<p class="help-block text-danger">Password should be atleast 8 chars</p>';
			return FALSE;
		}
	}
	elseif ($type == "strlen") {
		if (strlen($field_value) < $string_length) {
			$form_errors[$field] = '<p class="help-block text-danger">Atleast '.$string_length.' chars</p>';
			return FALSE;
		}
		elseif (($max_length > $string_length) && (strlen($field_value) > $max_length)) {
			$form_errors[$field] = '<p class="help-block text-danger">Not more than '.$max_length.' chars</p>';
			return FALSE;
		}
	}
	elseif ($type == "phone") {
		$phone_number_is_invalid = false;
		
		$pattern = "/\d{3,}/i";
		if (!preg_match($pattern, $_POST[$field])) {
			$phone_number_is_invalid = true;
		}

		if (!$phone_number_is_invalid) {
			// Remove anything that is not a digit
			$string = preg_replace(array("/[^\d]/i"), array(""), $_POST[$field]);
			
			// Get the number of digits
			$length = mb_strlen($string);

			// If less than 5 or more than 15, it is invalid
			if ($length < 5 || $length > 15) {
				$phone_number_is_invalid = true;
			}
		}

		if ($phone_number_is_invalid) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Phone Number.</p>';
			return FALSE;
		}
	}
	elseif ($type == "amount") {
		if (!my_is_amount($_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
			return FALSE;
		}
	}
	elseif ($type == "date") {
		$pattern = "/^\d{4}-\d{2}-\d{2}$/";
		if (!preg_match($pattern, $_POST[$field])) {
			$form_errors[$field] = '<p class="help-block text-danger">Invalid Date Format</p>';
			return FALSE;
		}
		else {
			$dt = $_POST[$field];
			
			$arr = explode("-", $dt); // splitting the array
			$yy = $arr[0]; // first element of the array is year
			$mm = $arr[1]; // second element is month
			$dd = $arr[2]; // third element is day
			if (!checkdate($mm,$dd,$yy)) {
				$form_errors[$field] = '<p class="help-block text-danger">Incorrect Date</p>';
			}
			return FALSE;
		}
	}
	
	return true;
}
} /* End_check for "validate_norequired" */

if (!function_exists('validate_array')) { /* Check_for "validate_array" */
function validate_array($field, $type="int") {
	global $form_errors;
	
	if (!array_key_exists($field, $_POST) || !is_array($field) || !$_POST[$field]) {
        $form_errors[$field] = '<p class="help-block text-danger">Invalid</p>';
		return FALSE;
    }
	
	if ($type == "int") {
		foreach ($_POST[$field] as $key => $value) {
			if (!my_is_int($value)) {
				$form_errors[$field] = '<p class="help-block text-danger">Invalid Value</p>';
				return FALSE;
			}
		}
	}
	
	return true;
}
} /* End_check for "validate_array" */

if (!function_exists('displayError')) { /* Check_for "displayError" */
function displayError($field="gdchdhj") {
	global $form_errors;
	
	if (array_key_exists($field, $form_errors)) {
		echo '<span class="csValidErrMsg danger label" csValidName="'.$field.'">'.$form_errors[$field].'</span>';
	} else {
		echo '<span class="csValidErrMsg" csValidName="'.$field.'"></span>';
	}
}
} /* End_check for "displayError" */

if (!function_exists('returnValue')) { /* Check_for "returnValue" */
function returnValue($field="gdchdhj") {
    global $form_errors;
	
	if (array_key_exists($field, $form_errors)) {
		echo $_POST[$field];
	}
}
} /* End_check for "returnValue" */

if (!function_exists('formatMySQLDate')) { /* Check_for "formatMySQLDate" */
function formatMySQLDate($month, $day, $year, $date_name) {
	global $form_errors;
	$mysqlFormat = null;
	$m = trim($month);
	$d = trim($day);
	$y = trim($year);
	if (empty($d) || empty($y) || !$m) {
		$form_errors[$date_name] = '<p class="help-block text-danger">Please fill in all fields</p>';
	} elseif (!my_is_int($d) || !my_is_int($y)) {
		$form_errors[$date_name] = '<p class="help-block text-danger">Please use numbers only</p>';
	} elseif (($d < 1 || $d > 31) || ($y < 1000 || $y > 9999)) {
		$form_errors[$date_name] = '<p class="help-block text-danger">Please use numbers within a valid range</p>';
	} elseif (!checkdate($m,$d,$y)) {
		$form_errors[$date_name] = '<p class="help-block text-danger">You have used an invalid date</p>';
	} else {
		$d = $d < 10 ? '0'.$d : $d;
		$mysqlFormat = "'$y-$m-$d'";
	}
  return $mysqlFormat;
}
} /* End_check for "formatMySQLDate" */

if (!function_exists('post_value')) { /* Check_for "post_value" */
function post_value($field="gdchdhj") {
	if (isset($_POST[$field])) { echo $_POST[$field]; }
}
} /* End_check for "post_value" */

/* End Form Validation functions

   SAMPLE USAGE:-
   
   if (isset($_POST['check_it'])) {
		validate("test", "float");
    	validate("exam", "int");
	}

<form action="" name="test_form" method="post">
	<input type="text" name="test" size="25" value="<?php post_value('test') ?>"  class="form-control"/> <?php displayError("test"); ?> <br />
    <input type="text" name="exam" size="25" value="<?php post_value('exam') ?>"  class="form-control"/> <?php displayError("exam"); ?> <br />
	<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="check_it" value="check it"  class="form-control"/></div><br />
</form>

*/

