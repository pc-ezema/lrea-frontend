<?php
    // Shared parameters amongst each event type
    $data["event_name"] = $facebook_api_conversion_event_name;
    $data["event_time"] = time();
    $data["event_id"] = $facebook_api_event_id_to_use; // Event id must be the same for both browser event and the server conversion API.
    $data["action_source"] = $facebook_event_action_source;
    $data["user_data"] = array(
        "client_ip_address"  => $facebook_api_client_ip,
        "client_user_agent"  => $facebook_api_client_agent
    );
    
    if ($fbp) { // Use this fbp value being passed from the generated AJAX script in the javascript
        $data["user_data"]["fbp"] = $fbp;
    }
    elseif ($_COOKIE['_fbp']) { // Facebook sets this unique cookie on user's browser to identify the user. It is set by the installed pixels and according to documentation, it can be be sent with the server event.
        $data["user_data"]["fbp"] = $_COOKIE['_fbp'];
    }

    if ($fbc) { // Use this fbc value being passed from the generated AJAX script in the javascript if it exists.
        $data["user_data"]["fbc"] = $fbc;
    }
    elseif ($_COOKIE['_fbc']) { // This cookie value does not always exist. It only exists when a user clicks on our advert from Facebook before they get taken to our website.
        $data["user_data"]["fbc"] = $_COOKIE['_fbc'];
    }

    if ($facebook_api_user_email) { // Use this email value being passed from the AJAX if it exists.
        $data["user_data"]["em"] = $facebook_api_user_email;
    }
    elseif ($sess_user_id > 0 && $_SESSION['user_type'] == 'student' && $_SESSION['email']) { // Alternative incase i mistakenly skipped where it should've been added in the AJAX request.
        $data["user_data"]["em"] = hash('sha256', $_SESSION['email']);
    }

    if (is_array($facebook_api_content_ids) && count($facebook_api_content_ids)) {
        $data["custom_data"]["content_ids"] = $facebook_api_content_ids; // This value must be an array.
    }

    if ($facebook_api_purchase_currency) {
        $data["custom_data"]["currency"] = $facebook_api_purchase_currency;
    }

    if ($facebook_api_purchase_amount) {
        $data["custom_data"]["value"] = $facebook_api_purchase_amount;
    }
    
    // Unique parameters amongst each event type
    if ($facebook_api_conversion_event_name == 'AddToCart') { // 'AddToCart Event'
        $data["event_source_url"] = $facebook_event_source_url;
        $data["custom_data"]["content_name"] = $facebook_api_page_to_track;
    } elseif ($facebook_api_conversion_event_name == 'AddPaymentInfo') { // AddPaymentInfo
        // This was commented because there will be values in the custom_data array as long as the variables are not empty and they've been declared in the "template--capi__se.php" file where this particular file is included into.
        /* $data["custom_data"] = array(
            "currency"  => $facebook_api_purchase_currency,
            "value"     => $facebook_api_purchase_amount
        );*/
        $data["custom_data"]["content_type"] = $facebook_api_content_type;
    } elseif ($facebook_api_conversion_event_name == 'ViewContent') { // 'ViewContent' Event
        $data["event_source_url"] = $facebook_event_source_url;

        $data["custom_data"]["content_name"] = $facebook_api_page_to_track;
        $data["custom_data"]["content_type"] = $facebook_api_content_type;
    } elseif ($facebook_api_conversion_event_name == 'InitiateCheckout') { // 'InitiateCheckout' Event
        $data["event_source_url"] = ROOT_DIR.'checkout';
        $data["custom_data"]["num_items"] = $facebook_api_number_of_items;
    } elseif ($facebook_api_conversion_event_name == 'PageView') { // 'PageView' Event
        $data["event_source_url"] = $facebook_event_source_url;
    } elseif ($facebook_api_conversion_event_name == 'CompleteRegistration') { // 'CompleteRegistration' Event
        $data["event_source_url"] = $facebook_event_source_url;
        
        $data["custom_data"]["status"] = $facebook_api_registration_status;
    } elseif ($facebook_api_conversion_event_name == 'Search') { // 'Search' Event        
        $data["custom_data"]["search_string"] = $facebook_api_search_keyword;
        $data["custom_data"]["content_type"] = $facebook_api_content_type;
    } elseif ($facebook_api_conversion_event_name == 'AddToWishlist') { // 'AddToWishlist' Event        
        $data["custom_data"]["content_name"] = $facebook_api_page_to_track;
        $data["custom_data"]["content_type"] = $facebook_api_content_type;
    }

    $data_json = json_encode(array($data));

    $fields = array();
    $fields['access_token'] = $facebook_api_token_to_use;
    $fields['data'] = $data_json;

    /*if (get_real_ip_addr() == '185.187.243.100') {
        echo '<!-- '; var_dump($facebook_api_pixel_to_use, $data, $fields, $row_DisProg['course_code']); echo ' -->';
    }*/

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => "https://graph.facebook.com/v8.0/".$facebook_api_pixel_to_use."/events", 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>  http_build_query($fields),
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "Accept: application/json"  
        ),
    ));            

    $result = curl_exec($ch);
    curl_close ($ch);