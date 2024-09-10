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
    $is_allowed_to_track_tiktok_view_content_event = true;

    if ($enable_add_to_cart_btn) {
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

    $clients_can_enrol_for_this_course = $site_version == 'localhost' ? 1 : does_programme_have_modules($dis_programme_id);
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	if ($row_DisProg['main_seo_title']) {
	    $page_title = $row_DisProg['main_seo_title'];
    }
    else {
        $page_title = $row_DisProg['programme_name'];
    }
    
    // Start Facebook eCommerce Tag 
    if ($row_DisProg['course_code'] && $enable_facebook_capi_events_tracking) {             
        require('inc.facebook-capi-view-content-script.php');
    }

    // TikTok ViewContent event
    if ($row_DisProg['course_code'] && $enable_tiktok_events_tracking) {             
        require('inc.tiktok-view-content-script.php');
    }

    $page_title = 'Professional Certificate Courses';
    $unique_meta_description = "";

    $banner_to_use = $dis_programme_banner_image ? $dis_programme_banner_image : ROOT_DIR.'images/banner-cover-1.jpg';
            
	require_once('components/doctype.inc.php');
?>
    <style>

    </style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
</head>

<style>
    .add-img-cover-2 {
        background-position: top center;
        background-size: cover;
        background-repeat: no-repeat;
    }
</style>

<body>
    <?php include_once('components/beginning-of-body.inc.php'); ?> 
    
    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>

    <?php // Product detail datalayer code for this programme.
        include('inc.individual-prog-page-datalayer-script.php');
    ?>

    <div class="main-content">
        <div class="container">
            <div class="hero-sections rounded-0 py-5 mt-custom position-relative text-white z-index-1 bg-main-3 add-bg-image-2" style="background-image: url(<?php echo $banner_to_use; ?>);">

                <div class="overlay-hero rounded-0"></div>

                <div class="container-fluid position-relative z-index-1">

                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h1 class="text-white text-bold ft-custom-hero"><?php echo $row_DisProg['programme_name']; ?></h1>

                            <?php if ($programme_covered_by_subscription) { ?>
                                <div class="col-lg-12 mb-2">
                                    <a href="<?php echo $course_ecomm_link_action_url; ?>" class="btn btn-custom-size text-bold bg-green btn-custom text-white"><?php echo $course_ecomm_link_action_text; ?></a>
                                </div>
                            <?php } else { ?>
                                <?php if ($da_prog_is_active && $clients_can_enrol_for_this_course) { ?>
                                    <div class="d-flex position-relative w-100 cart-info" style="gap: 10px;" data-id="<?php echo $dis_programme_id; ?>">                                
                                        <a href="<?php echo ROOT_DIR; ?>cart" class="btn btn-custom-size text-bold bg-green w-100 text-white btn-custom one-click-enrol save-to-cart">Enrol now - <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?></a>

                                        <?php if ($showSaveButton) { ?>
                                            <span class="show-cart-button w-100 w-sm-100">
                                                <a href="javascript:void(0)" class="btn btn-custom-size bg-white text-dark btn-custom w-100 text-bold w-sm-100 save-to-cart">Add to cart</a>
                                            </span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-5"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once('inc.course-details-tabs-segment.php'); ?>

        <div class="make-bg-white">
            <?php include_once('inc.testimonial-segment-new.php'); ?>
        </div>

        <?php // Get the matching professional certificate course programmes
            $prog_box_display_type = 'short course';
            $prog_query_where_additional = " AND np.programme_id <> '".sql_int($dis_programme_id)."' ";
            $prog_query_limit = "LIMIT 8";
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
                                <a href="<?php echo ROOT_DIR.$location_slug; ?>category/short-courses" class="btn btn-custom-size bg-white border btn-custom">View all courses <i data-feather="chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
</body>
</html>