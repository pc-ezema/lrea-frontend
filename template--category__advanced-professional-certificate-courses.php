<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
    // This will only work for the last entries into those session parameters. Both parameters need to be supplied.

    $max_visible_first = 8;

    $prog_box_display_type = 'advanced professional';
    $prog_query_limit = "LIMIT 4000";
    include('inc.programme-query.php');
    
    $available_course_subjects = array();

    if ($totalRows_DisProg) {
        $cacheBurstingDeadline = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $cache_session_id = 'apcc-page-subjects';
        $target_key = md5(curPageURL());
        $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);

        if ($cached_value !== NULL) {
            $available_course_subjects = $cached_value;
        }
        else {
            // Get all the subjects that exists
            mysqli_select_db($config, $database_config);
            $query_AllSubjects = "SELECT * 
                                FROM options
                                WHERE option_name = 'programme subjects' 
                                    AND status = 'active' 
                                ORDER BY option_value ASC";
            $AllSubjects = mysqli_query($config, $query_AllSubjects) or die(mysqli_error($config));
            $row_AllSubjects = mysqli_fetch_assoc($AllSubjects);
            $totalRows_AllSubjects = mysqli_num_rows($AllSubjects);

            if ($totalRows_AllSubjects) {
                // Check if the subject has at least a course
                // Loop through the subjects
                do {
                    if (!in_array($row_AllSubjects['option_id'], $available_course_subjects)) {
                        // Loop through the programmes to see if one exists as programme of this subject. Break out of the loop if one is found.
                        do {
                            // Check if a programme belongs to this subject.
                            mysqli_select_db($config, $database_config);
                            $query_SubjectProgramme = "SELECT op.*, oprel.option_two 
                                                        FROM option_relationships oprel 
                                                        JOIN options op ON oprel.option_two = op.option_id 
                                                        WHERE oprel.relationship_name = 'programme subjects' 
                                                            AND oprel.option_one = ".sql_int($row_DisProg['programme_id'])." 
                                                            AND oprel.option_two = ".sql_int($row_AllSubjects['option_id'])." 
                                                        ORDER BY oprel.relationship_id ASC";
                            $SubjectProgramme = mysqli_query($config, $query_SubjectProgramme) or die(mysqli_error($config));
                            $row_SubjectProgramme = mysqli_fetch_assoc($SubjectProgramme);
                            $totalRows_SubjectProgramme = mysqli_num_rows($SubjectProgramme);

                            if ($totalRows_SubjectProgramme) { // This subject has a programme.
                                $available_course_subjects[$row_AllSubjects['option_id']] = array(
                                    'name' => ucwords($row_AllSubjects['option_value']),
                                    'url' => $row_AllSubjects['url_name']
                                );

                                break;
                            }
                        } while($row_DisProg = mysqli_fetch_assoc($DisProg));
                        // Then reset the pointer to the beginning.
                        $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }
                    }
                } while ($row_AllSubjects = mysqli_fetch_assoc($AllSubjects));
            }

            add_entry_cached_in_memory($cache_session_id, $target_key, $available_course_subjects, $cacheBurstingDeadline);
        }
    }
    
    $page_title = 'Advanced Certificate Courses';
    $unique_meta_description = "";
            
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
                            <h1 class="text-bold text-white ft-custom-hero mb-4">Explore Advanced Professional Certificate Courses</h1>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="text-medium text-green mb-3">Courses by subjects:</h6>
                                <div class="form-floating">
                                    <select class="form-select bg-light subject_switch" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" id="floatingSelect" aria-label="Floating label select example">
                                        <option value="<?php echo $link_root_path; ?>category/advanced-professional-certificate-courses" selected>--Choose--</option>
                                        <option value="<?php echo $link_root_path; ?>subject/real-estate-management" <?php if ($coresite_page_segment_2_slug == 'real-estate-management') echo 'selected'; ?>>Real Estate Management</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/business-management" <?php if ($coresite_page_segment_2_slug == 'business-management') echo 'selected'; ?>>Business Management</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/health-and-psychology" <?php if ($coresite_page_segment_2_slug == 'health-and-psychology') echo 'selected'; ?>>Health and Psychology</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/business-law" <?php if ($coresite_page_segment_2_slug == 'business-law') echo 'selected'; ?>>Business Law</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/information-technology" <?php if ($coresite_page_segment_2_slug == 'information-technology') echo 'selected'; ?>>Information Technology</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/personal-development" <?php if ($coresite_page_segment_2_slug == 'personal-development') echo 'selected'; ?>>Personal Development</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/teaching-and-education" <?php if ($coresite_page_segment_2_slug == 'teaching-and-education') echo 'selected'; ?>>Teaching and Education</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/health-and-safety" <?php if ($coresite_page_segment_2_slug == 'health-and-safety') echo 'selected'; ?>>Health and Safety</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/finance-and-accounting" <?php if ($coresite_page_segment_2_slug == 'finance-and-accounting') echo 'selected'; ?>>Finance and Accounting</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/office-productivity" <?php if ($coresite_page_segment_2_slug == 'office-productivity') echo 'selected'; ?>>Office Productivity</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/sales-and-marketing" <?php if ($coresite_page_segment_2_slug == 'sales-and-marketing') echo 'selected'; ?>>Sales and Marketing</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/leadership-and-entrepreneurship" <?php if ($coresite_page_segment_2_slug == 'leadership-and-entrepreneurship') echo 'selected'; ?>>Leadership and Entrepreneurship</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/human-resources" <?php if ($coresite_page_segment_2_slug == 'human-resources') echo 'selected'; ?>>Human Resources</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/hotel-travel-and-hospitality-management" <?php if ($coresite_page_segment_2_slug == 'hotel-travel-and-hospitality-management') echo 'selected'; ?>>Hotel, Travel and Hospitality Management</a></li>
                                        <option value="<?php echo $link_root_path; ?>subject/logistics-and-supply-chain-management" <?php if ($coresite_page_segment_2_slug == 'logistics-and-supply-chain-management') echo 'selected'; ?>>Logistics and Supply Chain Management</a></li>
                                    </select>
                                    <label for="floatingSelect">Choose courses by subjects</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $cache_session_id = 'apcc_category_page_subject_courses'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
            <?php if (count($available_course_subjects)) { ?>
                <?php foreach ($available_course_subjects as $subject_key => $subject_value) { ?>
                    <?php 
                        $prog_box_display_type = 'advanced professional';
                        $prog_query_limit = "LIMIT 12";
                        $prog_query_where_additional = "AND np.programme_id IN (
                            SELECT np.programme_id  
                            FROM option_relationships option_prog_subject 
                            JOIN options op_subject ON option_prog_subject.option_two = op_subject.option_id 
                            JOIN new_programmes np ON option_prog_subject.option_one  = np.programme_id 
                            WHERE option_prog_subject.relationship_name = 'programme subjects' 
                                AND op_subject.option_id = ".sql_int($subject_key)."  
                                AND np.deleted = '0'
                        )";
                        include('inc.programme-query.php');
                    ?>

                    <?php if ($totalRows_DisProg) { $i = 0; ?>
                        <div class="courses-topics-list py-5">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-4">
                                            <h2 class="text-bold text-upper-custom mb-3"><?php echo ucwords($subject_value['name']); ?></h2>
                                        </div>

                                        <div class="courses-container-lister">
                                            <div class="row">
                                                <?php do { ?>
                                                    <?php if ($i++ == $max_visible_first) break; ?>
                                                    <div class="col-lg-3 mb-4 position-relative">
                                                        <?php include('inc.programme-box.php') ?>
                                                    </div>                                        
                                                <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                            </div>
                                        </div>

                                        <div class="collapse" id="collapseMore<?php echo $subject_key; ?>">
                                            <div class="courses-container-lister">
                                                <div class="row">
                                                    <?php do { ?>
                                                        <div class="col-lg-3 mb-4 position-relative">
                                                            <?php include('inc.programme-box.php') ?>
                                                        </div>
                                                    <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($totalRows_DisProg > $max_visible_first) { ?>
                                            <div class="text-center mt-4">
                                                <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseMore<?php echo $subject_key; ?>" aria-expanded="false" aria-controls="collapseMore<?php echo $subject_key; ?>" class="btn btn-custom-size-2 bg-white border btn-custom btn-collapse-view">
                                                    <div class="show_more">Show more <i data-feather="chevron-down"></i></div>
                                                    <div class="show_less">Show less <i data-feather="chevron-up"></i></div>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>
    </div>
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
</body>
</html>