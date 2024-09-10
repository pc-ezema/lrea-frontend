<?php $cache_session_id = 'testimonial_slider_v2'; $target_key = md5(curPageURL(true)); $expiration_date = date("Y-m-d H:i:s", strtotime('+90 minutes')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
    <?php
        $display_general_reviews = true;
        $show_review_modal = false;

        $dis_query_limit = 8;

        // Get the reviews
        $part1_testimony_sql = "SELECT pr.*, p.*, IF (usi.nationality IS NOT NULL, usi.nationality, p.nationality) nationality2
                                FROM programme_rating pr
                                JOIN prospects p ON p.p_id = pr.p_id
                                JOIN users_students us ON p.email = us.email
                                LEFT JOIN users_students_info usi ON usi.login_id = us.login_id 

                                LEFT JOIN loccountries lc ON p.nationality = lc.countryID
                                LEFT JOIN myip_ms_countries msc ON lc.CNTRY = msc.countryID
                                LEFT JOIN loccountries lc2 ON usi.nationality = lc2.countryID
                                LEFT JOIN myip_ms_countries msc2 ON lc2.CNTRY = msc2.countryID

                                WHERE pr.status = 'approved'
                                    AND pr.share_feedback = '1'
                                    AND pr.deleted = '0'
                                    AND pr.published = '1' ";
        $part2_testimony_sql = "ORDER BY RAND()
                                LIMIT ".$dis_query_limit."";

        // Get all reviews selected to be published.
        if ($coresite_curr_country_name == 'Nigeria') { // Display only reviews from African students if location is Nigeria
            $display_general_reviews = false;

            mysqli_select_db($config, $database_config);
            $query_Published_Reviews = $part1_testimony_sql."
                                            AND (
                                                msc.continent = 'Africa' 
                                                AND 
                                                msc2.continent = 'Africa' 
                                            )
                                        ".$part2_testimony_sql;
            $Published_Reviews = mysqli_query($config, $query_Published_Reviews) or die(mysqli_error($config));
            $row_Published_Reviews = mysqli_fetch_assoc($Published_Reviews);
            $totalRows_Published_Reviews = mysqli_num_rows($Published_Reviews);
            
            if ($totalRows_Published_Reviews < 4) { // If the reviews are not up to the amount required to be displayed. Ignore Africans and display as it had always been.
                $display_general_reviews = true;
            }
        } 
        
        if ($display_general_reviews) { // Display other countries first since the location is not Nigeria
            mysqli_select_db($config, $database_config);
            $query_Published_Reviews = $part1_testimony_sql."
                                            AND (
                                                msc.continent <> 'Africa' 
                                                AND 
                                                msc2.continent <> 'Africa' 
                                            )
                                            ".$part2_testimony_sql;
            $Published_Reviews = mysqli_query($config, $query_Published_Reviews) or die(mysqli_error($config));
            $row_Published_Reviews = mysqli_fetch_assoc($Published_Reviews);
            $totalRows_Published_Reviews = mysqli_num_rows($Published_Reviews);
        }

        if ($use_new_website_look) { // New testimonial look. Populate the array that will get chunked for the new carousel type.
            $array_of_reviews = $batches_of_reviews = array();

            if ($totalRows_Published_Reviews) {
                do {
                    // Get the country name
                    mysqli_select_db($config, $database_config);
                    $query_CountryInfo = "SELECT lc.countryName 
                                        FROM loccountries lc
                                        WHERE lc.countryID = ".sql_int($row_Published_Reviews['nationality2'])." 
                                        LIMIT 1";
                    $CountryInfo = mysqli_query($config, $query_CountryInfo) or die(mysqli_error($config));
                    $row_CountryInfo = mysqli_fetch_assoc($CountryInfo);

                    // process name, photo and rating star
                    $student_review_photo = '';
                    $student_review_name = '';
                    $sample_avatar_image = $row_Published_Reviews['gender']=='f' ? 'student-avatar-female.png' : 'student-avatar-male.png';

                    if ($row_Published_Reviews['share_full_info'] == '1') { // student permits sharing full info
                        if ($row_Published_Reviews['student_photo']) { // student has a photo
                            if ($site_version == "localhost") {
                                if (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.get_thumbnail_image($row_Published_Reviews['student_photo']))) {
                                    $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.get_thumbnail_image($row_Published_Reviews['student_photo']);
                                }
                                elseif (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.$row_Published_Reviews['student_photo'])) {
                                    $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.$row_Published_Reviews['student_photo'];
                                }
                                else {
                                    $student_review_photo = ROOT_DIR_PORTAL.'img/'.$sample_avatar_image;
                                }
                            }
                            else {
                                if (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.get_thumbnail_image($row_Published_Reviews['student_photo']))) {
                                    $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.get_thumbnail_image($row_Published_Reviews['student_photo']);
                                }
                                elseif (file_exists('../'.PORTAL_FOLDER.'/uploads/student_photos/'.$row_Published_Reviews['student_photo'])) {
                                    $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.$row_Published_Reviews['student_photo'];
                                }
                                elseif (file_exists(PORTAL_FOLDER.'/uploads/student_photos/'.get_thumbnail_image($row_Published_Reviews['student_photo']))) {
                                    $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.get_thumbnail_image($row_Published_Reviews['student_photo']);
                                }
                                elseif (file_exists(PORTAL_FOLDER.'/uploads/student_photos/'.$row_Published_Reviews['student_photo'])) {
                                    $student_review_photo = ROOT_DIR_PORTAL.'uploads/student_photos/'.$row_Published_Reviews['student_photo'];
                                }
                                else {
                                    $student_review_photo = ROOT_DIR_PORTAL.'img/'.$sample_avatar_image;
                                }
                            }
                        } else { // student has no photo. Hence use avatar.
                            $student_review_photo = ROOT_DIR_PORTAL.'img/'.$sample_avatar_image;
                        }

                        $reviewer_full_name = explode(" ", $row_Published_Reviews['student_name']);
                            
                        $first_name = ucwords($reviewer_full_name[0]);
                        $lastname_initial = ucwords(substr($reviewer_full_name[1], 0, 1));

                        $student_review_name = $first_name.' '.$lastname_initial.'.';
                    } else { // sharing full info not permitted by student, use avatar image | Display name as Anonymous
                        $student_review_photo = ROOT_DIR_PORTAL.'img/'.$sample_avatar_image;
                        $student_review_name = 'Anonymous';
                    }

                    $star_icon = "";
                                                            
                    if ($row_Published_Reviews['rating_score'] == '5') {
                        $star_icon = MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/green-stars.png';
                    } elseif ($row_Published_Reviews['rating_score'] == '4') {
                        $star_icon = MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/primary-stars.png';
                    } elseif ($row_Published_Reviews['rating_score'] == '3') {
                        $star_icon = MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/info-stars.png';
                    } elseif ($row_Published_Reviews['rating_score'] == '2') {
                        $star_icon = MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/warning-stars.png';
                    } else {
                        $star_icon = MEDIA_ROOT_DIR.$fileCacheVersion.'-images/banners/danger-stars.png';
                    }

                // Get the programme type
                $programme_id = get_new_programme_info($row_Published_Reviews['prog_interest'], 'programme_id');
                    $category_type = get_programme_relationship($programme_id, 'programme level');
                    
                    if (preg_match('/^free/i', $category_type)) { // Display Executive Education if programme is a free course
                        $category_type = 'Executive Education'; 
                    }

                    $array_of_reviews[] = array(
                        "student_photo" => $student_review_photo,
                        "testimony" => $row_Published_Reviews['testimony'],
                        "student_name" => $student_review_name,
                        "star_icon" => $star_icon,
                        "country_name" => $row_CountryInfo['countryName'],
                        'course_category' => $category_type,
                        'start_rating' => $row_Published_Reviews['rating_score']
                    );
                } while ($row_Published_Reviews = mysqli_fetch_assoc($Published_Reviews));
            }
        }
    ?>

    <?php if (count($array_of_reviews)) { ?>
        <div class="testimonial-layout py-5">
            <div class="container">
                <div class="mb-4">
                    <p class="text-green text-bold mb-1">Testimonial</p>
                    <h3 class="text-bold">Discover the success stories of learners like yourself</h3>
                    <p>Where learning goes beyond acquiring new skills to also deliver an enjoyable experience.</p>
                </div>
                <div class="row" data-masonry='{"percentPosition": true }'>
                    <?php foreach ($array_of_reviews as $key => $review_content) { ?>
                        <div class="col-sm-6 col-lg-4 mb-4">
                            <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="profile-img-size-2 me-2">
                                                <img src="<?php echo $review_content['student_photo']; ?>" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                            </div>
                                        </div>
                                        <div>
                                            <em>
                                                <h5 class="text-bold mb-0 text-dark"><?php echo $review_content['student_name']; ?></h5>
                                                <!-- <p class="mb-0 text-muted"><?php //echo $review_content['country_name']; ?></p> -->
                                            </em>
                                        </div>
                                    </div>

                                    <p class="flex-grow-1"><?php echo nl2br(ucfirst($review_content['testimony'])); ?></p>

                                    <!-- <p class="text-bold mb-0"><?php //echo ucwords($review_content['course_category']); ?></p> -->
                                    <div class="my-2">
                                        <?php
                                            $rating_star = $review_content['star_rating'] ? $review_content['star_rating'] : 3;    
                                        ?>
                                        <?php for ($i = 0; $i < $rating_star; $i++) { ?>
                                            <i data-feather="star" class="star-fill"></i>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="testimonial-layout py-5">
            <div class="container">
                <div class="mb-4">
                    <p class="text-green text-bold mb-1">Testimonial</p>
                    <h3 class="text-bold">Discover the success stories of learners like yourself</h3>
                    <p>Where learning goes beyond acquiring new skills to also deliver an enjoyable experience.</p>
                </div>
                <div class="row" data-masonry='{"percentPosition": true }'>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-1.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Jessica M.</h5>
                                            <!-- <p class="mb-0 text-dark">United Kingdom</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">Enrolling in these short courses was a game-changer for my career. The knowledge I gained was invaluable, and it significantly enhanced my skill set.</p>

                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-2.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Alex K.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">I highly recommend these professional short courses. The content is top-notch, and the practical insights have been instrumental in my professional growth.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-3.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Emily P.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">These short courses exceeded my expectations. The instructors are experts in their fields, and the hands-on approach to learning made a real difference.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php /* ?><div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-4.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Michael S.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">The convenience of these short courses is unmatched. I could balance learning with my busy schedule, and the impact on my career has been remarkable.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-5.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Olivia W.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">The value for money in these short courses is incredible. I acquired skills that are directly applicable to my job, and I'm now more confident in my role.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-6.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Daniel H.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">Completing these short courses was a fulfilling experience. The content is comprehensive, and the flexibility made it easy to integrate into my daily routine.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-7.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Amanda L.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">I've taken several short courses on this platform, and each one has been a stepping stone in my professional journey. The quality of instruction is outstanding.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-9.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Chris B.</h5>
                                            <!-- <p class="mb-0 text-dark">Maldives</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">These short courses are a wise investment in your professional development. The instructors are engaging, and the content is tailored to industry needs.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-8 text-start border bg-light text-dark h-100 p-lg-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start text-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="profile-img-size-2 me-2">
                                            <img src="<?php echo ROOT_DIR; ?>images/avatar-8.png" class="object-fit-cover object-position-center w-100 h-100" alt="Avatar">
                                        </div>
                                    </div>
                                    <div>
                                        <em>
                                            <h5 class="text-bold mb-0 text-dark">Sarah D.</h5>
                                            <!-- <p class="mb-0 text-dark">Ghana</p> -->
                                        </em>
                                    </div>
                                </div>

                                <p class="flex-grow-1">The impact of these short courses on my career has been immediate. The skills I gained are highly sought-after, and I've seen a positive change in my job responsibilities.</p>

                                <!-- <p class="text-bold mb-0">Executive Education</p> -->
                                <div class="my-2">
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                    <i data-feather="star" class="star-fill"></i>
                                </div>
                            </div>
                        </div>

                    </div><?php */ ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>