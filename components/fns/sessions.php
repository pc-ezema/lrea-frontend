<?php
 
session_set_save_handler('_open',
                         '_close',
                         '_read',
                         '_write',
                         '_destroy',
                         '_clean');
						 
if (!function_exists('_open')) { /* Check_for "_open" */
function _open()
{
    global $_sess_db, $sessions_db_name, $hostname_config, $database_config, $username_config, $password_config;
 
    if ($_sess_db = mysqli_connect($hostname_config, $username_config, $password_config, $sessions_db_name)) {
        return mysqli_select_db($_sess_db, $sessions_db_name);
    }
 
    return FALSE;
}
} /* End_check for "_open" */

if (!function_exists('_close')) { /* Check_for "_close" */
function _close()
{
    global $_sess_db, $sessions_db_name;
 
    //return mysqli_close($_sess_db);
}
} /* End_check for "_close" */

if (!function_exists('_read')) { /* Check_for "_read" */
function _read($id)
{
    global $_sess_db, $sessions_db_name;
 
    $id = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($id) : our_mysqli_real_escape_string($id);
 
    $sql = "SELECT data
            FROM   ".$sessions_db_name.".sessions
            WHERE  id = '$id'";
 
    if ($result = mysqli_query($_sess_db, $sql)) {
        if (mysqli_num_rows($result)) {
            $record = mysqli_fetch_assoc($result);
 
            return $record['data'];
        }
    }
 
    return '';
}
} /* End_check for "_read" */

if (!function_exists('_write')) { /* Check_for "_write" */
function _write($id, $data)
{
    global $_sess_db, $sessions_db_name;
 
    $access = time();
 
    $id = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($id) : our_mysqli_real_escape_string($id);
	$access = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($access) : our_mysqli_real_escape_string($access);
	$data = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($data) : our_mysqli_real_escape_string($data);
 
    $sql = "REPLACE
            INTO    ".$sessions_db_name.".sessions
            VALUES  ('$id', '$access', '$data')";
			
	/*$sql = "INSERT
            INTO    ".$sessions_db_name.".sessions (id, access, data) 
            VALUES  ('$id', '$access', '$data')
			ON DUPLICATE KEY UPDATE access='$access', data='$data'";*/
 
    return mysqli_query($_sess_db, $sql);
}
} /* End_check for "_write" */

if (!function_exists('_destroy')) { /* Check_for "_destroy" */
function _destroy($id)
{
    global $_sess_db, $sessions_db_name;
 
    $id = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($id) : our_mysqli_real_escape_string($id);
 
    $sql = "DELETE
            FROM   ".$sessions_db_name.".sessions
            WHERE  id = '$id'";
 
    return mysqli_query($_sess_db, $sql);
}
} /* End_check for "_destroy" */

if (!function_exists('_clean')) { /* Check_for "_clean" */
function _clean($max)
{
    global $_sess_db, $sessions_db_name, $site_version;
 
    //$old = time() - $max;
	
	if ($site_version == "localhost") {
		$old = time() - $max;
	} else $old = time() - (60*60*24*180);
	
	$old = function_exists("our_mysqli_real_escape_string") ? our_mysqli_real_escape_string($old) : our_mysqli_real_escape_string($old);
 
    $sql = "DELETE
            FROM   ".$sessions_db_name.".sessions
            WHERE  access < '$old'";
 
    return mysqli_query($_sess_db, $sql);
}
} /* End_check for "_clean" */

?>