<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');

    if ($coresite_page_segment_2_slug) {
        // Check for the subject
        mysqli_select_db($config, $database_config);
        $query_DisOption = "SELECT op.*
                        FROM options op 
                        WHERE op.url_name = '".sql_string($coresite_page_segment_2_slug)."' 
                        LIMIT 1";
        $DisOption = mysqli_query($config, $query_DisOption) or die(mysqli_error($config));
        $row_DisOption = mysqli_fetch_assoc($DisOption);
        $totalRows_DisOption = mysqli_num_rows($DisOption);

        $subject_name = $row_DisOption['option_value'];

        if ($coresite_page_segment_3_slug && $totalRows_DisOption) { // If sub-subject is present in the URL.
            // Check for the sub-subject
            mysqli_select_db($config, $database_config);
            $query_DisOption = "SELECT op.*
                            FROM options op 
                            WHERE op.url_name = '".sql_string($coresite_page_segment_3_slug)."' 
                            LIMIT 1";
            $DisOption = mysqli_query($config, $query_DisOption) or die(mysqli_error($config));
            $row_DisOption = mysqli_fetch_assoc($DisOption);
            $totalRows_DisOption = mysqli_num_rows($DisOption);

            if ($totalRows_DisOption) {
                $sub_subject_mode_is_enabled = true;
            }
        }
    
        if (!$totalRows_DisOption) {
            include_once('404.php');
            exit();
        }
    }
    else {
        include_once('404.php');
        exit();
    }

    // Prepare the condition for targeting specific subjects
    $max_visible_first = 8;
    $prog_query_limit = "LIMIT 4000";

    if ($sub_subject_mode_is_enabled) { // Show courses for the sub-subject
        $prog_query_where_additional = "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_subject 
                                            JOIN options op_subject ON option_prog_subject.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_subject.option_one  = np.programme_id 
                                            WHERE option_prog_subject.relationship_name = 'programme sub subjects' 
                                                AND op_subject.url_name = '".sql_string($row_DisOption['url_name'])."'  
                                                AND np.deleted = '0'
                                        )";
    }
    else { // Show courses for main subject.
        $prog_query_where_additional = "AND np.programme_id IN (
                                            SELECT np.programme_id  
                                            FROM option_relationships option_prog_subject 
                                            JOIN options op_subject ON option_prog_subject.option_two = op_subject.option_id 
                                            JOIN new_programmes np ON option_prog_subject.option_one  = np.programme_id 
                                            WHERE option_prog_subject.relationship_name = 'programme subjects' 
                                                AND op_subject.url_name = '".sql_string($row_DisOption['url_name'])."'  
                                                AND np.deleted = '0'
                                        )";

        if (subjectHasSubMenu($coresite_page_segment_2_slug)) {                                
            // Get all sub-subjects that have at least a programme
            mysqli_select_db($config, $database_config);
            $query_SubSubjects = "SELECT op.*
                                FROM options op
                                WHERE op.option_name = 'programme sub subjects' 
                                    AND op.parent_option_id = ".sql_int($row_DisOption['option_id'])." 
                                    AND op.status = 'active' 
                                    AND op.deleted = '0' 
                                    AND EXISTS (
                                        SELECT oprel.option_two 
                                        FROM option_relationships oprel 
                                        WHERE oprel.relationship_name = 'programme sub subjects' 
                                            AND oprel.option_two = op.option_id 
                                    )";
            $SubSubjects = mysqli_query($config, $query_SubSubjects) or die(mysqli_error($config));
            $row_SubSubjects = mysqli_fetch_assoc($SubSubjects);
            $totalRows_SubSubjects = mysqli_num_rows($SubSubjects);
        }
    }

    if ($coresite_page_segment_2_slug == "business-management") { 
        $unique_meta_description = "";
    } 
    elseif ($coresite_page_segment_2_slug == "health-and-psychology") {
        $unique_meta_description = "";
    } 
    elseif ($coresite_page_segment_2_slug == "business-law") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "information-technology") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "personal-development") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "teaching-and-education") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "health-and-safety") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "finance-and-accounting") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "office-productivity") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "sales-and-marketing") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "leadership-and-entrepreneurship") {
        $unique_meta_description = "";
    }
    elseif ($coresite_page_segment_2_slug == "human-resources") {
        $unique_meta_description = "";
    }

    $prog_query_limit = "LIMIT 4000";

    $page_title = $row_DisOption['option_value'].' Courses ';
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

    <div class="main-content">
        <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative mt-custom-2" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="text-start mb-5">
                            <h1 class="text-bold text-white ft-custom-hero mb-4">Explore <?php echo $row_DisOption['option_value']; ?> Courses.</h1>
                        </div>

                        <?php if ($totalRows_SubSubjects || $sub_subject_mode_is_enabled) { ?>
                            <?php if ($totalRows_SubSubjects && !$sub_subject_mode_is_enabled) { // Main subject page ?>
                                <h6 class="text-medium text-green mb-3">Choose <?php if (preg_match('/^[aeiou]/i', $row_DisOption['option_value'])) echo 'an'; else echo 'a'; ?> <?php echo $row_DisOption['option_value']; ?> topic:</h6>
                                <ul class="list-unstyled d-flex flex-wrap" style="gap: 10px">
                                    <?php do { ?>
                                        <li>
                                            <a href="<?php echo $link_root_path.'subject/'.$coresite_page_segment_2_slug.'/'.$row_SubSubjects['url_name']; ?>" class="btn btn-custom-size-2 btn-light btn-custom"><?php echo $row_SubSubjects['option_value']; ?></a>
                                        </li>
                                    <?php } while ($row_SubSubjects = mysqli_fetch_assoc($SubSubjects)); ?>
                                </ul>
                            <?php } elseif ($sub_subject_mode_is_enabled) { // Content on the page is for sub-subject ?>
                                <h6><a href="<?php echo $link_root_path.'subject/'.$coresite_page_segment_2_slug; ?>" class="text-white">&larr; Back to <?php echo $subject_name; ?></a></h6>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="courses-tabs py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mb-lg-0 order-lg-0 order-1">
                        <div class="tabs-programme mb-5">
                            <ul class="nav nav-pills bg-white mb-3" id="pills-tab" role="tablist" style="gap: 15px;">
                                <?php
                                    $short_courses_results = $advanced_professional_courses_results = $masters_courses_results = $all_courses_results = [];

                                    $cache_session_id = 'subject_' . $coresite_page_segment_2_slug;
                                    if ($coresite_page_segment_3_slug) $cache_session_id .= '_' . $coresite_page_segment_3_slug;

                                    $target_key = md5($cache_session_id);
                                    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);
                                ?>
                                <?php
                                    if ($cached_value !== NULL) {
                                        $short_courses_results = $cached_value['short-courses'];
                                    } else {
                                        $prog_query_preserve_params_for_next_run = true;
                                        $prog_box_display_type = 'short course';
                                        include('inc.programme-query.php');

                                        if ($totalRows_DisProg) {
                                            do {
                                                $short_courses_results[] = $row_DisProg;
                                            } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                            $all_courses_results['short-courses'] = $short_courses_results;
                                        }
                                    }
                                ?>
                                <?php if (count($short_courses_results)) { ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-pcc-tab" data-bs-toggle="pill" data-bs-target="#pills-pcc" type="button" role="tab" aria-controls="pills-pcc" aria-selected="true">Professional Certificate Course</button>
                                    </li>
                                <?php } ?>

                                <?php
                                    if ($cached_value !== NULL) {
                                        $advanced_professional_courses_results = $cached_value['advanced-professional'];
                                    } else {
                                        $prog_query_preserve_params_for_next_run = true;
                                        $prog_box_display_type = 'advanced professional';
                                        include('inc.programme-query.php');

                                        if ($totalRows_DisProg) {
                                            do {
                                                $advanced_professional_courses_results[] = $row_DisProg;
                                            } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                            $all_courses_results['advanced-professional'] = $advanced_professional_courses_results;
                                        }
                                    }
                                ?>
                                <?php if (count($advanced_professional_courses_results)) { ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-apcc-tab" data-bs-toggle="pill" data-bs-target="#pills-apcc" type="button" role="tab" aria-controls="pills-apcc" aria-selected="false">Advanced Professional Certificate Course</button>
                                    </li>
                                <?php } ?>

                                <!-- <?php 
                                    if ($cached_value !== NULL) {
                                        $masters_courses_results = $cached_value['masters'];
                                    } else {
                                        $prog_query_preserve_params_for_next_run = true;
                                        $prog_box_display_type = 'masters';
                                        include('inc.programme-query.php');

                                        if ($totalRows_DisProg) {
                                            do {
                                                $masters_courses_results[] = $row_DisProg;
                                            } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                            $all_courses_results['masters'] = $masters_courses_results;
                                        }
                                    }
                                ?>
                                <?php if (count($masters_courses_results)) { ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-masters-tab" data-bs-toggle="pill" data-bs-target="#pills-masters" type="button" role="tab" aria-controls="pills-masters" aria-selected="false">Degree Programmes</button>
                                    </li>
                                <?php } ?> -->

                                <?php
                                    if ($cached_value !== NULL) {
                                        $ipgd_courses_results = $cached_value['intl diploma'];
                                    } else {
                                        $prog_query_preserve_params_for_next_run = true;
                                        $prog_box_display_type = 'intl diploma';
                                        include('inc.programme-query.php');

                                        if ($totalRows_DisProg) {
                                            do {
                                                $ipgd_courses_results[] = $row_DisProg;
                                            } while ($row_DisProg = mysqli_fetch_assoc($DisProg));

                                            $all_courses_results['intl diploma'] = $ipgd_courses_results;
                                        }
                                    }
                                ?>
                                <?php if (count($ipgd_courses_results)) { ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-ipgd-tab" data-bs-toggle="pill" data-bs-target="#pills-ipgd" type="button" role="tab" aria-controls="pills-ipgd" aria-selected="false">International Postgraduate Diploma</button>
                                    </li>
                                <?php } ?>
                            </ul>

                            <?php
                                if ($cached_value === NULL) {
                                    add_entry_cached_in_memory($cache_session_id, $target_key, $all_courses_results);
                                }
                            ?>

                            <?php $active_tab_exists = false; ?>
                            <?php $cache_session_id = 'topics_tabs_section_v1'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
                                <div class="tab-content mb-4" id="pills-tabContent">
                                    <?php if (count($short_courses_results)) { $active_tab_exists = true; ?>
                                        <div class="tab-pane fade show active" id="pills-pcc" role="tabpanel" aria-labelledby="pills-pcc-tab" tabindex="0">
                                            <div class="py-4">
                                                <h3 class="text-bold">Professional Certificate Course</h3>
                                                <p>These are brief, online courses aimed at enhancing your professional development. Completing a course not only furthers your career aspirations but also grants you a professional certificate, making you eligible for continuous professional development (CPD).</p>
                                            </div>
                                            <div class="courses-container-lister">
                                                <div class="row">
                                                    <?php for ($i = 0; $i < count($short_courses_results); $i++) { ?>
                                                        <?php if ($i == $max_visible_first) break; ?>
                                                        <?php $row_DisProg = $short_courses_results[$i]; ?>
                                                        <div class="col-lg-3 mb-4 position-relative">
                                                            <?php include('inc.programme-box.php') ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseMore">
                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = $max_visible_first; $i < count($short_courses_results); $i++) { ?>
                                                            <?php $row_DisProg = $short_courses_results[$i]; ?>
                                                            <div class="col-lg-3 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (count($short_courses_results) > $max_visible_first) { ?>
                                                <div class="text-center mt-4">
                                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore" aria-expanded="false" aria-controls="collapseMore" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                        <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                        <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <?php if (count($advanced_professional_courses_results)) { ?>     
                                        <div class="tab-pane fade <?php if(!$active_tab_exists) { echo 'show active'; $active_tab_exists = true; } ?>" id="pills-apcc" role="tabpanel" aria-labelledby="pills-apcc-tab" tabindex="0">
                                            <div class="py-4">
                                                <h3 class="text-bold">Advanced Professional Certificate Course</h3>
                                                <p>These are advanced online certificate courses based in the UK, tailored to foster your professional growth. As you reach new career milestones, you'll also earn a professional certificate that qualifies you for relevant continuous professional development (CPD).</p>
                                            </div>
                                            <div class="courses-container-lister">
                                                <div class="row">
                                                    <?php for ($i = 0; $i < count($advanced_professional_courses_results); $i++) { ?>
                                                        <?php if ($i == $max_visible_first) break; ?>
                                                        <?php $row_DisProg = $advanced_professional_courses_results[$i]; ?>
                                                        <div class="col-lg-3 mb-4 position-relative">
                                                            <?php include('inc.programme-box.php') ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseMore2">
                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = $max_visible_first; $i < count($advanced_professional_courses_results); $i++) { ?>
                                                            <?php $row_DisProg = $advanced_professional_courses_results[$i]; ?>
                                                            <div class="col-lg-3 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (count($advanced_professional_courses_results) > $max_visible_first) { ?>
                                                <div class="text-center mt-4">
                                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore2" aria-expanded="false" aria-controls="collapseMore2" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                        <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                        <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <!-- <?php if (count($masters_courses_results)) { ?>
                                        <div class="tab-pane fade <?php if(!$active_tab_exists) { echo 'show active'; $active_tab_exists = true; } ?>" id="pills-masters" role="tabpanel" aria-labelledby="pills-masters-tab" tabindex="0">
                                            <div class="py-4">
                                                <h3 class="text-bold">Degree Programmes</h3>
                                                <p>Advance your expertise with our specialized Master's programs. Gain in-depth knowledge and practical skills for career growth and leadership opportunities.</p>
                                            </div>
                                            <div class="courses-container-lister">
                                                <div class="row">
                                                    <?php for ($i = 0; $i < count($masters_courses_results); $i++) { ?>
                                                        <?php if ($i == $max_visible_first) break; ?>
                                                        <?php $row_DisProg = $masters_courses_results[$i]; ?>
                                                        <div class="col-lg-3 mb-4 position-relative">
                                                            <?php include('inc.programme-box.php') ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseMore3">
                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = $max_visible_first; $i < count($masters_courses_results); $i++) { ?>
                                                            <?php $row_DisProg = $masters_courses_results[$i]; ?>
                                                            <div class="col-lg-3 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (count($masters_courses_results) > $max_visible_first) { ?>
                                                <div class="text-center mt-4">
                                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore3" aria-expanded="false" aria-controls="collapseMore3" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                        <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                        <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?> -->

                                    <?php if (count($ipgd_courses_results)) { ?>
                                        <div class="tab-pane fade <?php if(!$active_tab_exists) { echo 'show active'; $active_tab_exists = true; } ?>" id="pills-ipgd" role="tabpanel" aria-labelledby="pills-ipgd-tab" tabindex="0">
                                            <div class="py-4">
                                                <h3 class="text-bold">International Postgraduate Diploma</h3>
                                                <p>These programmes are 120 credit and available in various subjects.</p>
                                            </div>
                                            <div class="courses-container-lister">
                                                <div class="row">
                                                    <?php for ($i = 0; $i < count($ipgd_courses_results); $i++) { ?>
                                                        <?php if ($i == $max_visible_first) break; ?>
                                                        <?php $row_DisProg = $ipgd_courses_results[$i]; ?>
                                                        <div class="col-lg-3 mb-4 position-relative">
                                                            <?php include('inc.programme-box.php') ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseMore3">
                                                <div class="courses-container-lister">
                                                    <div class="row">
                                                        <?php for ($i = $max_visible_first; $i < count($ipgd_courses_results); $i++) { ?>
                                                            <?php $row_DisProg = $ipgd_courses_results[$i]; ?>
                                                            <div class="col-lg-3 mb-4 position-relative">
                                                                <?php include('inc.programme-box.php') ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (count($ipgd_courses_results) > $max_visible_first) { ?>
                                                <div class="text-center mt-4">
                                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore3" aria-expanded="false" aria-controls="collapseMore3" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                        <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                        <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once('inc.testimonial-segment-new.php'); ?>
    </div>
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
</body>
</html>