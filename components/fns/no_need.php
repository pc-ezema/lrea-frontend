<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('auto_url_naming')) { /* Check_for "auto_url_naming" */
function auto_url_naming($table_name, $column_id, $column_naming, $column_string) {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_wholeTable = "SELECT $column_id, $column_naming, $column_string FROM $table_name";
	$wholeTable = mysqli_query($config, $query_wholeTable) or die(mysqli_error($config));
	$row_wholeTable = mysqli_fetch_assoc($wholeTable);
	$totalRows_wholeTable = mysqli_num_rows($wholeTable);
	
	do {
		if (!$row_wholeTable[$column_naming]) {
			$new_slug = make_slug($row_wholeTable[$column_string], $table_name, $column_naming);
			
			if ($new_slug) {
				  $updateSQL = "UPDATE $table_name SET $column_naming = '$new_slug' WHERE $column_id = ".$row_wholeTable[$column_id];
				  mysqli_select_db($config, $database_config);
				  $Result1 = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
			}
		}
	} while ($row_wholeTable = mysqli_fetch_assoc($wholeTable));
	
	if ($totalRows_wholeTable) {
		return true;
	} else return false;
}
} /* End_check for "auto_url_naming" */

if (!function_exists('personal_url_naming')) { /* Check_for "personal_url_naming" */
function personal_url_naming($table_name, $column_id, $column_naming, $column_string) {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_wholeTable = "SELECT $column_id, $column_naming, CONCAT(first_name, ' ', last_name) AS $column_string FROM $table_name";
	$wholeTable = mysqli_query($config, $query_wholeTable) or die(mysqli_error($config));
	$row_wholeTable = mysqli_fetch_assoc($wholeTable);
	$totalRows_wholeTable = mysqli_num_rows($wholeTable);
	
	do {
		if (!$row_wholeTable[$column_naming]) {
			$new_slug = make_slug($row_wholeTable[$column_string], $table_name, $column_naming);
			
			if ($new_slug) {
				  $updateSQL = "UPDATE $table_name SET $column_naming = '$new_slug' WHERE $column_id = ".$row_wholeTable[$column_id];
				  mysqli_select_db($config, $database_config);
				  $Result1 = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
			}
		}
	} while ($row_wholeTable = mysqli_fetch_assoc($wholeTable));
	
	if ($totalRows_wholeTable) {
		return true;
	} else return false;
}
} /* End_check for "personal_url_naming" */

