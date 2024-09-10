<?php
    // Handle webhook setup confirmation from Facebook.
    if (isset($_GET['hub_mode']) && isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token'])) {
    	if ($_GET['hub_verify_token'] == 'leadsgenface-1234') {
    		echo $_GET['hub_challenge'];
    	}
    }
    else { // Handle leads that are sent directly from Facebook.        
        require_once('connections/config.php');
        require_once('components/misc.inc.php');

        $branch = "Dubai";
        $utm_source = "Facebook";
        $source = "Online - Facebook";
        $recipients = INFO_SUPPORT_EMAIL;

        $advertised_programme_forms = array(
            "922111105056950" => "master-of-business-administration-mba-online",
            "1601892283476184" => "ma-human-resource-management-hrm-online",
            "375027704288209" => "msc-in-logistics-and-supply-chain-management",
            "3047282975515506" => "msc-in-project-management-degree",
            "220941979931177" => "msc-accounting-and-finance",
        );

        // Capture the data from the webhook.
        $data_from_facebook = file_get_contents('php://input');
        $leads_data = json_decode($data_from_facebook, true);
        
        $changes = $leads_data["entry"][0]["changes"];

        if (count($changes) && $site_version == "online") {
            include('inc.facebook-webhook-processing-script.php');
        }
    }

    http_response_code(200);
?>