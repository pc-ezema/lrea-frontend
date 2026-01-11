<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
    // This will only work for the last entries into those session parameters. Both parameters need to be supplied.

    $max_visible_first = 4;

    if (!$coresite_page_segment_2_slug) {
        include_once('404.php');
        exit();
    }

    // Select option ID of the subject
    mysqli_select_db($config, $database_config);
    $query_SelectedSubject = "SELECT option_id, option_value    
                            FROM options
                            WHERE option_name = 'programme subjects' 
                                AND status = 'active' 
                                AND url_name = '".sql_string($coresite_page_segment_2_slug)."'
                            LIMIT 1";
    $SelectedSubject = mysqli_query($config, $query_SelectedSubject) or die(mysqli_error($config));
    $row_SelectedSubject = mysqli_fetch_assoc($SelectedSubject);
    $totalRows_SelectedSubject = mysqli_num_rows($SelectedSubject);

    if (!$totalRows_SelectedSubject) {
        redirect(link_back(ROOT_DIR."category/advanced-professional-certificate-courses"));
    }

    if ($totalRows_SelectedSubject) {
        $prog_box_display_type = 'advanced professional';
        $prog_query_where_additional = "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_subject 
                                            JOIN options op_subject ON option_prog_subject.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_subject.option_one  = np.programme_id 
                                            WHERE option_prog_subject.relationship_name = 'programme subjects' 
                                                AND op_subject.option_id = ".sql_int($row_SelectedSubject['option_id'])."  
                                                AND np.deleted = '0'
                                        )";

        $prog_query_limit = "LIMIT 4000";
        include('inc.programme-query.php');

        if (!$totalRows_DisProg) {
            redirect(link_back(ROOT_DIR."category/advanced-professional-certificate-courses"));
        }
    }

    $dynamic_title = $row_SelectedSubject['option_value'].' - Advanced Professional Certificate Courses';
    $unique_meta_description = "Best Online Advanced Professional Courses".country_specific(' in {%%state_or_country%%}');
    
    $datalayer_asset_collection_source = "Category Page";
    
	require_once('components/doctype.inc.php');
?>

    <?php if ($site_version == "online") { // Start Criteo Tag ?>
        <?php // Generate the course codes for Criteo
            if ($totalRows_DisProg) {
                $avail_course_codes = array();

                do {
                    if ($row_DisProg['course_code']) { // Only courses with course_code
                        $avail_course_codes[] = '"'.$row_DisProg['course_code'].'"';
                    }
                } while($row_DisProg = mysqli_fetch_assoc($DisProg));  

                // Then reset the pointer to the beginning
                $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }
            }
        ?>

        <?php if (count($avail_course_codes)) { // If the course codes were found ?>
            <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
            <script type="text/javascript">
            window.criteo_q = window.criteo_q || [];
            var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
            window.criteo_q.push(
            { event: "setAccount", account: <?php echo $criteo_partner_id; ?>}, // You should never update this line
            { event: "setEmail", email: "" }, // Can be an empty string 
            { event: "setSiteType", type: deviceType},
            { event: "viewList", item: [<?php echo implode(", ", $avail_course_codes); ?>] });
            </script> 
        <?php } ?>
    <?php } // End Criteo Tag ?>

    <style>

    </style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
  </head>

  <body>
    <?php include_once('components/beginning-of-body.inc.php'); ?> 

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>
      
    <!-- Main Content -->
    <div class="main-content">
        <div class="jumbotron position-relative z-index-1 py-5 bg-main-6 add-bg-image-2 position-relative" style="background-image: url(<?php echo ROOT_DIR; ?>images/course-bg.jpg);">
            <div class="overlay-2"></div>
            <div class="container position-relative" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="card card-body border-0 rounded-8 p-lg-5">
                            <h1 class="text-bold mb-4">Explore <?php echo $row_SelectedSubject['option_value']; ?> - Advanced Professional Certificate Courses</h1>
                            <p class="">Upskill, reskill or pursue a passion with short courses across every subject, whether you’re a beginner or already an expert.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $cache_session_id = 'apcc_subject_page_courses'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
            <div class="courses-topics-list py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                                <h4 class="text-bold mb-lg-0 mb-2"><?php echo ucwords($row_SelectedSubject['option_value']); ?> Courses</h4>
                                <p class="mb-0">
                                    <?php 
                                        $label_to_use = ($totalRows_DisProg > 1) ? 'Programmes' : 'Programme'; 
                                        echo $totalRows_DisProg.' '.$label_to_use;
                                    ?>
                                </p>
                            </div>

                            <div class="courses-container-lister">
                                <div class="row">
                                    <?php $i = 0; ?>
                                    <?php do { ?>
                                        <?php if ($i++ == $max_visible_first) break; ?>
                                        <div class="col-xl-3 mb-4 position-relative">
                                            <?php include('inc.programme-box.php') ?>
                                        </div>                                        
                                    <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>
                            </div>

                            <div class="collapse" id="collapseMore">
                                <div class="courses-container-lister">
                                    <div class="row">
                                        <?php do { ?>
                                            <div class="col-xl-3 mb-4 position-relative">
                                                <?php include('inc.programme-box.php') ?>
                                            </div>
                                        <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($totalRows_DisProg > $max_visible_first) { ?>
                                <div class="text-center mt-4">
                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore" aria-expanded="false" aria-controls="collapseMore" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                        <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                        <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>

        <?php include_once('inc.testimonial-segment-new.php'); ?>
    </div>
    <!-- Main content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
  </body>
</html>