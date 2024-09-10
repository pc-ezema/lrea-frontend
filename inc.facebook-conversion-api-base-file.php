<?php
    $facebook_api_client_ip = get_real_ip_addr();
    $facebook_api_client_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36"; // Set fall back as Chrome.
    $facebook_event_action_source = "website";
    $facebook_event_source_url = curPageURL();
    
    $data = array();

    // Make the API request for each of the pixels
    foreach ($conversion_api_tokens_and_pixels as $key => $value) {         
        $facebook_api_pixel_to_use = $value['pixel'];
        $facebook_api_token_to_use = $value['token'];

        if (array_key_exists($key, $facebook_pixel_purchase_lead_unique_event_id)) { // Use the same unique Event ID as the browser event
            $facebook_api_event_id_to_use = $facebook_pixel_purchase_lead_unique_event_id[$key];
        } else { // Fallback
            $facebook_api_event_id_to_use = generate_facebook_event_id();
        }
        
        include('inc.facebook-conversion-api-request.php');
    }
?>