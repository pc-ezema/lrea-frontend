<?php 
    require_once('connections/config.php');
    require_once('components/misc.inc.php');

    if ($_POST["en"] && $_POST['eip']) { // Prepare common variables.
        $tiktok_api_client_ip = $_POST['eip'];
        $tiktok_api_client_agent = $_POST['eagent'];
        $tiktok_event_source_url = $_POST['eurl'];
        $tiktok_api_user_email = $_POST['em'] ? $_POST['em'] : "";
        $tiktok_api_event_name = $_POST['en'];
        $tiktok_api_contents = $_POST['cont'] ? $_POST['cont'] : array();
        $tiktok_click_id = $_POST['ttclid'] ? $_POST['ttclid'] : "";
    }

    $send_api_request = false;
    $payload = "";

    // Record what we've got at the moment
    insert_latest_fb_info_into_db('', '', $tiktok_api_user_email, $tiktok_api_client_ip, $tiktok_api_client_agent);

    if (in_array($_POST['en'], array("ViewContent", "AddToCart", "InitiateCheckout", "AddPaymentInfo", "CompleteRegistration", "SubmitForm", "CompletePayment")) 
        && $_POST['eip'] && $enable_tiktok_events_tracking
    ) { // TikTok Events.
        $tiktok_api_purchase_currency = $_POST['ecur'] ? $_POST['ecur'] : "";
        $tiktok_api_purchase_amount = $_POST['eval'] ? $_POST['eval'] : "";

        $tiktok_payload = array();
        $tiktok_payload['pixel_code'] = TIKTOK_PIXEL;
        $tiktok_payload['event'] = $tiktok_api_event_name;

        if ($tiktok_click_id) {
            $tiktok_payload['context']['ad'] = array('callback' => $tiktok_click_id);         
        }

        $tiktok_payload['context']['page'] = array('url' => $tiktok_event_source_url);        

        if ($tiktok_api_user_email) {
            $tiktok_payload['context']['user'] = array('email' => $tiktok_api_user_email);
        }

        $tiktok_payload['context']['user_agent'] = $tiktok_api_client_agent;
        $tiktok_payload['context']['ip'] = $tiktok_api_client_ip;

        if ($tiktok_api_purchase_amount && $tiktok_api_purchase_currency) {
            $tiktok_payload['properties'] = array(
                "contents" => $tiktok_api_contents,
                "currency" => $tiktok_api_purchase_currency,
                "value" => $tiktok_api_purchase_amount
            );
        }
        
        $payload = json_encode(iso88591_to_utf8($tiktok_payload));

        $send_api_request = true;
    }

    if ($payload && ($site_version == 'localhost')) {
        echo '<pre>'; var_dump($payload); echo '</pre>'; exit();
    }

    if ($send_api_request && $payload && $site_version == "online") { // Send the Tiktok API request.

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://business-api.tiktok.com/open_api/v1.2/pixel/track/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Access-Token: '.TIKTOK_ACCESS_TOKEN,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        exit();
    }

    // If none of the expected parameters was passed
    redirect(ROOT_DIR); 
?>