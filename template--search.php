<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');

    // Prepare the condition for each type
    $max_visible_first = 4;
    $prog_query_limit = "LIMIT 4000";
    $prog_query_where_additional = "";

    if ($_GET['k']) {
        $user_keywords = $_GET['k'];
        $search_terms = explode(" ", $user_keywords);
        $modifier_sql = array();
        $relevance_sql = array();

        if (count($search_terms)) {
            foreach ($search_terms as $term) {
                // Check if term is not only symbols and is at least 3 characters long
                if ($term && strlen($term) >= 3 && !preg_match('/^[\p{P}\p{S}]+$/u', $term) && !in_array(strtolower($term), array('and', 'or', 'with', 'of', 'in', 'as', 'on', 'not', 'is', 'it', 'at', 'professional', 'certificate', 'degree', 'special', 'executive', 'programme', 'diploma', "master's", 'master', 'mba', 'phd', 'bsc', 'postgraduate', 'post', 'graduate', 'course', 'study', 'studies', 'training', 'class', 'classes', 'module', 'modules', 'lesson', 'lessons', 'education', 'educational', 'university', 'universities', 'college', 'school', 'academy', 'institute', 'faculty', 'department'))) {
                    $modifier_sql[] = " np.programme_name LIKE '%".sql_string($term)."%' ";
                    $relevance_sql[] = " (CASE WHEN np.programme_name LIKE '%".sql_string($term)."%' THEN 1 ELSE 0 END) ";
                }
            }
        }

        if (count($modifier_sql)) {
            $prog_query_where_additional .= "AND (".implode(" OR ", $modifier_sql).") ";
        }

        if ($keywords_to_check = count($relevance_sql)) {
            $prog_query_relevance_additional .= ", (".implode(" + ", $relevance_sql).") AS keyword_score ";

            if ($keywords_to_check > 1) {
                $prog_query_relevance_where_additional .= "AND (".implode(" + ", $relevance_sql).") > 1 ";
            }
        }
    }

    if ($_GET['p_cat']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_subject 
                                            JOIN options op_subject ON option_prog_subject.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_subject.option_one  = np.programme_id 
                                            WHERE option_prog_subject.relationship_name = 'programme subjects' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_cat'])."'  
                                                AND np.deleted = '0'
                                        )";
    }

    /*if ($_GET['p_type']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_type_2 
                                            JOIN options op_subject ON option_prog_type_2.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_type_2.option_one  = np.programme_id 
                                            WHERE option_prog_type_2.relationship_name = 'programme type' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_type'])."'  
                                                AND np.deleted = '0'
                                        )";
    }*/

    if ($_GET['p_study']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_study 
                                            JOIN options op_subject ON option_prog_study.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_study.option_one  = np.programme_id 
                                            WHERE option_prog_study.relationship_name = 'programme format' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_study'])."'  
                                                AND np.deleted = '0'
                                        )";
    }

    if ($_GET['p_diff']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_difficulty 
                                            JOIN options op_subject ON option_prog_difficulty.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_difficulty.option_one  = np.programme_id 
                                            WHERE option_prog_difficulty.relationship_name = 'programme difficulty' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_diff'])."'  
                                                AND np.deleted = '0'
                                        )";
    }

    if ($_GET['p_creds']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_credits 
                                            JOIN options op_subject ON option_prog_credits.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_credits.option_one  = np.programme_id 
                                            WHERE option_prog_credits.relationship_name = 'programme credits' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_creds'])."'  
                                                AND np.deleted = '0'
                                        )";
    }

    if ($_GET['p_dur']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_duration 
                                            JOIN options op_subject ON option_prog_duration.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_duration.option_one  = np.programme_id 
                                            WHERE option_prog_duration.relationship_name = 'programme duration' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_dur'])."'  
                                                AND np.deleted = '0'
                                        )";
    }

    if ($_GET['p_effort']) {
        $prog_query_where_additional .= "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_effort 
                                            JOIN options op_subject ON option_prog_effort.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_effort.option_one  = np.programme_id 
                                            WHERE option_prog_effort.relationship_name = 'programme effort' 
                                                AND op_subject.option_id = '".sql_int($_GET['p_effort'])."'  
                                                AND np.deleted = '0'
                                        )";
    }

    $facebook_search_content_items = array();
    $is_allowed_to_track_fb_search_event = true;
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = 'Search Courses ';
	require_once('components/doctype.inc.php');
?>

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
        <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-lg-0 mb-3">
                        <div class="">
                            <h1 class="text-bold text-white ft-custom-hero mb-2">Search for Courses</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($prog_query_where_additional) { ?>
            <?php // Get the matching short course programmes
                $prog_query_preserve_params_for_next_run = true;
                $prog_box_display_type = 'short course';

                if ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type']) { // Don't bother to execute the query if the chosen category is not the same except if there's no category chosen at all
                    include('inc.programme-query.php');
                }
            ?>
            <?php if ($totalRows_DisProg && ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type'])) { $display_count = 1; ?>
                <?php $found = true; ?>
                <div class="courses-topics-list py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                                    <h3 class="text-bold">Professional Certificate Courses</h3>
                                    <p class="mb-0">
                                        <?php 
                                            $label_to_use = ($totalRows_DisProg > 1) ? 'Programmes' : 'Programme'; 
                                            echo $totalRows_DisProg.' '.$label_to_use;
                                        ?>
                                    </p>
                                </div>

                                <div class="courses-container-lister">
                                    <div class="row">
                                        <?php do { ?>
                                            <div class="col-xl-4 col-lg-6 col-sm-12 mb-4 position-relative">
                                                <?php include('inc.programme-box.php') ?>
                                            </div>
                                        <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php // Get the matching advanced professional certificate course programmes
                $prog_query_preserve_params_for_next_run = true;
                $prog_box_display_type = 'advanced professional';

                if ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type']) { // Don't bother to execute the query if the chosen category is not the same except if there's no category chosen at all
                    include('inc.programme-query.php');
                }
            ?> 
            <?php if ($totalRows_DisProg && ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type'])) { $display_count = 1; ?>
                <?php $found = true; ?>
                <div class="courses-topics-list py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                                    <h3 class="text-bold">Advanced Professional Certificate Courses</h3>
                                    <p class="mb-0">
                                        <?php 
                                            $label_to_use = ($totalRows_DisProg > 1) ? 'Programmes' : 'Programme'; 
                                            echo $totalRows_DisProg.' '.$label_to_use;
                                        ?>
                                    </p>
                                </div>

                                <div class="courses-container-lister">
                                    <div class="row">
                                        <?php do { ?>
                                            <div class="col-xl-4 col-lg-6 col-sm-12 mb-4 position-relative">
                                                <?php include('inc.programme-box.php') ?>
                                            </div>
                                        <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- <?php // Get the matching semp online programmes
                $prog_query_preserve_params_for_next_run = true;
                $prog_box_display_type = 'masters';

                if ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type']) { // Don't bother to execute the query if the chosen category is not the same except if there's no category chosen at all
                    include('inc.programme-query.php');
                }
            ?>
            <?php if ($totalRows_DisProg && ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type'])) { $display_count = 1; ?>
                <?php $found = true; ?>
                <div class="courses-topics-list py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                                    <h3 class="text-bold">Master's Degree Courses</h3>
                                    <p class="mb-0">
                                        <?php 
                                            $label_to_use = ($totalRows_DisProg > 1) ? 'Programmes' : 'Programme'; 
                                            echo $totalRows_DisProg.' '.$label_to_use;
                                        ?>
                                    </p>
                                </div>

                                <div class="courses-container-lister">
                                    <div class="row">
                                        <?php do { ?>
                                            <div class="col-xl-4 col-lg-6 col-sm-12 mb-4 position-relative">
                                                <?php include('inc.programme-box.php') ?>
                                            </div>
                                        <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?> -->

            <?php // Get the matching international postgraduate diploma programmes
                $prog_query_preserve_params_for_next_run = true;
                $prog_box_display_type = 'intl diploma';

                if ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type']) { // Don't bother to execute the query if the chosen category is not the same except if there's no category chosen at all
                    include('inc.programme-query.php');
                }
            ?> 
            <?php if ($totalRows_DisProg && ($_GET['p_type'] == $prog_box_display_type || !$_GET['p_type'])) { $display_count = 1; ?>
                <?php $found = true; ?>
                <div class="courses-topics-list py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                                    <h3 class="text-bold">International Postgraduate diplomas</h3>
                                    <p class="mb-0">
                                        <?php 
                                            $label_to_use = ($totalRows_DisProg > 1) ? 'Programmes' : 'Programme'; 
                                            echo $totalRows_DisProg.' '.$label_to_use;
                                        ?>
                                    </p>
                                </div>

                                <div class="courses-container-lister">
                                    <div class="row">
                                        <?php do { ?>
                                            <div class="col-xl-4 col-lg-6 col-sm-12 mb-4 position-relative">
                                                <?php include('inc.programme-box.php') ?>
                                            </div>
                                        <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (!isset($found) || !$found) { ?>
                <div style="padding: 80px 0;text-align:center">
                    <h3>No result found.</h3>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div style="padding: 80px 0;text-align:center">
                <h3>Type the keyword of what you are looking for in the field above.</h3>
            </div>
        <?php } ?>

        <?php // Generate Facebook browser and server scripts for search event.
            if (count($facebook_search_content_ids) && $enable_facebook_capi_events_tracking) {
                $facebook_pixel_unique_event_id = array();

                foreach ($conversion_api_tokens_and_pixels as $key => $value) {
                    $facebook_pixel_unique_event_id[$key] = generate_facebook_event_id();
                            
                    $fb_search_event_browser_script = $fb_search_event_server_script = "";
            
                    include('inc.facebook-capi-general-parameters.php'); // This variable $capi_additional_object is initialized in this file.
            
                    // Prepare Search event specific parameters for this page.
                    $capi_additional_object["content_ids"] = $facebook_search_content_ids;
                    $capi_additional_object["search_string"] = $user_keywords;
                    $capi_additional_object["content_type"] = 'product';

                    $fb_capi_content_ids = json_encode($facebook_search_content_ids); // This array gets populated with course codes in the programme-box file.
            
                    if ($enable_facebook_capi_events_tracking && $facebook_pixel_unique_event_id[$key]) { // Generate the browser and server events to output in the javascript
                        if (count($capi_additional_object)) {
                            $fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
                        }
                    
                        $fb_search_event_browser_script = "<script>
                                                            fbq('init', '".$value['pixel']."');
                                                            fbq('track', 'Search'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_unique_event_id[$key]."'});
                                                        </script>";     
                
                        $fb_search_event_server_script = "<script>
                                                            $(function() {
                                                                $.ajax({
                                                                    url: '".$link_root_default."capi/se',
                                                                    method: 'POST',
                                                                    data: {
                                                                        en: 'Search',
                                                                        eid: '".$facebook_pixel_unique_event_id[$key]."',
                                                                        eip: '".$capi_ip_addr."',
                                                                        eagent: '".$capi_user_agent."',
                                                                        ecoid: ".$fb_capi_content_ids.",
                                                                        keyword: '".$user_keywords."',
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
            }
        ?>
    </div>
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
</body>
</html>