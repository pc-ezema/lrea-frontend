<?php 
	// Try to prevent too many redirects. Do not remove this line of code.
	unset($_SESSION['redirect_counter']);

	if ($site_version != 'localhost') {/*
		// Sending a 302 Found header for temporary redirect
		header('HTTP/1.1 302 Found');
		
		// Include your 'coming-soon.html' file
		include_once('coming-soon.html');

		// Stop further execution
		exit();*/
	}
?>
<?php 
  	// Dynamic Titles and Keywords
	$is_allowed_to_track_fb_add_to_wishlist_event = true;

	// It is important for the facebook conversion api to use the same unique Event ID for both the browser and server events
	// so as avoid deduplication error. 
	$facebook_pixel_unique_event_id = $facebook_complete_registration_event_id = array();

	foreach ($conversion_api_tokens_and_pixels as $key => $value) {
		$facebook_pixel_unique_event_id[$key] = generate_facebook_event_id();

		// Prepare additional CAPI browser event and server details
		$fb_pageview_browser_script = $fb_pageview_server_script = "";

		include('inc.facebook-capi-general-parameters.php'); // The variable $capi_additional_object is initialized in this file.

		if ($enable_facebook_capi_events_tracking && $facebook_pixel_unique_event_id[$key]) { // Generate the browser and server events codes to output in the javascript.
			if (count($capi_additional_object)) {
				$fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
			}

			$fb_pageview_browser_script = "<script>
											fbq('init', '".$value['pixel']."');
											fbq('track', 'PageView'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_unique_event_id[$key]."'});
											</script>
											<noscript><img height=\"1\" width=\"1\" style=\"display:none\"
											src=\"https://www.facebook.com/tr?id=".$value['pixel']."&ev=PageView&noscript=1\"
											/></noscript>";     

			$fb_pageview_server_script = "<script>
											$(function() {
												$.ajax({
												url: '".$link_root_default."capi/se',
												method: 'POST',
												data: {
													en: 'PageView',
													eid: '".$facebook_pixel_unique_event_id[$key]."',
													eurl: '".$fb_evt_url."',
													eip: '".$capi_ip_addr."',
													eagent: '".$capi_user_agent."',
													".$capi_fbp."
													".$capi_email."
													".$capi_fbc."
												}
												});
											});
											</script>";
		}

		if (($sess_user_id > 0) && ($_SESSION['user_type'] == 'student') && $_SESSION['confirm_registration_event']) {
		// Update the database to record that the student's registration has been completed.
		mysqli_select_db($config, $database_config);
		$updateSQL = "UPDATE users_students
						SET reg_confirmed = '".sql_string($time_now)."'
						WHERE login_id = ".sql_int($sess_user_id)."
						LIMIT 1";
		$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
		$updateStatus = mysqli_affected_rows($config);
	
		if ($updateStatus > 0) { // If UPDATE is successful
			unset($_SESSION['confirm_registration_event']);

			$fb_complete_registration_browser_script = $fb_complete_registration_server_script = "";

			$facebook_complete_registration_event_id[$key] = generate_facebook_event_id();

			$capi_additional_object["status"] = true;

			if ($enable_facebook_capi_events_tracking && $facebook_complete_registration_event_id[$key]) { // Generate the browser and server events codes to output in the javascript.
				if (count($capi_additional_object)) {
					$fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
				}
		
				$fb_complete_registration_browser_script = "<script>
															fbq('init', '".$value['pixel']."');
															fbq('track', 'CompleteRegistration'".$fb_capi_init_string.", {eventID: '".$facebook_complete_registration_event_id[$key]."'});
															</script>";

				$fb_complete_registration_server_script = "<script>
															$(function() {
																$.ajax({
																url: '".$link_root_default."capi/se',
																method: 'POST',
																data: {
																	en: 'CompleteRegistration',
																	eid: '".$facebook_complete_registration_event_id[$key]."',
																	eurl: '".$fb_evt_url."',
																	eip: '".$capi_ip_addr."',
																	eagent: '".$capi_user_agent."',
																	".$capi_fbp."
																	".$capi_email."
																	".$capi_fbc."
																}
																});
															});
															</script>";
			}

			if ($enable_tiktok_events_tracking) { // TikTok Complete registration events.
				$tiktok_complete_registration_event = "";

				include('inc.tiktok-events-general-parameters.php');

				$tiktok_complete_registration_event = "<script>
														$(function() {
															$.ajax({
															url: '".$link_root_default."tiktok/se',
															method: 'POST',
															data: {
																en: 'CompleteRegistration',
																eip: '".$tiktok_ip_addr."',
																eagent: '".$tiktok_user_agent."',
																eurl: '".$tiktok_evt_url."',
																".$tiktok_clid."
																".$tiktok_email."
															}
															});
														});
														</script>";
			}
		}
		}
	}

	if ($dis_programme_id) { 
		$get_dis_prog_status = get_old_programme_info($dis_programme_id, 'status', false);

		if ($get_dis_prog_status == 'inactive') {
			$da_prog_is_active = false;
		}
	}
?>

<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="<?php echo ROOT_DIR; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ROOT_DIR; ?>OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css" rel="stylesheet" />
    <link href="<?php echo ROOT_DIR; ?>OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css" rel="stylesheet" />
    <link href="<?php echo ROOT_DIR; ?>OwlCarousel2-2.3.4/dist/assets/owl.theme.green.min.css" rel="stylesheet" />
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo ROOT_DIR; ?>css/style-<?php echo $fileCacheVersion ?>.css" rel="stylesheet">

	<link rel="shortcut icon" href="<?php echo ROOT_DIR.$fileCacheVersion.'-'; ?>images/favicon.png" />

    <link rel="canonical" href="<?php echo curPageURL(true); ?>">
    
    <?php if ($seo_meta_description) { ?>
      <meta name="description" content="<?php echo $seo_meta_description; ?>">
    <?php } elseif ($dis_programme_overview) { ?>
      <meta name="description" content="<?php echo make_meta_description($dis_programme_overview); ?>">
    <?php } elseif ($unique_meta_description) { ?>
      <meta name="description" content="<?php echo $unique_meta_description; ?>">
    <?php } ?>

    <link rel="canonical" href="<?php echo curPageURL(true); ?>">

    <title><?php if ($dynamic_title) { echo $dynamic_title; } elseif ($page_title) { echo $page_title." | ".SITE_NAME; } else { echo SITE_NAME; } ?></title>
    
    <?php if ($site_version == "online") { /* Place google analytics javascript code here */  ?>
		<?php foreach ($conversion_api_tokens_and_pixels as $this_fb_country => $this_fb_country_pixel_data) { // Generate browser event for each location ?>
			<script>
				!function(f,b,e,v,n,t,s)
				{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
				n.callMethod.apply(n,arguments):n.queue.push(arguments)};
				if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
				n.queue=[];t=b.createElement(e);t.async=!0;
				t.src=v;s=b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t,s)}(window, document,'script',
				'https://connect.facebook.net/en_US/fbevents.js');
			</script>
		<?php } ?>
      
		<?php // TikTok Pixel ?>
		<script>
			!function (w, d, t) {
			w.TiktokAnalyticsObject=t;
			var ttq=w[t]=w[t]||[];
			ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],
			ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
			for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
			ttq.instance=function(t){
			for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e
			},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
			ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
			n=document.createElement("script");n.type="text/javascript",n.async=!0,n.src=i+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];
			e.parentNode.insertBefore(n,e)};          
			ttq.load('<?php echo TIKTOK_PIXEL; ?>');
			ttq.page();}(window, document, 'ttq');
		</script>

		<?php // Domain specific contents that needs to be in the head element of the website.
			require_once('components/inc.specific-site-domain-contents-for-head-element.php');
		?>

		<?php $gtag_pixel_ID = ""; ?>
		<?php if ($gtag_pixel_ID) {/* New google tags after Daramola left. Added on 20th Jan, 2023 */ ?>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','<?php echo $gtag_pixel_ID; ?>');</script>
			<!-- End Google Tag Manager -->
		<?php } ?>
	<?php /* End google analytics javascript code here */ } ?>

    <?php if (($site_version == "online" || $test_tradedoubler_tracking) && $enable_tradedoubler_scripts) { // Start TradeDoubler ?>
		<?php $trade_doubler_id = 0; ?>
		<?php if ($trade_doubler_id) { ?>
			<script type="text/javascript" src="https://wrap.tradedoubler.com/wrap?id=<?php echo $trade_doubler_id; ?>"></script>
			
			<!-- Sales Script --> 
			<!-- Start Tradedoubler Landing Page Tag Insert on all landing pages to handle first party cookie-->
			<script language="JavaScript">
					(function(i,s,o,g,r,a,m){i['TDConversionObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script', 'https://svht.tradedoubler.com/tr_sdk.js', 'tdconv');
			</script>
			<!-- End Tradedoubler tag-->
		<?php } ?>
    <?php } // End TradeDoubler ?>

	<?php // The frontend UI/UX styling and the developer's custom style modification file. Styling modifications go into this file. ?>
    <link rel="stylesheet" href="<?php echo ASSETS_ROOT_DIR; ?>css/ui_ux_stylings-<?php echo $fileCacheVersion ?>.css">

	<?php // Facebook Pixel scripts
        $fb_event_code_display = 'above';
        include("inc.facebook-event-js-code-syntax.php"); 
    ?>