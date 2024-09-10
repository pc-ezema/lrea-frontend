<?php
    // Prepare TikTok event details
    $tiktok_clid = $tiktok_email = "";

    $link_root_default = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;

    $tiktok_ip_addr = get_real_ip_addr();
    $tiktok_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36";

    if ($_COOKIE['ttclid']) {
        $tiktok_clid = " ttclid: '".$_COOKIE['ttclid']."',";
    }

    $found_value = "";

    // Is the visitor signed in
    if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student' && $_SESSION['email']) {
        $hashed_student_email = hash('sha256', trim($_SESSION['email']));
        $tiktok_email = "em: '".$hashed_student_email."',";
    }
    else {
        if ($tiktok_ip_addr != '127.0.0.1') {
            if ($found_value = get_latest_fb_info_from_db("ip_address", $tiktok_ip_addr, "email_hash")) { // Check for Email Hash
                $tiktok_email = "em: '".$found_value."',";
            }
        }
    }
    
    $tiktok_evt_url = curPageURL();

    if ($site_version == "localhost" && !$tiktok_clid) {
        $tiktok_clid = " ttclid: 'bcdefg123456',";
    }