<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('has_attachments')) { /* Check_for "has_attachments" */
function has_attachments($client_id) {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_MyQuery = "SELECT COUNT(upload_id) as hw_many FROM application_uploads WHERE p_id = ".sql_int($client_id);
	$MyQuery = mysqli_query($config, $query_MyQuery) or die(mysqli_error($config));
	$row_MyQuery = mysqli_fetch_assoc($MyQuery);
	$totalRows_MyQuery = mysqli_num_rows($MyQuery);
	
	return $row_MyQuery['hw_many'];
}
} /* End_check for "has_attachments" */

if (!function_exists('post_user_link')) { /* Check_for "post_user_link" */
function post_user_link($url='', $type='') {
	if ($type == 'applicant') {
		return 'resume/'.$url;
	}
	elseif ($type == 'employer') {
		return $url;
	}
	else return '';
}
} /* End_check for "post_user_link" */

if (!function_exists('post_user_view')) { /* Check_for "post_user_view" */
function post_user_view($url='', $post=0, $type='') {
	if ($type == 'applicant') {
		return 'resume/'.$url.'/post/'.$post.'#comment';
	}
	elseif ($type == 'employer') {
		return $url.'/post/'.$post.'#comment';
	}
	else return '';
}
} /* End_check for "post_user_view" */

if (!function_exists('post_img_type')) { /* Check_for "post_img_type" */
function post_img_type($type='') {
	if ($type == 'applicant') {
		return 'personal';
	}
	elseif ($type == 'employer') {
		return 'employer';
	}
	else return '';
}
} /* End_check for "post_img_type" */

if (!function_exists('no_of_comments')) { /* Check_for "no_of_comments" */
function no_of_comments($post_id, $table_name, $type) {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Comments = "SELECT * FROM $table_name WHERE initial_id = ".sql_int($post_id)." AND initial_type = '".$type."' AND status NOT IN ('deleted', 'suspended')";
	$Comments = mysqli_query($config, $query_Comments) or die(mysqli_error($config));
	$totalRows_Comments = mysqli_num_rows($Comments);
	
	return number_format($totalRows_Comments, 0);
}
} /* End_check for "no_of_comments" */

if (!function_exists('no_of_views')) { /* Check_for "no_of_views" */
function no_of_views($post_id, $table_name) {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Views = "SELECT views FROM $table_name WHERE id = ".sql_int($post_id);
	$Views = mysqli_query($config, $query_Views) or die(mysqli_error($config));
	$row_Views = mysqli_fetch_assoc($Views);
	$totalRows_Views = mysqli_num_rows($Views);
	
	return number_format($row_Views['views'], 0);
}
} /* End_check for "no_of_views" */

if (!function_exists('no_of_like_dislike')) { /* Check_for "no_of_like_dislike" */
function no_of_like_dislike($post_id, $table_name, $type, $which='like') {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(action_option) as how_many FROM $table_name WHERE initial_id = ".sql_int($post_id)." AND initial_type = '".$type."' AND action_option = '".$which."'";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	return number_format($row_Actions['how_many'], 0);
}
} /* End_check for "no_of_like_dislike" */

if (!function_exists('toggle_like_dislike')) { /* Check_for "toggle_like_dislike" */
function toggle_like_dislike($post_id, $table_name, $type, $which='like') {
	global $sess_user_id, $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT action_option FROM $table_name WHERE initial_id = ".sql_int($post_id)." AND initial_type = '".$type."' AND action_option = '".$which."' AND user_id = $sess_user_id LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		echo " active-".$which;
	}
}
} /* End_check for "toggle_like_dislike" */

if (!function_exists('create_like_dislike')) { /* Check_for "create_like_dislike" */
function create_like_dislike($post_id, $table_name, $type) {
	global $sess_user_id, $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT action_option FROM $table_name WHERE initial_id = ".sql_int($post_id)." AND initial_type = '".$type."' AND user_id = $sess_user_id LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) { // If a choice exists
		if ($row_Actions['action_option'] == 'like') { 
			return '<li class="'.$type.'-like active-like" title="Remove Like"><i class="fa fa-thumbs-up"></i> Liked</li>';
		} elseif ($row_Actions['action_option'] == 'dislike') { 
			return '<li class="'.$type.'-dislike active-dislike" title="Remove Dislike"><i class="fa fa-thumbs-down"></i> Disliked</li>';
		}
	} else { // If no choice yet
		return '<li class="'.$type.'-like" title="Like This"><i class="fa fa-thumbs-up"></i></li>
			  <li class="'.$type.'-dislike" title="Dislike This"><i class="fa fa-thumbs-down"></i></li>';
	}
}
} /* End_check for "create_like_dislike" */

if (!function_exists('create_delete_post')) { /* Check_for "create_delete_post" */
function create_delete_post($post_user_id, $type='', $desired='') {
	global $sess_user_id, $i_am_company_admin;
	
	if ($sess_user_id == $post_user_id && ($type == 'applicant')) {
		return '<li class="delete-this-post">Delete<i class="fa fa-trash"></i></li>';
	} elseif ($i_am_company_admin && ($type == 'employer')) {
		return '<li class="delete-this-post">Delete<i class="fa fa-trash"></i></li>';
	} else return '';
}
} /* End_check for "create_delete_post" */

if (!function_exists('record_views')) { /* Check_for "record_views" */
function record_views($post_id) {
	global $sess_user_id, $database_config, $config;
	$date_now = date("Y-m-d");
	
	if (!$_SESSION['last_post_view_date'] || ($_SESSION['last_post_view_date'] != $date_now)) {
		$_SESSION['last_post_view_date'] = $date_now;
		
		$_SESSION['posts_viewed'] = array();
		$_SESSION['posts_viewed'][] = $post_id;
		
		mysqli_select_db($config, $database_config);
		$query_Actions = "UPDATE posts SET views = views + 1 WHERE id = ".sql_int($post_id)." LIMIT 1";
		$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	} else {
		if (!$_SESSION['posts_viewed']) {
			$_SESSION['posts_viewed'] = array();
			$_SESSION['posts_viewed'][] = $post_id;
			
			mysqli_select_db($config, $database_config);
			$query_Actions = "UPDATE posts SET views = views + 1 WHERE id = ".sql_int($post_id)." LIMIT 1";
			$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
		} else {
			if (!in_array($post_id, $_SESSION['posts_viewed'])) {
				$_SESSION['posts_viewed'][] = $post_id;
				
				mysqli_select_db($config, $database_config);
				$query_Actions = "UPDATE posts SET views = views + 1 WHERE id = ".sql_int($post_id)." LIMIT 1";
				$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
			}
		}
	}
}
} /* End_check for "record_views" */

