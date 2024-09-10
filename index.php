<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = '';
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
    <?php 
        if (!$_SESSION['seen_initial_country_options']) {
            $_SESSION['seen_initial_country_options'] = true;
        }

        if ((!$coresite_curr_country_slug && !$coresite_curr_state_slug && !$_SESSION['location_of_choice']) || !$_SESSION['seen_initial_country_options']) {
            include('inc.modal-country-options.php'); 
        }
    ?>
    <div class="main-content">
        <div class="container">
            <div class="hero-sections rounded-8 py-5 mt-custom position-relative text-white z-index-1 bg-main-3 add-bg-image-2" style="background-image: url(<?php echo ROOT_DIR; ?>images/banner-cover-3.jpg);">

                <div class="overlay rounded-8"></div>

                <div class="container-fluid position-relative z-index-1">

                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h1 class="text-white text-bold ft-custom-hero">Rapid Mastery in Real Estate Dynamics</h1>
                            <p class="ft-normal-2 text-white">Condensed Courses Tailored for Professionals. Explore Key Areas - Valuation, Investment, and Sustainability. Propel Your Real Estate Career Quickly.</p>

                            <div class="row">
                                <div class="col-lg-6 mb-lg-0 mb-3">
                                    <a class="btn btn-custom-size text-bold bg-green w-100 text-white btn-custom" href="<?php echo ROOT_DIR.$location_slug; ?>category/short-courses">Explore all courses</a>
                                </div>

                                <div class="col-lg-6">
                                    <a class="btn btn-custom-size bg-white text-dark btn-custom w-100 text-bold w-sm-100" href="<?php echo $link_portal_path."user/access".$url_extend; ?>">Log in</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">

                        </div>
                    </div>

                    <div class="row">
                        <div class="course-details-container mt-5">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="box-details bg-white rounded-0 p-lg-4 p-3 text-dark">
                                        <div class="mb-4">
                                            <h2 class="text-bold text-upper-custom ft-custom-hero-2">Search for courses</h2>
                                        </div>
                                        <form action="<?php echo $link_root_default.$location_slug; ?>search" method="GET">
                                            <div class="row align-items-center">
                                                <div class="col-lg-3 mb-lg-0 mb-3">
                                                    <div class="form-floating">
                                                        <input name="k" type="search" class="form-control" id="floatingInput" placeholder="What do you want to learn?" required>
                                                        <label for="floatingInput">What do you want to learn?</label>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 mb-lg-0 mb-3">
                                                    <div class="form-floating">
                                                        <select name="p_type" class="form-select" id="floatingSelectCat" aria-label="Floating label select">
                                                            <option value="">--Choose--</option>
                                                            <?php // Categories
                                                                $search_prog_categories_array = array(
                                                                    'short course' => 'Professional Certificate Courses',
                                                                    'advanced professional' => 'Advanced Professional Certificate Courses',
                                                                    'masters' => 'Master\'s Degree',
                                                                    'intl diploma' => 'International Postgraduate Diploma (Endorsed)',
                                                                );
                                                            ?>

                                                            <?php foreach ($search_prog_categories_array as $key => $value) { ?>
                                                                <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <label for="floatingSelect">Category</label>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 mb-lg-0 mb-3">
                                                    <div class="form-floating">
                                                        <select name="p_cat" class="form-select" id="floatingSelectSub" aria-label="Floating label select">
                                                            <option value="">--Choose--</option>
                                                            <?php // Get all subjects
                                                                mysqli_select_db($config, $database_config);
                                                                $query_valuesList = "SELECT * 
                                                                                    FROM options
                                                                                    WHERE option_name = 'programme subjects' 
                                                                                        AND status = 'active' 
                                                                                    ORDER BY option_sort DESC, option_id ASC";
                                                                $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                                                $row_valuesList = mysqli_fetch_assoc($valuesList);
                                                                $totalRows_valuesList = mysqli_num_rows($valuesList);
                                                            ?>

                                                            <?php if ($totalRows_valuesList) do { ?>
                                                                <option value="<?php echo $row_valuesList['option_id'] ?>">
                                                                    <?php echo ucwords($row_valuesList['option_value']) ?>
                                                                </option>
                                                            <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                                        </select>
                                                        <label for="floatingSelect">Subject</label>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <button class="btn btn-custom-size text-bold bg-green w-100 text-white btn-custom" style="padding-top: 14px; padding-bottom: 14px;">Search <i data-feather="search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php $cache_session_id = 'homepage_degree_courses_v12'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
            <!-- <?php // Get the matching masters programmes
                // include('inc.reset-programme-query-params.php');
                $prog_box_display_type = 'masters';
                $prog_query_limit = "LIMIT 8";
                include('inc.programme-query.php');
            ?>

            <?php if ($totalRows_DisProg) { ?>
                <div class="courses-sect arrow-circle py-5">
                    <div class="container">
                        <p class="text-green text-bold mb-1">Degree Programme</p>
                        <h3 class="mb-4">Explore Masters' Courses</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="owl-carousel position-relative courses-carousel owl-theme">
                                    <?php do { ?>
                                        <div class="item position-relative h-100">
                                            <?php include('inc.programme-box.php') ?>
                                        </div>
                                    <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo ROOT_DIR.$location_slug; ?>category/masters-degrees" class="text-bold text-dark text-decoration-none">Explore Degree <i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?> -->
        <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>

        <?php $cache_session_id = 'homepage_short_courses_v12'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
            <?php
                $prog_box_display_type = 'short course';
                $prog_query_limit = "LIMIT 8";
                include('inc.programme-query.php');
            ?>

            <?php if ($totalRows_DisProg) { ?>
                <div class="courses-sect arrow-circle py-5">
                    <div class="container">
                        <p class="text-green text-bold mb-1">Certificates & Courses for Professionals</p>
                        <h3 class="mb-4">Explore Professional Courses</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="owl-carousel position-relative courses-carousel owl-theme">
                                    <?php do { ?>
                                        <div class="item position-relative h-100">
                                            <?php include('inc.programme-box.php') ?>
                                        </div>
                                    <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo ROOT_DIR.$location_slug; ?>category/short-courses" class="text-bold text-dark text-decoration-none">Explore Professional Certificate Courses <i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>

        <?php $cache_session_id = 'homepage_popular_courses_v4'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
            <?php // Popular courses Query
                $use_new_query = true; 

                
                if ($override_course_country_value) {
                    $sql_course_country = $override_course_country_value;
                }
                elseif ($coresite_curr_country_slug) {
                    $sql_course_country = is_country($coresite_curr_country_slug, "countryID");
                }
                else {
                    $sql_course_country = "";
                }
        
                // if the category has courses for only this location, display them
                if ($sql_course_country) {
                    $prog_to_country_join_additional = " JOIN course_to_country ctc ON ctc.prog_id = np.programme_id ";
                    $prog_to_country_where_additional = " AND ctc.countryID IN ('".$sql_course_country."', '***') ";
        
                    $thumbnail_to_country_join_additional = " LEFT JOIN content_info_to_country citc ON citc.cic_title = np.programme_id AND citc.cic_country_id = '".$sql_course_country."' AND citc.cic_type = 'thumbnail' AND cic_status = 'active' ";
                    $thumbnail_to_country_columns = ", citc.cic_country_id, citc.cic_banner_image as country_image ";
        
                    $country_max_no_of_instalments_join_additional = " LEFT JOIN content_info_to_country citc_install ON citc_install.cic_title = np.programme_id AND citc_install.cic_country_id = '".$sql_course_country."' AND citc_install.cic_type = 'max-instalments' AND citc_install.cic_status = 'active' ";
                    $country_max_no_of_instalments_column = ", citc_install.cic_body as country_max_instalment ";
                }

                if ($use_new_query) {
                    $prog_query_where_modifier = $prog_query_group_by_modifier = $prog_query_new_join_modifier = $prog_query_new_column_modifier = "";
                    $prog_query_order_by_modifier = " ORDER BY np.programme_id DESC ";
                }
                else {
                    $prog_query_new_column_modifier = "COUNT(np.programme_name) as no_of_course_apps, ";
                    $prog_query_where_modifier = " AND p.deleted = '0' 
                                                AND p.entry_date BETWEEN '".date("Y-01-01")."' AND  '".date("Y-m-d", strtotime("last day of this month"))."' ";
                    $prog_query_group_by_modifier = " GROUP BY np.programme_name ";
                    $prog_query_order_by_modifier = " ORDER BY COUNT(np.programme_name) DESC";
                    $prog_query_new_join_modifier = " JOIN prospects p ON p.prog_interest = op_other.option_id ";

                    if ($order_progs_by_partner_schools) {
                        $prog_partner_school_join_additional = " LEFT JOIN (
                                                                    SELECT option_two, option_one 
                                                                    FROM option_relationships 
                                                                    WHERE relationship_name = 'programme partner schools' 
                                                                    GROUP BY option_one 
                                                                ) option_school ON option_school.option_one = np.programme_id 
                                                                LEFT JOIN partner_schools pschool ON option_school.option_two = pschool.partner_id AND pschool.partner_status = 'current' AND pschool.deleted = '0' ";
            
                        $prog_query_order_by = "  , CASE 
                                                        WHEN pschool.is_masked = '0' THEN 1 
                                                        ELSE 0 
                                                    END DESC, 
                                                    CASE 
                                                        WHEN pschool.logo <> '' THEN 1 
                                                        ELSE 0 
                                                    END DESC, 
                                                    CASE 
                                                        WHEN pschool.type = 'school' THEN 3 
                                                        WHEN pschool.type = 'both' THEN 2 
                                                        WHEN pschool.type = 'awarding' THEN 1 
                                                        ELSE 0 
                                                    END DESC, 
                                                    CASE 
                                                        WHEN pschool.partner_name NOT LIKE '%MSBM%' THEN 1 
                                                        ELSE 0 
                                                    END DESC";
                    }
                }
                
                $coresite_popular_courses_array = array();

                mysqli_select_db($config, $database_config);
                $query_DisProg = "SELECT ".$prog_query_new_column_modifier." np.*, oprel.option_two, op.option_id as subcategory_id, op.parent_option_id as parent_category_id, op.option_value, 
                                    op_other.option_id backend_prog_id, op_other.option_value backend_prog_name ".$thumbnail_to_country_columns.$country_max_no_of_instalments_column."  
                                FROM option_relationships oprel 
                                JOIN options op ON oprel.option_two = op.option_id 
                                JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                ".$prog_to_country_join_additional.$thumbnail_to_country_join_additional.$country_max_no_of_instalments_join_additional."
                                
                                JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                                JOIN options op_other ON option_prog_other.option_one = op_other.option_id 
                                ".$prog_query_new_join_modifier."
                                ".$prog_partner_school_join_additional." 

                                WHERE oprel.relationship_name = 'programme type' 
                                    AND option_prog_other.relationship_name = 'old prog to new prog' 
                                    AND op_other.status = 'active' 
                                    AND op.option_id IN (883, 409) /* APCC, PCC */
                                    AND np.deleted = '0'
                                    ".$prog_query_where_modifier."
                                ".$prog_query_group_by_modifier."
                                ".$prog_query_order_by_modifier."
                                ".$prog_query_order_by." 
                                LIMIT 4";
                                //echo '<pre>'; print_r($query_DisProg); echo '</pre>'; exit();
                $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
                $row_DisProg = mysqli_fetch_assoc($DisProg);
                $totalRows_DisProg = mysqli_num_rows($DisProg);
            ?>
            <?php if ($totalRows_DisProg) { ?>
                <div class="courses-sect arrow-circle py-5">
                    <div class="container">
                        <p class="text-green text-bold mb-1">Latest courses added</p>
                        <h3 class="mb-4">New courses on London Real Estate Academy</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="owl-carousel position-relative courses-carousel owl-theme">
                                    <?php do { ?>
                                        <div class="item position-relative h-100">
                                            <?php include('inc.programme-box.php') ?>
                                        </div>
                                    <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>

        <?php include_once('inc.500-courses-segment.php'); ?> 
        
        <?php $cache_session_id = 'homepage_ipgd_courses_v1'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
            <?php
                $prog_box_display_type = 'intl diploma';
                $prog_query_limit = "LIMIT 8";
                include('inc.programme-query.php');
            ?>

            <?php if ($totalRows_DisProg) { ?>
                <div class="courses-sect arrow-circle py-5">
                    <div class="container">
                        <p class="text-green text-bold mb-1">International Postgraduate Diploma</p>
                        <h3 class="mb-4">Explore Diploma Courses</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="owl-carousel position-relative courses-carousel owl-theme">
                                    <?php do { ?>
                                        <div class="item position-relative h-100">
                                            <?php include('inc.programme-box.php') ?>
                                        </div>
                                    <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo ROOT_DIR.$location_slug; ?>category/international-postgraduate-diplomas" class="text-bold text-dark text-decoration-none">Explore International Postgraduate Diploma Courses <i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>

        <?php include_once('inc.testimonial-segment-new.php'); ?>
    </div>
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
  </body>
</html>