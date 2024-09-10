<?php
    // Shared parameters amongst each event type
    $data["event_name"] = $facebook_api_conversion_event_name;
    $data["event_time"] = time();
    $data["event_id"] = $facebook_api_event_id_to_use; // Event id must be the same for both browser event and the server conversion API.
    $data["action_source"] = $facebook_event_action_source;
    $data["event_source_url"] = $facebook_event_source_url;
    $data["user_data"] = array(
        "client_ip_address"  => $facebook_api_client_ip,
        "client_user_agent"  => $facebook_api_client_agent,
        "em" => $facebook_api_user_email
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
    elseif ($_COOKIE['_fbc']) { // This value does not always exist. It only exists when a user clicks on our advert from Facebook before they get taken to our website.
        $data["user_data"]["fbc"] = $_COOKIE['_fbc'];
    }

    if (is_array($facebook_api_content_ids) && count($facebook_api_content_ids)) {
        $data["custom_data"]["content_ids"] = $facebook_api_content_ids; // This value must be an array.
    }

    if (is_array($facebook_api_contents_purchased) && count($facebook_api_content_ids)) {
        $data["custom_data"]["contents"] = $facebook_api_contents_purchased; // This value must be an array of Json objects. (Reference: https://developers.facebook.com/docs/facebook-pixel/reference#object-properties);
    }

    if ($facebook_api_content_type) {
        $data["custom_data"]["content_type"] = $facebook_api_content_type;
    }

    // Unique parameters amongst each event type
    if ($use_purchase_event) { // track purchases
        $data["custom_data"]["currency"] = $facebook_api_purchase_currency;
        $data["custom_data"]["value"] = $facebook_api_purchase_amount;
    } 
    else { // track leads        
        $data["custom_data"]["content_name"] = $facebook_api_page_to_track;
    }

    $data_json = json_encode(array($data));

    $fields = array();
    $fields['access_token'] = $facebook_api_token_to_use;
    $fields['data'] = $data_json;

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
?>