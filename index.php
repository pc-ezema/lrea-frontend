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
    /* HERO */
    .hero {
        padding: 56px 0 36px;
        position: relative
    }

    .hero-title {
        font-size: 3.4rem;
        font-weight: 800;
        line-height: 60px;
        letter-spacing: -0.5px;
    }

    .hero-title .accent,
    .mobile-hero-title .accent {
        color: var(--green);
    }

    .hero-desc {
        color: #000;
        margin-top: 18px;
        max-width: 520px;
        margin-bottom: 2rem;
    }

    .btn-outline-soft {
        background: #ffeded;
        color: var(--green) !important;
        font-weight: 600
    }

    /* LEFT rings */
    .rings-left {
        position: absolute;
        left: -120px;
        /* top: -20px; */
        z-index: -1;
        opacity: .9;
        pointer-events: none;
    }

    .rings-right {
        position: absolute;
        right: 40px;
        bottom: -90px;
        z-index: 4;
        opacity: .85;
        pointer-events: none;
    }

    .rings-left circle,
    .rings-right circle {
        fill: none;
        stroke: #e6f6f0;
        stroke-width: 1
    }

    /* Visual side */
    .visual-wrap {
        position: relative;
        height: 420px;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .big-circle {
        position: absolute;
        width: 400px;
        height: 420px;
        border-radius: 50%;
        border: 1px solid var(--green);
        left: 10%;
        top: 25px;
        transform: translateY(-10%);
        z-index: 1;
    }

    .portrait {
        width: 400px;
        height: 420px;
        border-radius: 50%;
        overflow: hidden;
        border: 8px solid var(--green);
        position: relative;
        z-index: 2;
        box-shadow: 0 12px 30px rgba(16, 24, 32, 0.15);
        background: var(--green)
    }

    .portrait img {
        width: 100%;
        height: 100%;
        object-fit: cover
    }

    /* dots */
    .dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--green);
        position: absolute;
        z-index: 3
    }

    .dot.three {
        left: 10%;
        bottom: 16%;
        background: var(--green)
    }

    /* stats */
    .stat {
        position: absolute;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(12, 20, 30, 0.08);
        padding: 12px 14px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-weight: 700;
        z-index: 4;
        min-width: 120px
    }

    .stat.small {
        font-size: .86rem;
        min-width: 110px
    }

    .stat .icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--green)
    }

    .stat.top-right {
        right: 6%;
        top: 6%
    }

    .stat.left-mid {
        left: 6%;
        top: 40%
    }

    .stat.bottom-right {
        right: 10%;
        bottom: 4%
    }

    /* Partners */
    .partners {
        padding: 26px 0 46px;
        border-top: 1px solid rgba(0, 0, 0, 0.05)
    }

    .partners .count {
        color: var(--green);
        font-weight: 800;
        font-size: 1.6rem
    }

    .partners .label {
        color: #6c6c6c;
        font-weight: 600
    }

    .logos img {
        max-height: 44px;
        margin: 0 12px
    }

    /* Mobile */
    @media(max-width:991px) {
        .left {
            text-align: center;
        }

        .right {
            display: none !important;
        }

        .hero-title {
            font-size: 2.2rem;
            display: none;
        }

        .mobile-hero-title {
            display: block !important;
            font-size: 4rem;
        }

        .mobile-hero-title .accent {
            display: block !important
        }

        .hero-desc {
            max-width: 100% !important;
        }

        .hero-buttons {
            justify-content: center;
        }

        .big-circle {
            width: 300px;
            height: 300px
        }

        .portrait {
            width: 200px;
            height: 200px
        }

        .rings-left,
        .rings-right {
            display: none
        }
    }

    @media(max-width:575px) {
        .mobile-hero-title {
            font-size: 2.5rem;
        }

        .hero-title {
            font-size: 1.6rem
        }

        .hero-desc {
            font-size: .95rem
        }

        .visual-wrap {
            height: auto
        }

        .big-circle {
            display: none
        }

        .stat {
            position: relative;
            margin-top: 14px
        }
    }

    :root {
        --tail-width: 220px;
        /* width of fixed logo */
        --tail-gap: 18px;
        /* gap before fixed logo */
        --scroll-duration: 18s;
        /* speed of carousel */
    }

    .logo-wrapper {
        display: flex;
        align-items: center;
        padding: 18px 20px;
        /* background: #f8f9fa; */
    }

    /* Fixed Logo */
    .tail-logo {
        flex: 0 0 var(--tail-width);
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 3;
    }

    .tail-logo img {
        display: block;
        max-height: 70px;
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    /* Carousel Area */
    .carousel-wrapper {
        flex: 1 1 auto;
        overflow: hidden;
        position: relative;
        z-index: 1;
        padding-right: calc(var(--tail-width) + var(--tail-gap));
    }

    .carousel-track {
        display: flex;
        gap: 28px;
        align-items: center;
        will-change: transform;
        animation: scroll var(--scroll-duration) linear infinite;
    }

    .logo-item {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-item img {
        display: block;
        max-height: 50px;
        width: auto;
        object-fit: contain;
    }

    /* Keyframes */
    @keyframes scroll {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }

        /* works because we duplicated the set */
    }

    /* Pause on Hover */
    .carousel-wrapper:hover .carousel-track {
        animation-play-state: paused;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .tail-logo {
            display: none;
        }

        .carousel-wrapper {
            padding-right: 20px;
        }

        .logo-item img {
            max-height: 60px;
        }
    }
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
        <div class="hero-sections rounded-8 py-5 mt-custom position-relative text-white z-index-1 bg-main-3 add-bg-image-2">
            <div class="container position-relative">
                <!-- bigger rings left -->
                <svg class="rings-left" width="500" height="500" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <g transform="translate(50,40)">
                        <circle cx="130" cy="140" r="160" fill="none" stroke="#e6f6f0" stroke-width="2" />
                        <circle cx="130" cy="140" r="130" fill="none" stroke="#eaf9f5" stroke-width="1.6" />
                        <circle cx="130" cy="140" r="100" fill="none" stroke="#f5fbfa" stroke-width="1.2" />
                        <circle cx="130" cy="140" r="60" fill="none" stroke="#eef9f5" stroke-width="1" />
                    </g>
                </svg>

                <!-- BOTTOM-RIGHT concentric rings -->
                <svg class="rings-right" width="230" height="230" viewBox="0 0 250 250" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <g transform="translate(30,30)">
                        <circle cx="100" cy="100" r="100" fill="none" stroke="#e6f6f0" stroke-width="2" />
                        <circle cx="100" cy="100" r="70" fill="none" stroke="#eaf9f5" stroke-width="1.6" />
                        <circle cx="100" cy="100" r="40" fill="none" stroke="#f5fbfa" stroke-width="1.2" />
                        <circle cx="100" cy="100" r="15" fill="none" stroke="#eef9f5" stroke-width="1" />
                    </g>
                </svg>

                <div class="row align-items-center">
                    <div class="col-lg-6 left">
                        <h1 class="hero-title">Your Complete Guide<br>to a <span class="accent">Successful </span><br><span class="accent">Real Estate Career</span></h1>
                        <h1 class="mobile-hero-title d-none">Your Complete Guide to a Successful <span class="accent">Real Estate Career</span></h1>
                        <p class="hero-desc">Gain practical real estate industry-ready skills in record time.</p>
                        <div class="mt-3 d-flex gap-3 hero-buttons">
                            <a class="btn btn-custom-size-2 bg-green text-white btn-custom text-bold w-sm-100" href="<?php echo $link_portal_path . "user/register" . $url_extend; ?>">Get Started</a>
                            <a class="btn btn-outline-soft btn-custom-size-2 btn-custom text-bold w-sm-100" href="#menuModal" data-bs-toggle="modal">Explore all Courses</a>
                        </div>
                    </div>

                    <div class="col-lg-6 right">
                        <div class="visual-wrap">
                            <div class="big-circle"></div>
                            <div class="dot three"></div>
                            <div class="portrait">
                                <img src="<?php echo ROOT_DIR . 'images/mask-group.png?v=' . $fileCacheVersion; ?>" alt="Graduate">
                            </div>
                            <div class="stat top-right">
                                <div><img class="icon" src="<?php echo ROOT_DIR . 'images/progress-circle.png?v=' . $fileCacheVersion; ?>" alt="Online Courses"></div>
                                <div style="color: #000">
                                    <div>5K+</div><small>Online Courses</small>
                                </div>
                            </div>
                            <div class="stat left-mid small">
                                <div class="icon"><img class="icon" src="<?php echo ROOT_DIR . 'images/board.png?v=' . $fileCacheVersion; ?>" alt="Online Courses"></div>
                                <div style="color: #000">
                                    <div>2K+</div><small>Video Courses</small>
                                </div>
                            </div>
                            <div class="stat bottom-right small">
                                <div class="icon"><img class="icon" src="<?php echo ROOT_DIR . 'images/online-education.png?v=' . $fileCacheVersion; ?>" alt="Online Courses"></div>
                                <div style="color: #000">
                                    <div>250+</div><small>Tutors</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="logo-wrapper">
            <!-- Fixed Logo -->
            <div class="tail-logo">
                <img id="tailImg" src="<?php echo ROOT_DIR . 'images/partner-logos/lrea-logo.png?v=' . $fileCacheVersion; ?>" alt="London Real Estate Academy">
            </div>

            <!-- Carousel (scrolling logos) -->
            <div class="carousel-wrapper">
                <div class="carousel-track">
                    <!-- Original Logos -->
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/metropolitan-logo.png?v=' . $fileCacheVersion; ?>" alt="Metropolitan"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/world-bridge-university-logo.png?v=' . $fileCacheVersion; ?>" alt="World Bridge"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/glasgow-business-school-logo.png?v=' . $fileCacheVersion; ?>" alt="Glasgow"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/ucam-logo.png?v=' . $fileCacheVersion; ?>" alt="UCAM"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/gloucestershire-logo.png?v=' . $fileCacheVersion; ?>" alt="Gloucestershire"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/universidad-azteca-logo.png?v=' . $fileCacheVersion; ?>" alt="Azteca"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/girne-american-university-logo.png?v=' . $fileCacheVersion; ?>" alt="Girne American"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/eimt-logo.png?v=' . $fileCacheVersion; ?>" alt="EIMT"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/international-american-university-logo.png?v=' . $fileCacheVersion; ?>" alt="IAU"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/rushford-logo.png?v=' . $fileCacheVersion; ?>" alt="Rushford"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/birchwood-university-logo.png?v=' . $fileCacheVersion; ?>" alt="Birchwood"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/othm-qualifications-logo.png?v=' . $fileCacheVersion; ?>" alt="OTHM"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/cpd-group-logo.png?v=' . $fileCacheVersion; ?>" alt="CPD"></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/british-council-logo.png?v=' . $fileCacheVersion; ?>" alt="British Council"></div>

                    <!-- Duplicate Logos for Infinite Scroll -->
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/metropolitan-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/world-bridge-university-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/glasgow-business-school-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/ucam-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/gloucestershire-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/universidad-azteca-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/girne-american-university-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/eimt-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/international-american-university-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/rushford-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/birchwood-university-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/othm-qualifications-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/cpd-group-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                    <div class="logo-item"><img src="<?php echo ROOT_DIR . 'images/partner-logos/british-council-logo.png?v=' . $fileCacheVersion; ?>" alt=""></div>
                </div>
            </div>
        </div>

        <?php include_once('inc.partner-segment-new.php'); ?>

        <?php $cache_session_id = 'homepage_degree_courses_v12';
        $target_key = md5(curPageURL(true));
        $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */
        if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) {
            echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/
        } else {
            ob_start(); ?>
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
                                    <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo ROOT_DIR . $location_slug; ?>category/masters-degrees" class="text-bold text-dark text-decoration-none">Explore Degree <i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?> -->
        <?php $cache_html = compress_page(ob_get_contents());
            ob_end_clean();
            add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date);
            echo $cache_html;
        } ?>

        <?php $cache_session_id = 'homepage_short_courses_v12';
        $target_key = md5(curPageURL(true));
        $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */
        if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) {
            echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/
        } else {
            ob_start(); ?>
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
                                    <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo ROOT_DIR . $location_slug; ?>category/short-courses" class="text-bold text-dark text-decoration-none">Explore Professional Certificate Courses <i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents());
            ob_end_clean();
            add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date);
            echo $cache_html;
        } ?>

        <?php $cache_session_id = 'homepage_popular_courses_v4';
        $target_key = md5(curPageURL(true));
        $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */
        if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) {
            echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/
        } else {
            ob_start(); ?>
            <?php // Popular courses Query
            $use_new_query = true;


            if ($override_course_country_value) {
                $sql_course_country = $override_course_country_value;
            } elseif ($coresite_curr_country_slug) {
                $sql_course_country = is_country($coresite_curr_country_slug, "countryID");
            } else {
                $sql_course_country = "";
            }

            // if the category has courses for only this location, display them
            if ($sql_course_country) {
                $prog_to_country_join_additional = " JOIN course_to_country ctc ON ctc.prog_id = np.programme_id ";
                $prog_to_country_where_additional = " AND ctc.countryID IN ('" . $sql_course_country . "', '***') ";

                $thumbnail_to_country_join_additional = " LEFT JOIN content_info_to_country citc ON citc.cic_title = np.programme_id AND citc.cic_country_id = '" . $sql_course_country . "' AND citc.cic_type = 'thumbnail' AND cic_status = 'active' ";
                $thumbnail_to_country_columns = ", citc.cic_country_id, citc.cic_banner_image as country_image ";

                $country_max_no_of_instalments_join_additional = " LEFT JOIN content_info_to_country citc_install ON citc_install.cic_title = np.programme_id AND citc_install.cic_country_id = '" . $sql_course_country . "' AND citc_install.cic_type = 'max-instalments' AND citc_install.cic_status = 'active' ";
                $country_max_no_of_instalments_column = ", citc_install.cic_body as country_max_instalment ";
            }

            if ($use_new_query) {
                $prog_query_where_modifier = $prog_query_group_by_modifier = $prog_query_new_join_modifier = $prog_query_new_column_modifier = "";
                $prog_query_order_by_modifier = " ORDER BY np.programme_id DESC ";
            } else {
                $prog_query_new_column_modifier = "COUNT(np.programme_name) as no_of_course_apps, ";
                $prog_query_where_modifier = " AND p.deleted = '0' 
                                                AND p.entry_date BETWEEN '" . date("Y-01-01") . "' AND  '" . date("Y-m-d", strtotime("last day of this month")) . "' ";
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
            $query_DisProg = "SELECT " . $prog_query_new_column_modifier . " np.*, oprel.option_two, op.option_id as subcategory_id, op.parent_option_id as parent_category_id, op.option_value, 
                                    op_other.option_id backend_prog_id, op_other.option_value backend_prog_name " . $thumbnail_to_country_columns . $country_max_no_of_instalments_column . "  
                                FROM option_relationships oprel 
                                JOIN options op ON oprel.option_two = op.option_id 
                                JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                " . $prog_to_country_join_additional . $thumbnail_to_country_join_additional . $country_max_no_of_instalments_join_additional . "
                                
                                JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                                JOIN options op_other ON option_prog_other.option_one = op_other.option_id 
                                " . $prog_query_new_join_modifier . "
                                " . $prog_partner_school_join_additional . " 

                                WHERE oprel.relationship_name = 'programme type' 
                                    AND option_prog_other.relationship_name = 'old prog to new prog' 
                                    AND op_other.status = 'active' 
                                    AND op.option_id IN (883, 409) /* APCC, PCC */
                                    AND np.deleted = '0'
                                    " . $prog_query_where_modifier . "
                                " . $prog_query_group_by_modifier . "
                                " . $prog_query_order_by_modifier . "
                                " . $prog_query_order_by . " 
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
                                    <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents());
            ob_end_clean();
            add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date);
            echo $cache_html;
        } ?>

        <?php include_once('inc.500-courses-segment.php'); ?>

        <?php $cache_session_id = 'homepage_ipgd_courses_v1';
        $target_key = md5(curPageURL(true));
        $expiration_date = date("Y-m-d H:i:s", strtotime('+1 hour')); /* Don't edit beyond here */
        if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) {
            echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/
        } else {
            ob_start(); ?>
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
                                    <?php } while ($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo ROOT_DIR . $location_slug; ?>category/international-postgraduate-diplomas" class="text-bold text-dark text-decoration-none">Explore International Postgraduate Diploma Courses <i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php $cache_html = compress_page(ob_get_contents());
            ob_end_clean();
            add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date);
            echo $cache_html;
        } ?>

        <?php include_once('inc.testimonial-segment-new.php'); ?>

    </div>

    <?php require_once('components/footer.inc.php'); ?>

    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?>

</body>

</html>