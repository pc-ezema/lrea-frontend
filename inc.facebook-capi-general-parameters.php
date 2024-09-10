<?php 
    // Prepare additional CAPI browser event and server details
    $capi_additional_object = array();
    $capi_fbc = $capi_fbp = $capi_email = "";

    $link_root_default = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;

    $capi_ip_addr = get_real_ip_addr(); // IP address and user agent are automatically added to the events sent through the browser. So this is only prepared for server event. (Reference: https://developers.facebook.com/docs/marketing-api/conversions-api/parameters/customer-information-parameters/#client-ip-address)
    $capi_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36";

    if (isset($_COOKIE['_fbp'])) {
        $capi_additional_object["fbp"] = $_COOKIE['_fbp']; // Prepared for browser event.
        $capi_fbp = " efbp: '".$_COOKIE['_fbp']."',"; // Prepared for server event. (AJAX)
    }

    if (isset($_COOKIE['_fbc'])) {
        $capi_additional_object["fbc"] = $_COOKIE['_fbc']; // Prepared for browser event.
        $capi_fbc = "efbc: '".$_COOKIE['_fbc']."',"; // Prepared for server event. (AJAX)
    }

    $found_value = "";

    // Is the visitor signed in
    if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student' && $_SESSION['email']) { 
        $hashed_student_email = hash('sha256', $_SESSION['email']);
        $capi_additional_object["em"] = $hashed_student_email; // Prepared for browser event.

        $capi_email = "em: '".$hashed_student_email."',"; // Prepared for server event. (AJAX)

        if (!$capi_additional_object["fbp"] && !$capi_additional_object["fbc"]) {
            if ($found_value = get_latest_fb_info_from_db("email_hash", $hashed_student_email, "fbp")) { // Check for FBP
                $capi_additional_object["fbp"] = $found_value; // Prepared for browser event.
                $capi_fbp = " efbp: '".$found_value."',"; // Prepared for server event. (AJAX)
            }
            elseif ($found_value = get_latest_fb_info_from_db("email_hash", $hashed_student_email, "fbc")) { // Check for FBC
                $capi_additional_object["fbc"] = $found_value; // Prepared for browser event.
                $capi_fbc = " efbc: '".$found_value."',"; // Prepared for server event. (AJAX)
            }
        }
    }
    else {
        if (!$capi_additional_object["fbp"] || !$capi_additional_object["fbc"]) {
            if ($capi_additional_object["fbp"]) {
                if ($found_value = get_latest_fb_info_from_db("fbp", $capi_additional_object["fbp"], "email_hash")) { // Check for Email Hash
                    $capi_additional_object["em"] = $found_value; // Prepared for browser event.
                    $capi_email = "em: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
                elseif ($found_value = get_latest_fb_info_from_db("fbp", $capi_additional_object["fbp"], "fbc")) { // Check for FBC
                    $capi_additional_object["fbc"] = $found_value; // Prepared for browser event.
                    $capi_fbc = " efbc: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
            }
            elseif ($capi_additional_object["fbc"]) {
                if ($found_value = get_latest_fb_info_from_db("fbc", $capi_additional_object["fbc"], "email_hash")) { // Check for Email Hash
                    $capi_additional_object["em"] = $found_value; // Prepared for browser event.
                    $capi_email = "em: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
                elseif ($found_value = get_latest_fb_info_from_db("fbc", $capi_additional_object["fbc"], "fbp")) { // Check for FBP
                    $capi_additional_object["fbp"] = $found_value; // Prepared for browser event.
                    $capi_fbp = " efbp: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
            }
            elseif ($capi_ip_addr != '127.0.0.1') {
                $no_of_first_party_param_gotten = 0;

                if ($found_value = get_latest_fb_info_from_db("ip_address", $capi_ip_addr, "email_hash")) { // Check for Email Hash
                    $capi_additional_object["em"] = $found_value; // Prepared for browser event.
                    $capi_email = "em: '".$found_value."',"; // Prepared for server event. (AJAX)

                    $no_of_first_party_param_gotten++;
                }
                
                if ($found_value = get_latest_fb_info_from_db("ip_address", $capi_ip_addr, "fbp")) { // Check for FBP
                    $capi_additional_object["fbp"] = $found_value; // Prepared for browser event.
                    $capi_fbp = " efbp: '".$found_value."',"; // Prepared for server event. (AJAX)

                    $no_of_first_party_param_gotten++;
                }
                
                // Only if FBP is not available
                if (!$capi_additional_object["fbp"]) {
                    if ($capi_additional_object["em"] && $found_value = get_latest_fb_info_from_db("email_hash", $capi_additional_object["em"], "fbc")) { // Check for FBC
                        $capi_additional_object["fbc"] = $found_value; // Prepared for browser event.
                        $capi_fbc = " efbc: '".$found_value."',"; // Prepared for server event. (AJAX)
                    }
                    elseif ($found_value = get_latest_fb_info_from_db("ip_address", $capi_ip_addr, "fbc")) { // Check for FBC
                        $capi_additional_object["fbc"] = $found_value; // Prepared for browser event.
                        $capi_fbc = " efbc: '".$found_value."',"; // Prepared for server event. (AJAX)
                    }
                }
            }
        }

        if (!$capi_additional_object["em"]) {
            if ($capi_additional_object["fbp"] && !$capi_additional_object["em"]) {
                if ($found_value = get_latest_fb_info_from_db("fbp", $capi_additional_object["fbp"], "email_hash")) { // Check for Email Hash
                    $capi_additional_object["em"] = $found_value; // Prepared for browser event.
                    $capi_email = "em: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
            }
            
            if ($capi_additional_object["fbc"] && !$capi_additional_object["em"]) {
                if ($found_value = get_latest_fb_info_from_db("fbc", $capi_additional_object["fbc"], "email_hash")) { // Check for Email Hash
                    $capi_additional_object["em"] = $found_value; // Prepared for browser event.
                    $capi_email = "em: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
            }
            
            if (!$capi_additional_object["em"] && ($capi_ip_addr != '127.0.0.1')) {
                if ($found_value = get_latest_fb_info_from_db("ip_address", $capi_ip_addr, "email_hash")) { // Check for Email Hash
                    $capi_additional_object["em"] = $found_value; // Prepared for browser event.
                    $capi_email = "em: '".$found_value."',"; // Prepared for server event. (AJAX)
                }
            }
        }
    }
    
    if ($site_version == "localhost" && !count($capi_additional_object)) { 
        $capi_additional_object["fbp"] = 'abcdefg123456'; // Prepared for browser event.
        $capi_fbp = " efbp: 'bcdefg123456',"; // Prepared for server event. (AJAX)

        $capi_additional_object["fbc"] = 'hijklmno7890'; // Prepared for browser event.
        $capi_fbc = "efbc: 'hijklmno7890',"; // Prepared for server event. (AJAX)
    }
    
    $fb_evt_url = curPageURL();

    $able_to_match_to_fb_account = ($_COOKIE['_fbp'] || $_COOKIE['_fbc']) ? true : false;