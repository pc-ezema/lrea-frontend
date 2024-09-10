<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');

    // The query has been executed in "dynamic-programme.php" 
    // which this file gets included into in order to display this page template

    require('inc.programme-variables.php');

    // Facebook parameters to use in the "send_facebook_add_to_cart_event()" function inside "javascripts.inc.php".
    // We must set alternatively named variables here because the original variables e.g $dis_programme_fee etc, 
    // get replaced when "inc.programme-query.php" gets called to show suggested courses, and thus would be inaccurate.
    include('inc.facebook-add-to-cart-variables.php');

    $is_allowed_to_track_fb_view_content_event = true;
    $is_allowed_to_track_fb_lead_event = true;
    $is_allowed_to_track_tiktok_view_content_event = true;
    $is_allowed_to_track_tiktok_submit_form_event = true;

    require('inc.programme-form-parameters.php');

    $same_page_landing_form = true;
    require_once(ROOT_PATH_TO_PORTAL.'landing-page-callback.php'); 

    // Replacement values
    if ($is_Girne_American_University) {
        $xyz_degree_type = "MBA Degree";
        $xyz_diploma_type = "Strategic Management and Leadership";
        // $xyz_cert_1 = "Level-7-IPGD-sample-certificate.jpg";
        $xyz_cert_1 = "PGD-certificate-blank.jpg";
        $xyz_cert_2 = "GAU-MBA-Cert.jpg";
        $xyz_seo_shorten = "MBA";
        $seo_meta_description = "Study for an online MBA Degree".country_specific(' in {%%state_or_country%%}').". Our MBA Degree is recognized and accepted worldwide. Find out if this MBA is right for your career.";
    }
    elseif ($is_european_masters_university) {
        $xyz_degree_type = "Master's Degree";
        $xyz_diploma_type = "Relevant Specialization";
        // $xyz_cert_1 = "Level-7-IPGD-sample-certificate.jpg";
        $xyz_cert_1 = "PGD-certificate-blank.jpg";
        $xyz_cert_2 = "european-university-sample-certificate.png";
        $xyz_seo_shorten = "Master's";
    }
    elseif (preg_match('/human/i', $row_DisProg['programme_name'])) {
        $xyz_degree_type = "MA Degree";
        $xyz_diploma_type = "Human Resource Management";
        $xyz_cert_1 = "PGD HRM Sample.jpg";
        $xyz_cert_2 = "MA HRM sample Certificate.jpg";
        $xyz_seo_shorten = "MA Degree";
        $seo_meta_description = "Our MA Human Resource Management".country_specific(' in {%%state_or_country%%}')." prepares you for a global career in HRM and equips you to progress your career in learning and development.";
    }
    elseif (preg_match('/finance/i', $row_DisProg['programme_name'])) {
        $xyz_degree_type = "MSc Degree";
        $xyz_diploma_type = "Accounting and Finance";
        $xyz_cert_1 = "PDG Accounting Sample.jpg";
        $xyz_cert_2 = "MSC Accounting Sample Certificate.jpg";
        $xyz_seo_shorten = "MSc";
    }
    elseif (preg_match('/administration/i', $row_DisProg['programme_name'])) {
        $xyz_degree_type = "MBA Degree";
        $xyz_diploma_type = "Strategic Management and Leadership";
        // $xyz_cert_1 = "Level-7-IPGD-sample-certificate.jpg";
        $xyz_cert_1 = "PGD-certificate-blank.jpg";
        $xyz_cert_2 = "MBA Sample Certificate.jpg";
        $xyz_seo_shorten = "MBA";
        $seo_meta_description = "Study for an online MBA Degree".country_specific(' in {%%state_or_country%%}').". Our MBA Degree is recognized and accepted worldwide. Find out if this MBA is right for your career.";
    }
    elseif (preg_match('/management/i', $row_DisProg['programme_name'])) {
        $xyz_degree_type = "MBA Degree";
        $xyz_diploma_type = "Strategic Management and Leadership";
        $xyz_cert_1 = "Level-7-IPGD-sample-certificate.jpg";
        $xyz_cert_2 = "MBA Sample Certificate.jpg";
        $xyz_seo_shorten = "MBA";
        
    }
    else {
        $xyz_degree_type = "Master's Degree";
        $xyz_diploma_type = "Relevant Specialization";
        // $xyz_cert_1 = "Level-7-IPGD-sample-certificate.jpg";
        $xyz_cert_1 = "PGD-certificate-blank.jpg";
        $xyz_cert_2 = "MBA Sample Certificate.jpg";
        $xyz_seo_shorten = "Master's";
    }

    if ($is_IU_university) {
        $xyz_cert_2 = "IU-certificate-copy.png";
    }

    // Prepare meta descriptions for some selected courses listed by Daramola on 14 of October, 2021.
    if (preg_match('/health and social care/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Our Master's degree in Health Management will open up opportunities for career development at the highest levels.";
    }
    elseif (preg_match('/accounting/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Our MSc Accounting & Finance".country_specific(' in {%%state_or_country%%}')." prepares you for a global career in Finance and equips you to progress your career in learning and development.";
    }
    elseif (preg_match('/logistics and supply/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Our MSc in Logistics and Supply Chain Management".country_specific(' in {%%state_or_country%%}')." prepares you for a global career in Finance and equips you to progress your career in learning and development.";
    }
    elseif (preg_match('/education management/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Our Master's degree in Education Management and Leadership will open up opportunities for career development at the highest levels.";
    }
    elseif (preg_match('/strategic marketing/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Our MSc Strategic Marketing will develop your strategic marketing skills and compete in today's global marketplace.";
    }
    elseif (preg_match('/public administration/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Gain the skills you need to move into coveted positions with government agencies with our Master’s Degree in Public Administration, MA.";
    }
    elseif (preg_match('/tourism and hospitality/i', $row_DisProg['programme_name']) || preg_match('/risk management/i', $row_DisProg['programme_name'])) {
        $seo_meta_description = "Polish your planning, organizing and management skills for the challenges of the hospitality industry by learning practical approaches to effectively navigate resources for the best service delivery.";
    }

    $cert_single_template = "";

    if ($is_Girne_American_University) {
        $cert_single_template = ROOT_DIR."images/banners/".$xyz_cert_2;
    }
    elseif ($is_european_masters_university) {
        $cert_single_template = ROOT_DIR."images/banners/".$xyz_cert_2;
    }
    elseif ($is_IU_university) {
        $cert_single_template = ROOT_DIR."images/banners/".$xyz_cert_2;
    }
    elseif (!$row_DisProg['dual_cert_issued']) {
        $cert_single_template = MEDIA_ROOT_DIR."images/banners/degree-sample-certificate-new.png";
    }

    $thisPublisherIDInfo = $_COOKIE['a_pub_id'];

    if ($enable_add_to_cart_btn && $thisPublisherIDInfo) { // show save to cart if publisher_id cookie is set
        // Save to cart functionality
        $showSaveButton = true;

        if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // user is logged in
            $showSaveButton = show_add_to_cart_save_button($dis_programme_id);
        } 
        else {
            $thisCartCookieInfo = $_COOKIE['s2cart'];

            if ($thisCartCookieInfo) {
                $retrieved_Cart_Info = explode(",", $thisCartCookieInfo);

                if (in_array($dis_programme_id, $retrieved_Cart_Info)) {
                    $showSaveButton = false;
                }
            }
        }
    }

    $revert_to_old_enrol_button = false;

    $this_page_unique_location = country_specific(' in {%%state_or_country%%}');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.

    /*$seo_meta_description = "Enroll for our ".$row_DisProg['programme_name'].country_specific(' in {%%state_or_country%%}').". Internationally accredited ".$xyz_seo_shorten." courses. Easy payment structure. Scholarship provided. Inquire now!";
	
	$page_title = $row_DisProg['programme_name'].country_specific(' {%%state_or_country%%}').' | Online '.$xyz_seo_shorten.' Course';*/
    
    if ($row_DisProg['main_seo_title']) {
	    $page_title = $row_DisProg['main_seo_title'].country_specific(' in {%%state_or_country%%}').' '.year_for_seo();
    }
    else {
        $page_title = $row_DisProg['programme_name'];
    }
    
    // Start Facebook eCommerce Tag 
    if ($row_DisProg['course_code'] && $enable_facebook_capi_events_tracking) {             
        require('inc.facebook-capi-view-content-script.php');
    }
    
    // Generate CAPI browser and server scripts for Lead event.
    if ($_SESSION['dis_new_lead_email']) { // This session is set in the "landing-page-callback.php" file in portal.
        $hashed_lead_email = hash('sha256', $_SESSION['dis_new_lead_email']);

        unset($_SESSION['dis_new_lead_email']);

        $fb_lead_browser_script = $fb_lead_server_script = $capi_con_id = "";
        
        $fb_capi_track_content = addslashes($dis_programme_name);

        $facebook_pixel_lead_unique_event_id = array();

        foreach ($conversion_api_tokens_and_pixels as $key => $value) {
            $facebook_pixel_lead_unique_event_id[$key] = generate_facebook_event_id();

            include('inc.facebook-capi-general-parameters.php'); // The variable $capi_additional_object is initialized in this file.

            $capi_additional_object["content_type"] = 'product';

            if ($row_DisProg['course_code']) {
                $capi_additional_object["content_ids"] = array($row_DisProg['course_code']); // Prepared for browser event.

                $capi_con_id = "ecoid: ".json_encode(array($row_DisProg['course_code'])).","; // Prepared for server event. (AJAX)
            }
            
            $capi_additional_object["content_name"] = $fb_capi_track_content;

            $capi_additional_object["em"] = $hashed_lead_email; // Prepared for browser event.

            $capi_email = "em: '".$hashed_lead_email."',"; // Prepared for server event. (AJAX)

            if ($enable_facebook_capi_events_tracking && $facebook_pixel_lead_unique_event_id[$key]) { // Generate the browser and server events codes to output in the javascript.
                if (count($capi_additional_object)) {
                    $fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
                }
          
                $fb_lead_browser_script = "<script>
                                                fbq('init', '".$value['pixel']."');
                                                fbq('track', 'Lead'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_lead_unique_event_id[$key]."'});
                                            </script>";     
            
                $fb_lead_server_script = "<script>
                                            $(function() {
                                                $.ajax({
                                                    url: '".$link_root_default."capi/se',
                                                    method: 'POST',
                                                    data: {
                                                        en: 'Lead',
                                                        eid: '".$facebook_pixel_lead_unique_event_id[$key]."',
                                                        eurl: '".$fb_evt_url."',
                                                        eip: '".$capi_ip_addr."',
                                                        eagent: '".$capi_user_agent."',
                                                        epage: '".$fb_capi_track_content."',
                                                        etype: 'product',
                                                        ".$capi_con_id."
                                                        ".$capi_fbp."
                                                        ".$capi_email."
                                                        ".$capi_fbc."
                                                    }
                                                });
                                            });
                                        </script>";
            }
        }

        if ($enable_tiktok_events_tracking) { // TikTok SubmitForm Event
            $tiktok_submit_form_event = "";

            include('inc.tiktok-events-general-parameters.php');

            $tiktok_email = "em: '".$hashed_lead_email."',";

            $tiktok_submit_form_event = "<script>
                                            $(function() {
                                                $.ajax({
                                                    url: '".$link_root_default."tiktok/se',
                                                    method: 'POST',
                                                    data: {
                                                        en: 'SubmitForm',
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

    // TikTok ViewContent event
    if ($row_DisProg['course_code'] && $enable_tiktok_events_tracking) {             
        require('inc.tiktok-view-content-script.php');
    }

    $displayNewlyDesignedForm = true;

    if ($dis_programme_banner_image) {
        $masters_banner_image = $dis_programme_banner_image;
    }
    else {
        $masters_banner_image = MEDIA_ROOT_DIR.'images/banner-cover-3.jpg';
    }

	require_once('components/doctype.inc.php');
?>

    <?php if ($site_version == "online") { // Start Criteo Tag ?>
        <?php if ($row_DisProg['course_code']) { // If it has a course code ?>
            <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
            <script type="text/javascript">
            window.criteo_q = window.criteo_q || [];
            var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
            window.criteo_q.push(
            { event: "setAccount", account: <?php echo $criteo_partner_id; ?>}, // You should never update this line
            { event: "setEmail", email: "" }, // Can be an empty string 
            { event: "setSiteType", type: deviceType},
            { event: "viewItem", item: "<?php echo $row_DisProg['course_code']; ?>" });
            </script> 
        <?php } ?>
    <?php } // End Criteo Tag ?>

<style>
    .add-img-cover-2 {
        background-position: top center;
        background-size: cover;
        background-repeat: no-repeat;
    }
</style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
  </head>

  <body>
    <?php include_once('components/beginning-of-body.inc.php'); ?> 

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>
      
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="hero-sections rounded-0 py-5 mt-custom position-relative text-white z-index-1 bg-main-3 add-bg-image-2" style="background-image: url(<?php echo ROOT_DIR; ?>images/banner-cover-2.jpg);">

                <div class="overlay-hero rounded-0"></div>

                <div class="container-fluid position-relative z-index-1">

                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h1 class="text-white text-bold ft-custom-hero"><?php echo $row_DisProg['programme_name']; ?></h1>
                        </div>
                        <div class="col-lg-5"></div>
                    </div>

                </div>
            </div>

            <div class="box-details mt-5 text-dark rounded-8">
                <div class="row">

                    <div class="col-lg-3 mb-lg-0 mb-4">
                        <div class="media-view d-flex">
                            <div class="media-view-img flex-shrink-0 me-2">
                                <div class="box-icon">
                                    <i data-feather="clock"></i>
                                </div>
                            </div>
                            <div class="media-view-body">
                                <p class="mb-1">Duration</p>
                                <h4 class="text-bold mb-0"><?php echo get_programme_relationship($dis_programme_id, 'programme duration'); ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-4">
                        <div class="media-view d-flex">
                            <div class="media-view-img flex-shrink-0 me-2">
                                <div class="box-icon">
                                    <i data-feather="award"></i>
                                </div>
                            </div>
                            <div class="media-view-body">
                                <p class="mb-1">Pedagogy</p>
                                <h4 class="text-bold mb-0"><?php echo get_programme_relationship($dis_programme_id, 'programme format'); ?></h4>
                            </div>
                        </div>
                    </div>

                    <?php // Instalment fee
                        if ($dis_country_programme_max_instalments) { // Use the max number of instalments value that has been set for the country if there's one.
                            $instalmental_fee_percentage_increase = get_increment_percentage($dis_country_programme_max_instalments);
                            $estimated_monthly_fee = round(($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))/$dis_country_programme_max_instalments, 2);
                        }
                        elseif ($dis_programme_max_instalments > 1) { // Using the max number of instalments value
                            $instalmental_fee_percentage_increase = get_increment_percentage($dis_programme_max_instalments);
                            $estimated_monthly_fee = round(($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))/$dis_programme_max_instalments, 2);
                        }
                        else {
                            $estimated_monthly_fee = highest_instalment_amount($dis_programme_instalment_duration, $dis_programme_fee); 
                        }
                    ?>
                    <div class="col-lg-3 mb-lg-0 mb-4">
                        <div class="media-view d-flex">
                            <div class="media-view-img flex-shrink-0 me-2">
                                <div class="box-icon">
                                    <i data-feather="bar-chart"></i>
                                </div>
                            </div>
                            <div class="media-view-body">
                                <p class="mb-1">Instalment</p>
                                <h4 class="text-bold mb-0"><?php if ($dis_programme_fee) echo $symbol_for_programme_currency.' '.show_amount($estimated_monthly_fee); ?> <?php if ($da_prog_has_instalment) echo '/month'; ?>
                                    interest-free*</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-4">
                        <div class="media-view d-flex">
                            <div class="media-view-img flex-shrink-0 me-2">
                                <div class="box-icon">
                                    <i data-feather="activity"></i>
                                </div>
                            </div>
                            <div class="media-view-body">
                                <p class="mb-1">Total tuition</p>
                                <h4 class="text-bold mb-0">
                                    <?php if ($cosmetics_dis_programme_fee && $cosmetics_dis_programme_original_fee) { ?>
                                        <?php echo $symbol_for_programme_currency.' '.show_amount($cosmetics_dis_programme_fee); ?>
                                    <?php } else { ?>
                                        <?php if ($dis_programme_fee) echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?>
                                    <?php } ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="hero-sections bg-dark-2 py-5 my-5 z-index-1" id="request-info">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-lg-12">
                        <div class="card card-body bg-main p-lg-5 mb-lg-5 mb-0 sticky-top border-0 rounded-8">
                            <h3 class="text-bold text-upper-custom text-green">Request follow-up</h3>
                            <p class="">Fill in the form below and one of our student advisers will contact you soon.</p>
                            <?php require_once('inc.programme-form.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $course_details_type = 'masters';
            include_once('inc.course-details-tabs-segment.php'); 
        ?>

        <?php if (count($dis_single_course_modules)) { $i = 0; ?>
            <div class="info-section bg-white py-5">
                <div class="container">
                    <h3 class="text-bold text-black mb-4">What you'll learn</h3>
                    <div class="row">
                        <?php foreach ($dis_single_course_modules as $row_DisModules) { ?>
                            <div class="col-lg-3 mb-3">
                                <div class="card custom-card-border rounded-8 h-100">
                                    <div class="card-body">
                                        <p class="text-green">Module <?php echo ++$i; ?></p>
                                        <h4 class="text-bold"><?php echo ucwords($row_DisModules['module_name']); ?></h4>

                                        <p class="ft-sm">
                                            <?php if($row_DisModules['module_description']) {
                                                echo $row_DisModules['module_description'];
                                            } else { ?>
                                                This module covers key elements that are essential for understanding the course material.
                                            <?php } ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                        $request_follow_up_button_outline_dark = true;
                        require('inc.enrol-request-buttons-new-2.php');
                    ?>                        
                </div>
            </div>
        <?php } ?>

        <?php include('inc.testimonial-segment-new.php'); ?>

        <div class="">
            <div class="container py-5">
                <?php
                    $request_follow_up_button_outline_dark = true;
                    require('inc.enrol-request-buttons-new-2.php'); 
                ?>
            </div>
        </div>

        <?php // Get the matching advanced professional certificate course programmes
            $prog_box_display_type = 'masters';
            $prog_query_where_additional = " AND np.programme_id <> '".sql_int($dis_programme_id)."' ";
            $prog_query_limit = "LIMIT 4";
            include('inc.programme-query.php');
        ?>

        <?php if ($totalRows_DisProg) { ?>
            <div class="courses-sect arrow-circle py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex align-items-center flex-wrap mb-4">
                                <h2 class="text-bold mb-lg-0 mb-2 text-upper-custom ft-custom-hero-2">Other related courses</h2>
                            </div>
                            <div class="owl-carousel position-relative courses-carousel owl-theme">
                                <?php do { ?>
                                    <div class="item position-relative h-100">
                                        <?php include('inc.programme-box.php'); ?>
                                    </div>
                                <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                            </div>

                            <div class="text-center mt-4">
                                <a href="<?php echo ROOT_DIR.$location_slug; ?>category/masters-degrees" class="btn btn-custom-size bg-white border btn-custom">View all courses <i data-feather="chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (count($data_layer_programme_items) && $enable_data_layer_scripts) { // Prepare the dataLayer impression event for Recommended Master's courses. ?>
            <?php include('inc.data-layer-script.php'); ?>
        <?php } ?>
    </div>
    <!-- /Main Content -->
        
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>

    <?php if ($site_version == "online") { // Google Conversion Action Script ?>
        <script>
            <?php $google_conversion_action_id = ''; ?>
            var x = 0;
            var myVar = setInterval(function(){
                if(x == 0){
                    if(jQuery('h4:contains("Your request has been received")').is(":visible"))
                    {
                        gtag('event', 'conversion', {'send_to': '<?php echo $google_conversion_action_id; ?>'});
                        clearInterval(myVar);
                        x = 1;
                    }
                }
            }, 1000);
        </script>
    <?php } ?>
    
    <?php include_once('components/end-of-body.inc.php'); ?> 
  </body>
</html>