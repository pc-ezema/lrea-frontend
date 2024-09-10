<?php 
    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    if (($site_version == 'localhost') && ($_POST["action"] != "prepare_add_to_wishlist")) {
        echo '<pre>'; var_dump($_POST); echo '</pre>'; exit();
    }

    if ($_POST["en"] && $_POST['eid'] && $_POST['eip']) { // Prepare common variables.
        $facebook_api_client_ip = $_POST['eip'];
        $facebook_api_client_agent = $_POST['eagent'];
        $facebook_event_action_source = "website";
        $facebook_event_source_url = $_POST['eurl'];
        $facebook_api_user_email = $_POST['em'] ? $_POST['em'] : "";
        $fbp = $_POST['efbp'] ? $_POST['efbp'] : "";
        $fbc = $_POST['efbc'] ? $_POST['efbc'] : "";
        $facebook_api_conversion_event_name = $_POST['en'];
        $facebook_api_content_ids = $_POST['ecoid'] ? $_POST['ecoid'] : array();
    }

    // Record what we've got at the moment
    insert_latest_fb_info_into_db($fbp, $fbc, $facebook_api_user_email, $facebook_api_client_ip, $facebook_api_client_agent);

    if ($_POST['en'] == "PageView" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI PageView server event.
        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if ($_POST['en'] == "ViewContent" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI ViewContent server event.
        $facebook_api_page_to_track = $_POST['epage'];
        $facebook_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
        $facebook_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if ($_POST['en'] == "Lead" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI Lead server event.
        $use_purchase_event = false;
        $facebook_api_page_to_track = $_POST['epage'];
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request.php');    
        }

        exit();
    }

    if ($_POST['en'] == "Purchase" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI Purchase server event.
        $use_purchase_event = true;

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 
            
            $facebook_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
            $facebook_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";
            $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';
            $facebook_api_contents_purchased = $_POST['econtents'] ? $_POST['econtents'] : array();

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request.php');    
        }

        exit();
    }

    if ($_POST['en'] == "AddPaymentInfo" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI ViewContent server event.
        $facebook_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
        $facebook_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if ($_POST['en'] == "AddToCart" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI ViewContent server event.
        $facebook_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
        $facebook_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';
        $facebook_api_page_to_track = $_POST['epage'];

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if ($_POST['en'] == "InitiateCheckout" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI ViewContent server event.
        $facebook_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
        $facebook_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';
        $facebook_api_number_of_items = $_POST['enum'];     

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if ($_POST['en'] == "CompleteRegistration" && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI ViewContent server event.
        $facebook_api_registration_status = true;
        
        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if (($_POST['en'] == "Search") && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI Search server event.
        $facebook_api_search_keyword = $_POST['keyword'] ? $_POST['keyword'] : "";         
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';   

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    if (($_POST["action"] == "prepare_add_to_wishlist") && $enable_facebook_capi_events_tracking) { // Prepare AddToWishlist scripts which will be rendered on the page and gets executed with eval().
        $facebook_pixel_unique_event_id = array();

        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $facebook_pixel_unique_event_id[$key] = generate_facebook_event_id();

            include('inc.facebook-capi-general-parameters.php'); // This variable $capi_additional_object is initialized in this file.

            $capi_additional_object["value"] = $_POST["value"];
            $capi_additional_object["currency"] = $_POST["curr"];
            $capi_additional_object["content_name"] = $_POST["name"];
            $capi_additional_object["content_ids"] = array($_POST["coids"]);
            $capi_additional_object["content_type"] = 'product';

            if ($enable_facebook_capi_events_tracking && $facebook_pixel_unique_event_id[$key]) { // Generate the browser and server events codes to output.
                if (count($capi_additional_object)) {
                    $fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
                }        
  
                $fb_add_to_wishlist_browser_script = "<script>fbq('init', '".$value['pixel']."');
                                                    fbq('track', 'AddToWishlist'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_unique_event_id[$key]."'}); </script>";
  
                $fb_add_to_wishlist_server_script = "<script>
                                                        $.ajax({
                                                            url: '".$link_root_default."capi/se',
                                                            method: 'POST',
                                                            data: {
                                                                en: 'AddToWishlist',
                                                                eid: '".$facebook_pixel_unique_event_id[$key]."',
                                                                eip: '".$capi_ip_addr."',
                                                                eagent: '".$capi_user_agent."',
                                                                epage: '".addslashes($_POST["name"])."',
                                                                eval: ".$_POST["value"].",
                                                                ecur: '".$_POST["curr"]."',
                                                                ecoid: ".json_encode(array($_POST["coids"])).",
                                                                etype: 'product',
                                                                ".$capi_fbp."
                                                                ".$capi_email."
                                                                ".$capi_fbc."
                                                            }
                                                        });
                                                    </script>";

                $response = array(
                    "status" => 200,
                    "result" => $fb_add_to_wishlist_browser_script.PHP_EOL.$fb_add_to_wishlist_server_script.PHP_EOL
                );

                echo json_encode($response);
                exit();
            }
            exit();
        }
    }

    if (($_POST['en'] == "AddToWishlist") && $_POST['eid'] && $_POST['eip'] && $enable_facebook_capi_events_tracking) { // CAPI AddToWishlist server event.
        $facebook_api_page_to_track = $_POST['epage'];
        $facebook_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
        $facebook_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";      
        $facebook_api_content_type = $_POST['etype'] ? $_POST['etype'] : 'product';  

        // Make the API request
        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $data = array(); 

            $facebook_api_pixel_to_use = $value['pixel'];
            $facebook_api_token_to_use = $value['token'];

            $facebook_api_event_id_to_use = $_POST['eid'];

            include('inc.facebook-conversion-api-request-for-other-events.php');    
        }

        exit();
    }

    // If none of the expected parameters was passed
    redirect(ROOT_DIR); 
?>