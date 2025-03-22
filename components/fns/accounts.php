<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('strlen_sort_asc')) { /* Check_for "strlen_sort_asc" */
function strlen_sort_asc($a,$b){
    return strlen($a)-strlen($b);
}
} /* End_check for "strlen_sort_asc" */

if (!function_exists('extract_short_name')) { /* Check_for "extract_short_name" */
function extract_short_name($name = '') {
	$name = strtolower($name);
	$name = explode(" ", trim($name));

	$final_name = '';

	if (trim($name[0])) {
		// Remove all non-alphabets
		foreach ($name as $key => $val) { 
			$name[$key] = preg_replace(array("/[^a-z]/i"), array(""), $val); 
		}
		
		// Sort names by length using the shortest possible name
		usort($name,'strlen_sort_asc');

		// Try to use a name that is at least 4 chars
		foreach ($name as $sub_name) { 
			if (strlen($sub_name) >= 4 && $final_name == '') {
				$final_name = $sub_name; 
			}
		}
		
		if (!$final_name) $final_name = trim($name[0]);
	}
	else $final_name = trim($name);

	return $final_name;
}
} /* End_check for "extract_short_name" */

if (!function_exists('create_username')) { /* Check_for "create_username" */
function create_username($name = '') {
	$short_name = extract_short_name($name);

	$random_no = mt_rand(11, 99);

	return $short_name.date("d").$random_no.date("s");
}
} /* End_check for "create_username" */

if (!function_exists('create_password')) { /* Check_for "create_password" */
function create_password() {
	$random_no = mt_rand(1000, 9999);

	//return strtolower(date("M").'am'.date("si"));
	return strtolower(date("M").'am'.$random_no);
}
} /* End_check for "create_password" */

if (!function_exists('create_student_number')) { /* Check_for "create_student_number" */
function create_student_number($p_id, $initial_number, $no_iterations = 1) {
	global $database_config, $config;
	
	// Only if student didn't have a number previously
	if (!$initial_number) {
		// Check for last number
		mysqli_select_db($config, $database_config);
		$query_Actions = "SELECT MAX(student_number) max_student_number 
						FROM prospects 
						LIMIT 1";
		$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
		$row_Actions = mysqli_fetch_assoc($Actions);
		$totalRows_Actions = mysqli_num_rows($Actions);
		
		if ($totalRows_Actions) { 
			// If the first student to get a number ever
			if ($row_Actions['max_student_number'] <= 2001900) {
				$expected_student_no = 2001901;
			}
			else {
				$expected_student_no = $row_Actions['max_student_number'] + 1;
			}

			// Give the student the number then check if he/she is the only one using it
			$number_outcome = assign_student_number($p_id, $expected_student_no);

			// If the number was successfully assigned
			if ($number_outcome) {
				// Check to ensure uniqueness
				$is_unique_student_number = is_unique_student_number($expected_student_no);

				// If not unique
				if (!$is_unique_student_number) {
					// Clear the number from the student and start again
					mysqli_select_db($config, $database_config);
					$updateSQL = "UPDATE prospects 
								SET student_number = 0 
								WHERE p_id = ".sql_int($p_id)." 
									AND student_number = ".sql_int($expected_student_no)." 
								LIMIT 1";
					$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
					$updateStatus = mysqli_affected_rows($config);

					// Repeat the process
					create_student_number($p_id, $initial_number, $no_iterations++);
				}
				else {
					return $expected_student_no;
				}
			}
			elseif ($no_iterations <= 10) {
				// Repeat the process
				create_student_number($p_id, $initial_number, $no_iterations++);
			}
		}
		
		return 0;
	}
	else return $initial_number;
}
} /* End_check for "create_student_number" */

if (!function_exists('assign_student_number')) { /* Check_for "assign_student_number" */
function assign_student_number($p_id, $student_number) {
	global $database_config, $config;

	mysqli_select_db($config, $database_config);
	$updateSQL = "UPDATE prospects 
				SET student_number = ".sql_int($student_number)." 
				WHERE p_id = ".sql_int($p_id)." 
					AND student_number = 0 
				LIMIT 1";
	$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
	$updateStatus = mysqli_affected_rows($config);

	if ($updateStatus > 0) { // If UPDATE is successful
		return true;
	}
	else { // If UPDATE failed
		return false;
	}
}
} /* End_check for "assign_student_number" */

if (!function_exists('is_unique_student_number')) { /* Check_for "is_unique_student_number" */
function is_unique_student_number($student_number) {
	global $database_config, $config;

	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(student_number) how_many 
					FROM prospects 
					WHERE student_number = ".sql_int($student_number)." ";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);

	if ($row_Actions['how_many'] != 1) {
		return false;
	}
	else {
		return true;
	}
}
} /* End_check for "is_unique_student_number" */

if (!function_exists('get_sessions_with_modules')) { /* Check_for "get_sessions_with_modules" */
function get_sessions_with_modules() {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT DISTINCT prog_date
                      FROM programme_modules";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		$available_sessions = array();

		do {
			$available_sessions[] = $row_Actions['prog_date'];
		} while ($row_Actions = mysqli_fetch_assoc($Actions));
		
		return implode(", ", $available_sessions);
	}
    else return 0;
}
} /* End_check for "get_sessions_with_modules" */

if (!function_exists('does_branch_prog_have_modules')) { /* Check_for "does_branch_prog_have_modules" */
function does_branch_prog_have_modules($branch_id = '', $prog_id = '', $prog_date = '') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT prog_mod_id
                      FROM programme_modules 
                      WHERE branch_id = ".sql_int($branch_id)." 
                            AND prog_id = ".sql_int($prog_id)." 
                            AND prog_date = ".sql_int($prog_date)." 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['prog_mod_id'];
    else return 0;
}
} /* End_check for "does_branch_prog_have_modules" */

if (!function_exists('does_student_have_course')) { /* Check_for "does_student_have_course" */
function does_student_have_course($user_id = '') {
	global $database_config, $config, $course_applicable_stages;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT p.prog_interest
					FROM users_students_prospect usp 
					JOIN prospects p ON usp.p_id = p.p_id 
					LEFT JOIN options progs ON p.prog_interest = progs.option_id 
					LEFT JOIN programme_dates ON p.prog_date = programme_dates.entry_id 
					WHERE usp.login_id = ".sql_int($user_id)." 
					AND p.deleted = '0' 
					AND p.stage IN (".implode(", ", $course_applicable_stages).") 
                	LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $totalRows_Actions;
    else return 0;
}
} /* End_check for "does_student_have_course" */

if (!function_exists('does_branch_prog_have_lecture_content')) { /* Check_for "does_branch_prog_have_lecture_content" */
function does_branch_prog_have_lecture_content($branch_id = '', $prog_id = '', $prog_date = '') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT pm.prog_mod_id
                      FROM programme_modules pm 
					  JOIN module_components mc ON mc.module_id = pm.module_id 
					  JOIN components c ON c.component_id = mc.component_id
                      WHERE pm.branch_id = ".sql_int($branch_id)." 
                            AND pm.prog_id = ".sql_int($prog_id)." 
							AND pm.prog_date = ".sql_int($prog_date)." 
							AND c.status = 'active'
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['prog_mod_id'];
    else return 0;
}
} /* End_check for "does_branch_prog_have_lecture_content" */

if (!function_exists('does_branch_prog_have_offline_material')) { /* Check_for "does_branch_prog_have_offline_material" */
function does_branch_prog_have_offline_material($branch_id = '', $prog_id = '', $prog_date = '') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT pm.prog_mod_id
                      FROM programme_modules pm 
					  JOIN module_materials mm ON mm.module_id = pm.module_id 
                      WHERE pm.branch_id = ".sql_int($branch_id)." 
                            AND pm.prog_id = ".sql_int($prog_id)." 
							AND pm.prog_date = ".sql_int($prog_date)." 
							AND mm.status = 'active' 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['prog_mod_id'];
    else return 0;
}
} /* End_check for "does_branch_prog_have_offline_material" */

if (!function_exists('does_branch_prog_have_lecture_materials')) { /* Check_for "does_branch_prog_have_lecture_materials" */
function does_branch_prog_have_lecture_materials($branch_id = '', $prog_id = '', $prog_date = '') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT pm.prog_mod_id
                      FROM programme_modules pm 
					  JOIN module_materials mm ON mm.module_id = pm.module_id 
                      WHERE pm.branch_id = ".sql_int($branch_id)." 
                            AND pm.prog_id = ".sql_int($prog_id)." 
							AND pm.prog_date = ".sql_int($prog_date)." 
							AND mm.status = 'active'
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['prog_mod_id'];
    else return 0;
}
} /* End_check for "does_branch_prog_have_lecture_materials" */

if (!function_exists('get_prog_mod_id')) { /* Check_for "get_prog_mod_id" */
function get_prog_mod_id($branch_id = '', $prog_id = '', $prog_date = '', $assgnt_id = '') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT pm.prog_mod_id
                      FROM programme_modules pm
					  JOIN alt_assignments alta ON pm.module_id = alta.module_id
                      WHERE pm.branch_id = ".sql_int($branch_id)." 
                            AND pm.prog_id = ".sql_int($prog_id)." 
                            AND pm.prog_date = ".sql_int($prog_date)." 
                            AND alta.assignment_id = ".sql_int($assgnt_id)." 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['prog_mod_id'];
    else return 0;
}
} /* End_check for "get_prog_mod_id" */

if (!function_exists('mark_seen_assignment')) { /* Check_for "mark_seen_assignment" */
function mark_seen_assignment($assignment_update_id = '') {
	global $database_config, $config;
    
	// Mark as seen
	mysqli_select_db($config, $database_config);
	$updateSQL = "UPDATE alt_assignment_updates 
				SET seen = '".date("Y-m-d H:i:s")."' 
				WHERE assignment_update_id = ".sql_int($assignment_update_id)."  
				AND seen = '0000-00-00 00:00:00'
				LIMIT 1";
	$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
	$updateStatus = mysqli_affected_rows($config);
}
} /* End_check for "mark_seen_assignment" */

if (!function_exists('is_assignment_lecturer')) { /* Check_for "is_assignment_lecturer" */
function is_assignment_lecturer($user_id = 0, $branch_id = 0) {
	global $database_config, $config;

	if ($branch_id) $sql_modifier = " AND branch_id = '".sql_int($branch_id)."' ";
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT branch_id
                      FROM branch_assignment_lecturers 
                      WHERE user_id = ".sql_int($user_id)." 
                            ".$sql_modifier."";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		$applicable_branches = array();
		
		do {
			$applicable_branches[] = $row_Actions['branch_id'];
		} 
		while ($row_Actions = mysqli_fetch_assoc($Actions));
		
		if (count($applicable_branches)) {
			return implode(", ", $applicable_branches);
		}
		else return false;
	}
    else return 0;
}
} /* End_check for "is_assignment_lecturer" */

if (!function_exists('is_ticket_responder')) { /* Check_for "is_ticket_responder" */
function is_ticket_responder($user_id = 0, $branch_id = 0, $return_types = false) {
	global $database_config, $config;

	if ($branch_id) $sql_modifier = " AND branch_id = '".sql_int($branch_id)."' ";
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT branch_id, ticket_type
                      FROM branch_ticket_responders 
                      WHERE user_id = ".sql_int($user_id)." 
                            ".$sql_modifier."";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		$applicable_branches = $applicable_types = array();
		
		do {
			$applicable_branches[] = $row_Actions['branch_id'];
			$applicable_types[] = $row_Actions['ticket_type'];
		} 
		while ($row_Actions = mysqli_fetch_assoc($Actions));
		
		if ($return_types) {
			if (count($applicable_types)) {
				return implode(", ", $applicable_types);
			}
			else return false;
		}
		
		if (count($applicable_branches)) {
			return implode(", ", $applicable_branches);
		}
		else return false;
	}
    else return 0;
}
} /* End_check for "is_ticket_responder" */

if (!function_exists('mark_seen_ticket')) { /* Check_for "mark_seen_ticket" */
function mark_seen_ticket($ticket_id = '') {
	global $database_config, $config;
    
	// Mark as seen
	mysqli_select_db($config, $database_config);
	$updateSQL = "UPDATE student_tickets 
				SET seen = '".date("Y-m-d H:i:s")."' 
				WHERE ticket_id = ".sql_int($ticket_id)."  
				AND seen = '0000-00-00 00:00:00'
				LIMIT 1";
	$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
	$updateStatus = mysqli_affected_rows($config);
}
} /* End_check for "mark_seen_ticket" */

if (!function_exists('mark_seen_ticket_reply')) { /* Check_for "mark_seen_ticket_reply" */
function mark_seen_ticket_reply($response_id = '', $contact_message = false) { // $contact_message should be true if it is a message from the contact us page
	global $database_config, $config;
	
	if ($contact_message) {
		$table = "contact_us_reply";
	} else {
		$table = "student_tickets_reply";
	}
	// Mark as seen
	mysqli_select_db($config, $database_config);
	$updateSQL = "UPDATE $table
				SET seen = '".date("Y-m-d H:i:s")."' 
				WHERE response_id = ".sql_int($response_id)." 
				AND seen = '0000-00-00 00:00:00'
				LIMIT 1";
	$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
	$updateStatus = mysqli_affected_rows($config);
}
} /* End_check for "mark_seen_ticket_reply" */

// Randomly generate student number()
function generate_random_student_number() 
{
    // Define the range for student numbers
    $min = 2001901;
    $max = 9999999; // Adjust this value as needed

    // Generate a random number within the specified range
    return rand($min, $max);
}