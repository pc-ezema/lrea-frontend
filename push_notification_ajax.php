<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
  
    if ($_POST["action"] == "new") {      
        $insertSQL = "INSERT INTO push_notif_tokens (token, date_added, send_after)
                        VALUES (
                            '".sql_string($_POST['token'])."',
                            '".sql_string($time_now)."',
                            '".sql_string(date("Y-m-d H:i:s", strtotime('+5 minutes')))."'
                        )";
                        
        mysqli_select_db($config, $database_config);
        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
        $insertStatus = mysqli_affected_rows($config);

        if ($insertStatus >= 0) { 
            echo "Token saved";
            exit();
        } 
        else { 
            echo "Failed to saved token";
            exit();
        }
    }

    if ($_POST["action"] == "update") {  
        // update the token
        mysqli_select_db($config, $database_config);
        $updateSQL =    "UPDATE push_notif_tokens
                        SET token = '".sql_string($_POST['new_Token'])."',
                            date_added = '".sql_string($time_now)."',
                            status = 'pending'
                        WHERE token = '".sql_string($_POST['old_Token'])."'
                        LIMIT 1";
        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
        $updateStatus = mysqli_affected_rows($config);

        if ($updateStatus) { 
            echo "Token Updated Successfully";
            exit();
        } 
        else { 
            echo "Failed to update token";
            exit();
        }
    }

    echo "Something went wrong";
    exit();
?>