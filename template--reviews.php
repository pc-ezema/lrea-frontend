<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');

    require_once('inc.no-location-in-url.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.

    if (defined('ROOT_DIR_UK')) {
        $link_root_default = ROOT_DIR_UK;
        $link_root_path = ROOT_DIR_UK.$location_slug;
    }
    else {
        $link_root_default = ROOT_DIR;
        $link_root_path = ROOT_DIR.$location_slug;
    }

    // Get total reviews
    $total_available_reviews = get_total_all_reviews();
    $total_visible_reviews = get_total_visible_reviews();
    $total_invisible_reviews = $total_available_reviews - $total_visible_reviews;

    if ($total_visible_reviews < $least_expected_total_reviews) {
        $_SESSION['userfeedback'] = "Invalid request. Please try again later.";
        $_SESSION['feedbacktype'] = "error";

        redirect(ROOT_DIR);
    }

    $get_average_visible_rating_score = get_average_visible_rating_score();
    $avg_rating_excellent = 4.1;

    $sql_modifier = "";
    $score_filter = array();
    
    // rating filter
    if (isset($_GET['bad'])) {
        $score_filter[] = 1;
    }
    if (isset($_GET['poor'])) {
        $score_filter[] = 2;
    } 
    if (isset($_GET['average'])) {
        $score_filter[] = 3;
    } 
    if (isset($_GET['good'])) {
        $score_filter[] = 4;
    }
    if (isset($_GET['excellent'])) {
        $score_filter[] = 5;
    } 

    if (count($score_filter)) {
        $sql_modifier = "AND pr.rating_score IN (".implode(', ', $score_filter).")";
    }

    // Select all reviews. Criteria: must be approved, student must have given permission to share feedback publicly
    mysqli_select_db($config, $database_config);
    $query_Approved_Reviews = "SELECT pr.*, DATE_FORMAT(pr.date_added, '%d %b, %Y') as review_date, DATE_FORMAT(pr.date_modified, '%d %b, %Y') as reply_date, p.*, us.photo, np.programme_id, np.programme_name, np.url_name, IF (usi.nationality IS NOT NULL, usi.nationality, p.nationality) nationality2
                            FROM programme_rating pr
                            JOIN prospects p ON p.p_id = pr.p_id
                            JOIN users_students us ON p.email = us.email
                            JOIN options op ON op.option_id = p.prog_interest
                            JOIN option_relationships oprel ON oprel.option_one = op.option_id
                            JOIN new_programmes np ON oprel.option_two = np.programme_id
                            LEFT JOIN users_students_info usi ON usi.login_id = us.login_id 
                            WHERE pr.status = 'approved'
                                AND pr.share_feedback = '1'
                                AND pr.deleted = '0'
                                ".$sql_modifier."
                                AND oprel.relationship_name = 'old prog to new prog'
                                AND np.deleted = '0'
                            ORDER BY pr.rating_score DESC, 
                                    CASE 
                                        WHEN (
                                            SELECT lc.continentID
                                            FROM loccountries lc 
                                            WHERE lc.countryID = nationality2
                                        ) <> 5 THEN 3
                                        ELSE 1
                                    END DESC,
                                    CASE 
                                        WHEN us.photo <> '' THEN 3
                                        ELSE 1
                                    END DESC,
                                    pr.date_added DESC"; //echo '<pre>'; var_dump($query_Approved_Reviews); echo '</pre>'; exit();

    // Set pagination variables
    if (my_is_int($_GET['results'])) $limit = $_GET['results'] ; else $limit = 20;

    // Execute first step and assign it to a variable
    $pg_resources = paginate_b4_query($query_Approved_Reviews, $limit, $precisepage);
    extract($pg_resources);

    // Add LIMIT to query
    $query_Approved_Reviews = paginate_limit($query_Approved_Reviews, $pgstart, $pglimit);

    // Execute query
    $Approved_Reviews = mysqli_query($config, $query_Approved_Reviews) or die(mysqli_error($config));
    $row_Approved_Reviews = mysqli_fetch_assoc($Approved_Reviews);
    $totalRows_Approved_Reviews = mysqli_num_rows($Approved_Reviews);

    // Initialize pagination and assign to a variable. The value of which is the set of pagination links.
    $pagination = paginate_after_limit($page, $total_pages, $targetpage, $pgadjacents, $pglimit);

    // Record count
    $pagination_count = paginate_record_count($pglimit, $total_pages, $page);    

    $five_star_rating_percent = calculate_review_percentage_by_rating(5); 
    $four_star_rating_percent = calculate_review_percentage_by_rating(4); 
    $three_star_rating_percent = calculate_review_percentage_by_rating(3); 
    $two_star_rating_percent = calculate_review_percentage_by_rating(2); 
    $one_star_rating_percent = calculate_review_percentage_by_rating(1); 
	
	$page_title = 'Reviews';
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
            <div class="container position-relative mt-custom-2" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="text-start">
                            <h1 class="text-bold text-white ft-custom-hero-3 mb-4">Success stories
                                from our learners</h1>
                            <p class="text-white mb-4">Amazing individuals from all around the world who are enhancing their lives through <?php echo SITE_NAME; ?> share their inspiring experiences.</p>
                        </div>
                    </div>

                    <div class="col-lg-5 offset-lg-1">
                        <div class="row text-lg-end">
                            <div class="col-lg-6 col-6 mb-lg-0 mb-3">
                                <h6 class="text-white badge-main-2">Total Reviews</h6>
                                <h2 class="text-bold ft-custom-hero text-white"><?php echo number_format($total_available_reviews); ?></h2>
                            </div>

                            <div class="col-lg-6 col-6 mb-lg-0 mb-3">
                                <h6 class="text-white badge-main-2">Score</h6>
                                <h2 class="text-bold ft-custom-hero text-white"><?php echo $get_average_visible_rating_score; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="community-sect py-5">
            <div class="container">
                <div class="row">
                    <?php if ($totalRows_Approved_Reviews) { $i = 0; ?>
                        <div class="col-lg-5">
                            <h2 class="text-bold text-upper-custom mb-4">A thriving community of professionals</h2>
                            <p class="">We’ve helped thousands of digital professionals across the globe to pursue successful careers in Business and Management.</p>

                            <div class="card review-content-card rounded-8 p-lg-3">
                                <div class="card-body review-content-card-body">
                                    <div class="review-progress">
                                        <div class="mb-3">
                                            <h3 class="text-upper-custom mb-0"><?php echo number_format($total_available_reviews); ?> Reviews</h3>
                                            <p class="ft-sm <?php if($total_invisible_reviews == 0) echo 'd-none'; ?>"><?php echo number_format($total_invisible_reviews); ?> people have not given permission to show their reviews.</p>
                                        </div>

                                        <hr />

                                        <form action="" method="get">
                                            <div class="progress-set mb-3">
                                                <div class="row align-items-center justify-content-between progress-status mb-3">
                                                    <div class="col-lg-2 col-2">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="checkbox" name="excellent" class="form-check-input text-dark me-2 flex-shrink-0" <?php if (isset($_GET['excellent'])) echo 'checked'; ?> id="reviewCheck1">
                                                            <label class="form-check-label text-green text-bold" for="reviewCheck1">Excellent</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9 col-9">
                                                        <div class="progress rounded-pill progress-height">
                                                            <div class="progress-bar bg-green state-success" role="progressbar" style="width: <?php echo $five_star_rating_percent; ?>%" aria-valuenow="<?php echo $five_star_rating_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-2 d-none">
                                                        <p class="float-right text-dark ft-xs make-bold mb-0"><?php echo $five_star_rating_percent; ?>%</p>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center justify-content-between progress-status mb-3">
                                                    <div class="col-lg-2 col-2">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="checkbox" name="good" class="form-check-input text-dark me-2 flex-shrink-0" <?php if (isset($_GET['good'])) echo 'checked'; ?> id="reviewCheck2">
                                                            <label class="form-check-label text-green text-bold" for="reviewCheck2">Good</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9 col-9">
                                                        <div class="progress rounded-pill progress-height">
                                                            <div class="progress-bar bg-green state-success" role="progressbar" style="width: <?php echo $four_star_rating_percent; ?>%" aria-valuenow="<?php echo $four_star_rating_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-2 d-none">
                                                        <p class="float-right text-dark ft-xs make-bold mb-0"><?php echo $four_star_rating_percent; ?>%</p>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center justify-content-between progress-status mb-3">
                                                    <div class="col-lg-2 col-2">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="checkbox" name="average" class="form-check-input text-dark me-2 flex-shrink-0" <?php if (isset($_GET['average'])) echo 'checked'; ?> id="reviewCheck3">
                                                            <label class="form-check-label text-green text-bold" for="reviewCheck3">Average</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9 col-9">
                                                        <div class="progress rounded-pill progress-height">
                                                            <div class="progress-bar bg-green state-success" role="progressbar" style="width: <?php echo $three_star_rating_percent; ?>%" aria-valuenow="<?php echo $three_star_rating_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-2 d-none">
                                                        <p class="float-right text-dark ft-xs make-bold mb-0"><?php echo $three_star_rating_percent; ?>%</p>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center justify-content-between progress-status mb-3">
                                                    <div class="col-lg-2 col-2">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="checkbox" name="poor" class="form-check-input text-dark me-2 flex-shrink-0" id="reviewCheck4" <?php if (isset($_GET['poor'])) echo 'checked'; ?>>
                                                            <label class="form-check-label text-green text-bold" for="reviewCheck4">Poor</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9 col-9">
                                                        <div class="progress rounded-pill progress-height">
                                                            <div class="progress-bar bg-green state-success" role="progressbar" style="width: <?php echo $two_star_rating_percent; ?>%" aria-valuenow="<?php echo $two_star_rating_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-2 d-none">
                                                        <p class="float-right text-dark ft-xs make-bold mb-0"><?php echo $two_star_rating_percent; ?>%</p>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center justify-content-between progress-status mb-3">
                                                    <div class="col-lg-2 col-2">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="checkbox" name="bad" class="form-check-input text-dark me-2 flex-shrink-0" id="reviewCheck5" <?php if (isset($_GET['bad'])) echo 'checked'; ?>>
                                                            <label class="form-check-label text-green text-bold" for="reviewCheck5">Bad</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9 col-9">
                                                        <div class="progress rounded-pill progress-height">
                                                            <div class="progress-bar bg-green state-success" role="progressbar" style="width: <?php echo $one_star_rating_percent; ?>%" aria-valuenow="<?php echo $one_star_rating_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 col-2 d-none">
                                                        <p class="float-right text-dark ft-xs make-bold mb-0"><?php echo $one_star_rating_percent; ?>%</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr />

                                            <button type="submit" class="btn btn-custom-size-2 bg-color-3 text-white">Apply Filter</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>                       
                        
                        <div class="col-lg-7">
                            <div class="card card-body bg-white rounded-8 p-lg-4 p-3">          
                                <?php do { $i++; ?>
                                    <?php 
                                        $student_review_photo = '';
                                        $sample_avatar_image = $row_Approved_Reviews['gender']=='f' ? 'avatar-1.jpg' : 'avatar-2.jpg';
                                        $student_review_photo = ROOT_DIR."images/".$sample_avatar_image;

                                        // Get the country name
                                        mysqli_select_db($config, $database_config);
                                        $query_CountryInfo = "SELECT lc.countryName 
                                                            FROM loccountries lc
                                                            WHERE lc.countryID = ".sql_int($row_Approved_Reviews['nationality2'])." 
                                                            LIMIT 1";
                                        $CountryInfo = mysqli_query($config, $query_CountryInfo) or die(mysqli_error($config));
                                        $row_CountryInfo = mysqli_fetch_assoc($CountryInfo);

                                        $reviewer_country = $row_CountryInfo['countryName'] ? ' - '.$row_CountryInfo['countryName'] : '';
                                        $reviewer_name_to_use = 'Anonymous'; 

                                        if ($row_Approved_Reviews['share_full_info'] == '1') { // student permits sharing full info
                                            if ($row_Approved_Reviews['student_photo']) { // student has a photo
                                                if ($site_version == "localhost") {
                                                    if (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.get_thumbnail_image($row_Approved_Reviews['student_photo']))) {
                                                        $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.get_thumbnail_image($row_Approved_Reviews['student_photo']);
                                                    }
                                                    elseif (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.$row_Approved_Reviews['student_photo'])) {
                                                        $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.$row_Approved_Reviews['student_photo'];
                                                    }
                                                }
                                                else {
                                                    if (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.get_thumbnail_image($row_Approved_Reviews['student_photo']))) {
                                                        $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.get_thumbnail_image($row_Approved_Reviews['student_photo']);
                                                    }
                                                    elseif (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.$row_Approved_Reviews['student_photo'])) {
                                                        $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.$row_Approved_Reviews['student_photo'];
                                                    }
                                                    elseif (file_exists(PORTAL_FOLDER.'/uploads/student_photos/'.get_thumbnail_image($row_Approved_Reviews['student_photo']))) {
                                                        $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.get_thumbnail_image($row_Approved_Reviews['student_photo']);
                                                    }
                                                    elseif (file_exists(PORTAL_FOLDER.'/uploads/student_photos/'.$row_Approved_Reviews['student_photo'])) {
                                                        $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.$row_Approved_Reviews['student_photo'];
                                                    }
                                                }
                                            }
                                            
                                            $use_lastname_initial = false;

                                            if ($use_lastname_initial) {
                                                $reviewer_full_name = explode(" ", $row_Approved_Reviews['student_name']);
                                                    
                                                $first_name = ucwords($reviewer_full_name[0]);
                                                $lastname_initial = ucwords(substr($reviewer_full_name[1], 0, 1));

                                                $reviewer_name_to_use = $first_name.' '.$lastname_initial.'.';
                                            }
                                            else {
                                                $reviewer_name_to_use = $row_Approved_Reviews['student_name'];
                                            }
                                        }
                                    ?>
                                    <div class="review-content py-5 <?php if($i != $totalRows_Approved_Reviews) echo 'border-bottom'; ?>">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="profile-img-size-2 me-2">
                                                            <img src="<?php echo $student_review_photo; ?>" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <em>
                                                            <h4 class="text-bold mb-0"><?php echo $reviewer_name_to_use; ?></h4>
                                                            <small class="mb-0 text-muted"><?php echo $reviewer_country; ?></small>
                                                        </em>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-7">
                                                <div class="">
                                                    <p class="text-bold mb-0">
                                                        <?php // get the programme type
                                                            $category_type = get_programme_relationship($row_Approved_Reviews['programme_id'], 'programme level');
                                                            if (preg_match('/^free/i', $category_type)) { // display Executive Education if programme is a free course
                                                                echo 'Executive Education';
                                                            } else {
                                                                echo ucwords($category_type);
                                                            }
                                                        ?>
                                                    </p>
                                                    <p class="flex-grow-1 ft-sm"><?php echo nl2br(ucfirst($row_Approved_Reviews['testimony'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } while ($row_Approved_Reviews = mysqli_fetch_assoc($Approved_Reviews)); ?>                                
                            </div>
                            <?php if ($totalRows_Approved_Reviews) { ?>
                                <nav class="mt-3 ms-auto d-flex justify-content-center py-5">
                                    <?php echo $pagination; ?>
                                </nav>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <h3 class="make-bold text-center">No Review</h3>
                    <?php } ?>
                </div>
            </div>
        </div>
        
    </div>
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
    <script>
        $(function() {
            $('[data-toggle="popover"]').popover()
        })
    </script>   
  </body>
</html>