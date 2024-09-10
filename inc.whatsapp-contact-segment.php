<?php $cache_session_id = 'whatsapp_student_advisor'; $target_key = md5(SITE_NAME_LOWER); $expiration_date = date("Y-m-d H:i:s", strtotime('+15 minutes')); /* Don't edit beyond here */ if ($cached_value = get_entry_cached_in_memory($cache_session_id, $target_key)) { echo $cached_value; /*echo '<pre>'; var_dump(mb_strlen($cached_value)); echo '</pre>';*/ } else { ob_start(); ?>
    <?php // Check for possible staff based on country
        $found_whatsapp_staff_phone = '';

        // Get the list of all branches
        mysqli_select_db($config, $database_config);
        $query_contacts = "SELECT DISTINCT countries.countryID, countries.countryName 
                        FROM branches 
                        JOIN myip_ms_countries countries ON branches.myip_country = countries.countryID";
        $contacts = mysqli_query($config, $query_contacts) or die(mysqli_error($config));
        $row_contacts = mysqli_fetch_assoc($contacts);
        $totalRows_contacts = mysqli_num_rows($contacts);

        if ($totalRows_contacts) { 
            $whatsapp_target_country_id = '***';

            do {
                if ($row_contacts['countryName'] == $coresite_curr_country_name) {
                    $whatsapp_target_country_id = $row_contacts['countryID'];
                }
            } while ($row_contacts = mysqli_fetch_assoc($contacts));

            // Get the record of the target contact for this location
            mysqli_select_db($config, $database_config);
            $query_ContactDetails = "SELECT wc.*, u.user_id, u.first_name, u.last_name, u.email, u.socials 
                                    FROM whatsapp_contacts wc 
                                    JOIN users u ON u.user_id = wc.user_id 
                                    LEFT JOIN branches ON u.branch = branches.branch_id 
                                    LEFT JOIN myip_ms_countries countries ON branches.myip_country = countries.countryID 
                                    WHERE wc.country_id = '".sql_string($whatsapp_target_country_id)."' 
                                        AND u.deleted = '0'
                                    ORDER BY RAND()";
            $ContactDetails = mysqli_query($config, $query_ContactDetails) or die(mysqli_error($config));
            $row_ContactDetails = mysqli_fetch_assoc($ContactDetails);
            $totalRows_ContactDetails = mysqli_num_rows($ContactDetails);

            if ($totalRows_ContactDetails) {
                do {
                    // Grab the whatsapp number from the socials array
                    $staff_socials = is_serialized($row_ContactDetails['socials']) ? unserialize($row_ContactDetails['socials']) : array();

                    $whatsapp_staff_name = clean_name($row_ContactDetails['first_name'].' '.$row_ContactDetails['last_name']);
                    $whatsapp_staff_email = $row_ContactDetails['email'];
                    $whatsapp_staff_whatsapp = preg_replace('/[^0-9]/', '', $staff_socials['whatsapp']);
                    $whatsapp_staff_photo_source = $row_ContactDetails['image_source'];
                    $whatsapp_staff_available_from_weekday = ucfirst($row_ContactDetails['available_from_weekday']);
                    $whatsapp_staff_available_to_weekday = ucfirst($row_ContactDetails['available_to_weekday']);
                    $whatsapp_staff_available_from_time = $row_ContactDetails['available_from_time'];
                    $whatsapp_staff_available_to_time = $row_ContactDetails['available_to_time'];
                    $whatsapp_staff_timezone = $row_ContactDetails['timezone'];
                    $whatsapp_staff_country = ($_GET['co'] == '***') ? $row_ContactDetails['countryName'] : '';

                    // Check that the phone number is in the valid format
                    if ($staff_socials['whatsapp']) {
                        $whatsapp_image_url = ROOT_DIR.'images/banners/prog-advisor.jpg'; // Default image

                        if ($row_ContactDetails['image_source'] == 'portal') {
                            if ($row_ContactDetails['photo']) {
                                $whatsapp_image_url = ROOT_DIR_PORTAL.'uploads/staff_photos/'.$row_ContactDetails['photo'];
                            }
                        }

                        $found_whatsapp_staff_phone = true;
                        break;
                    }
                } while($row_ContactDetails = mysqli_fetch_assoc($ContactDetails));
            }
        }
    ?>
    <?php if ($found_whatsapp_staff_phone) { ?>
        <div class="staff-counsel-sect prog-courses-big-display-sect  main-thumbnail-view font-fam py-5 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-3 pb-0 bg-light">
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <div class="position-relative">
                                        <div class="text-box-container-view bg-white clip-path-overlay-text p-3">
                                            <h5 class="make-bold mb-0">Personal Counselling</h5>
                                        </div>
                                        <div class="card-img-wrap prog-advice-card-img">
                                            <img src="<?php echo $whatsapp_image_url; ?>" class="card-img-top" alt="Personal Counsellor" style="object-position: top center;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body h-100 pb-0">
                                        <h3 class="card-title">Have any questions?</h3>
                                        <p class="desc">Visit our FAQ page or get in touch with our Study Advisors. You can also find information about the application process under our Become a Student page.</p>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="media mb-4">
                                                    <div class="mr-3">
                                                        <i class="fa fa-phone fa-lg" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="mt-0 make-bold mb-1"><?php echo formatPhoneNumber($whatsapp_staff_whatsapp); ?></h6>
                                                        <p class="text-muted ft-xs mb-0">*Available <?php echo $whatsapp_staff_available_from_weekday."-".$whatsapp_staff_available_to_weekday." ".$whatsapp_staff_available_from_time." - ".$whatsapp_staff_available_to_time." ".$whatsapp_staff_timezone; ?></p>
                                                    </div>
                                                </div>

                                                <div class="media mb-4 align-items-center">
                                                    <div class="mr-3">
                                                        <i class="fa fa-whatsapp fa-lg" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="mt-0 make-bold mb-1"><a target="_blank" href="https://wa.me/<?php echo $whatsapp_staff_whatsapp; ?>" alt="Chat on Whatsapp" class="text-black"><u>Chat with our programme advisor</u></a></h6>
                                                    </div>
                                                </div>

                                                <div class="media mb-4 align-items-center">
                                                    <div class="mr-3">
                                                        <i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="mt-0 make-bold mb-0"><a href="mailto:<?php echo INFO_SUPPORT_EMAIL; ?>" alt="Send an email" class="text-black">Send us an email</a></h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="media mb-4">
                                                    <div class="mr-3">
                                                        <i class="fa fa-language fa-lg" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="mt-0 make-bold mb-1">Our programme advisors speak:</h6>
                                                        <ul class="list-unstyled d-flex country-icon-flag-img-container-ul">
                                                            <li>
                                                                <div class="country-icon-flag-img-container">
                                                                    <img src="<?php echo ROOT_DIR; ?>images/banners/united-kingdom-2.png" class="ob-fit-cover w-100 h-100" />
                                                                </div>
                                                            </li>

                                                            <li>
                                                                <div class="country-icon-flag-img-container">
                                                                    <img src="<?php echo ROOT_DIR; ?>images/banners/united-arab-emirates.png" class="ob-fit-cover w-100 h-100" />
                                                                </div>
                                                            </li>

                                                            <li>
                                                                <div class="country-icon-flag-img-container">
                                                                    <img src="<?php echo ROOT_DIR; ?>images/banners/nigeria.png" class="ob-fit-cover w-100 h-100" />
                                                                </div>
                                                            </li>

                                                            <li>
                                                                <div class="country-icon-flag-img-container">
                                                                    <img src="<?php echo ROOT_DIR; ?>images/banners/united-states-2.png" class="ob-fit-cover w-100 h-100" />
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php $cache_html = compress_page(ob_get_contents()); ob_end_clean(); add_entry_cached_in_memory($cache_session_id, $target_key, $cache_html, $expiration_date); echo $cache_html; } ?>