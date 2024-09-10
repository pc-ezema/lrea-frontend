<?php // GENERATE FACEBOOK BROWSER AND SERVER SCRIPTS FOR VIEW CONTENT EVENT.
    $facebook_pixel_unique_event_id = array();

    foreach ($conversion_api_tokens_and_pixels as $key => $value) {
        $facebook_pixel_unique_event_id[$key] = generate_facebook_event_id();
                
        $fb_view_content_browser_script = $fb_view_content_server_script = "";

        include('inc.facebook-capi-general-parameters.php'); // This variable $capi_additional_object is initialized in this file.

        // Prepare ViewContent specific parameters for this page.
        $capi_additional_object["value"] = round($row_DisProg['discounted_fee'], 2);
        $capi_additional_object["currency"] = 'GBP';
        $capi_additional_object["content_type"] = 'product';
        $capi_additional_object["content_ids"] = array($row_DisProg['course_code']);

        $fb_capi_track_page = addslashes(trim($row_DisProg['programme_name']));
        $fb_capi_content_ids = json_encode(array($row_DisProg['course_code'])); // This must be sent as an array. (Reference: https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking/#custom-properties)
    
        if ($enable_facebook_capi_events_tracking && $facebook_pixel_unique_event_id[$key]) { // Generate the browser and server events to output in the javascript
            if (count($capi_additional_object)) {
                $fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
            }
      
            $fb_view_content_browser_script = "<script>
                                                fbq('init', '".$value['pixel']."');
                                                fbq('track', 'ViewContent'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_unique_event_id[$key]."'});
                                            </script>";     
    
            $fb_view_content_server_script = "<script>
                                                $(function() {
                                                    $.ajax({
                                                        url: '".$link_root_default."capi/se',
                                                        method: 'POST',
                                                        data: {
                                                            en: 'ViewContent',
                                                            eid: '".$facebook_pixel_unique_event_id[$key]."',
                                                            eurl: '".$fb_evt_url."',
                                                            eip: '".$capi_ip_addr."',
                                                            eagent: '".$capi_user_agent."',
                                                            epage: '".$fb_capi_track_page."',
                                                            eval: ".round($row_DisProg['discounted_fee'], 2).",
                                                            ecur: 'GBP',
                                                            ecoid: ".$fb_capi_content_ids.",
                                                            etype: 'product',
                                                            ".$capi_fbp."
                                                            ".$capi_email."
                                                            ".$capi_fbc."
                                                        }
                                                    });
                                                });
                                            </script>";
        }
    }
?>