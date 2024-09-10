<style>
    .navbar .megamenu {
        padding: 1rem;
    }

    /* ============ desktop view ============ */
    @media all and (min-width: 992px) {

        .navbar .has-megamenu {
            position: static !important;
        }

        .navbar .megamenu {
            left: 0;
            right: 0;
            width: 80%;
            margin-top: 0;
            margin: 0 auto;
        }

    }

    /* ============ desktop view .end// ============ */

    /* ============ mobile view ============ */
    @media(max-width: 991px) {

        .navbar.fixed-top .navbar-collapse,
        .navbar.sticky-top .navbar-collapse {
            overflow-y: auto;
            max-height: 90vh;
            margin-top: 10px;
        }
    }

    /* ============ mobile view .end// ============ */
</style>

<?php 
    $link_root_default = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;
    $link_root_path = defined('ROOT_DIR_UK') ? ROOT_DIR_UK.$location_slug : ROOT_DIR.$location_slug;
    $link_portal_path = defined('ROOT_DIR_PORTAL') ? ROOT_DIR_PORTAL : ROOT_DIR;

    $remove_text_from_cart_icon = true;
?>

<?php 
    // Sanitize POST and GET variables before using in page content
    if (!function_exists('recursive_user_value_sanitization')) {
        function recursive_user_value_sanitization( $value ) { 
            if ( is_array( $value ) ) {
                foreach ( $value as $index => $item ) {
                    $value[ $index ] = recursive_user_value_sanitization( $item );
                }
            } elseif ( is_object( $value ) ) {
                $object_vars = get_object_vars( $value );
                foreach ( $object_vars as $property_name => $property_value ) {
                    $value->$property_name = recursive_user_value_sanitization( $property_value );
                }
            } else {
                $value = htmlentities($value, ENT_QUOTES);
            }
            
            return $value;
        }
    }

    if (count($_POST)) {
        $_POST = recursive_user_value_sanitization($_POST);
    }
    if (count($_GET)) {
        $_GET = recursive_user_value_sanitization($_GET);
    }
?>

<?php // Get all countries for dropdown
    mysqli_select_db($config, $database_config);
    $query_CtrySwitch = "SELECT mmctry.*  
                        FROM myip_ms_countries mmctry 
                        ORDER BY mmctry.countryName ASC";
    $CtrySwitch = mysqli_query($config, $query_CtrySwitch) or die(mysqli_error($config));
    $row_CtrySwitch = mysqli_fetch_assoc($CtrySwitch);
    $totalRows_CtrySwitch = mysqli_num_rows($CtrySwitch);

    $country_switch_url = $site_version == 'online' ? 'https://lrea.co.uk/' : $link_root_default;
    $switch_page_to = $coresite_page_segment_slug_combined ? '/'.$coresite_page_segment_slug_combined : '';

    $switchingFinalParams = array();

    if ($_COOKIE['a_pub_id']) {
        $switchingFinalParams[] = 'affPubId='.$_COOKIE['a_pub_id'];
    }

    if (isset($_COOKIE['s2cart'])) {
        $switchingFinalParams[] = 'crtCntntIDs='.$_COOKIE['s2cart'];
    }

    if (count($switchingFinalParams)) {
        $switch_page_to .= '?'.implode("&", $switchingFinalParams);
    }
?>

<?php // Process the cart
    if (!isset($is_ad_mode) || (isset($is_ad_mode) && !$is_ad_mode)) { // Run the functions if not ad mode
        $user_cart_info = get_user_cart_info();
        $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];

        // Process wishlist details
        $user_wishlist_info = get_user_wishlist_info();
        $no_of_courses_in_wishlist = $user_wishlist_info['no_of_courses_in_wishlist'];
    }
?>

<nav class="navbar navbar-expand-lg bg-white main-navbar border-bottom navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand apply-grow-sm" href="<?php echo $link_root_path; ?>">
            <img src="<?php echo ROOT_DIR.'images/main-logo-2.png?v='.$fileCacheVersion; ?>" class="img-fluid" width="100" />
        </a>

        <div class="nav-item ms-auto d-lg-none d-block">
            <a class="nav-link position-relative cursor-pointer" data-bs-toggle="modal" data-bs-target="#countryModal">
                <i data-feather="globe" style="height: 25px; width: 25px;" class="text-black"></i>
            </a>
        </div>
        <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i data-feather="menu" class="text-black"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav main-nav-list me-auto mb-2 mb-lg-0" style="gap: 20px;">

                <li class="nav-item">
                    <a class="nav-link" href="#menuModal" data-bs-toggle="modal">Course categories</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_root_path; ?>subscriptions">Subscriptions</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_root_path; ?>payment">Payment</a>
                </li>
            </ul>

            <div class="d-flex align-items-center bg-light text-dark rounded-8 p-1 flex-grow-1 mx-lg-4 mx-0 mb-lg-0 mb-3 pe-lg-3 border">
                <form action="<?php echo $link_root_default.$location_slug; ?>search" method="GET" class="d-flex w-100 align-items-center">
                    <div class="form-floating w-100">
                        <input type="search" name="k" class="form-control text-dark bg-light rounded-pill border-0" id="floatingSearch" placeholder="What do you want to learn?" required>
                        <label for="floatingSearch">What do you want to learn?</label>
                    </div>

                    <button class="btn btn-custom-size-2 rounded-8 btn-custom-color-1 text-main-4 btn-custom text-bold">
                        <i data-feather="search" class="text-white"></i>
                    </button>
                </form>
            </div>

            <ul class="navbar-nav main-nav-list me-auto mb-2 mb-lg-0" style="gap: 20px;">
                <li class="nav-item d-lg-block d-none">
                    <a class="nav-link position-relative cursor-pointer text-black" data-bs-toggle="modal" data-bs-target="#countryModal">
                        <i data-feather="globe" style="height: 25px; width: 25px;"></i>
                    </a>
                </li>
            </ul>

            <div class="d-flex" style="gap: 20px;">
                <?php if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in ?>
                    <a class="btn btn-custom-size-2 bg-green text-white btn-custom text-bold w-sm-100" href="<?php echo $link_portal_path; ?>">My profile</a>
                <?php } elseif (!isset($hide_signin_option) || (isset($hide_signin_option) && !$hide_signin_option)) { ?>
                    <a class="btn btn-custom-size-2 text-dark btn-custom text-bold w-sm-100" href="<?php echo $link_portal_path."user/access".$url_extend; ?>">Log in</a>
                    <a class="btn btn-custom-size-2 bg-green text-white btn-custom text-bold w-sm-100" href="<?php echo $link_portal_path."user/register".$url_extend; ?>">Apply now</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<!-- Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header border-bottom-0">
                <h1 class="modal-title fs-5" id="menuModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row">
                    <div class="col-lg-4">
                        <ul class="list-unstyled menu-list-item ">
                            <h4 class="text-bold mb-4">Subject</h4>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/real-estate-management">Real Estate Management <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/business-management">Business Management <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/health-and-psychology">Health and Psychology <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/business-law">Business Law <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/information-technology">Information Technology <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/personal-development">Personal Development <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/teaching-and-education">Teaching and Education <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/health-and-safety">Health and Safety <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/finance-and-accounting">Finance and Accounting <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/office-productivity">Office Productivity <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/sales-and-marketing">Sales and Marketing <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/leadership-and-entrepreneurship">Leadership and Entrepreneurship <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/human-resources">Human Resources <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/hotel-travel-and-hospitality-management">Hotel, Travel and Hospitality Management <i data-feather="chevron-right"></i></a></li>
                            <li><a class="menu-item" href="<?php echo $link_root_path; ?>subject/logistics-and-supply-chain-management">Logistics and Supply Chain Management <i data-feather="chevron-right"></i></a></li>
                        </ul>
                    </div>

                    <div class="col-lg-8">

                        <div class="px-lg-4">
                            <div class="row">

                                <div class="col-lg-6">
                                    <h5 class="text-bold ft-normal-2">Short courses</h5>

                                    <ul class="list-unstyled main-courses-box">
                                        <li>
                                            <a href="<?php echo $link_root_path; ?>category/short-courses" class="text-decoration-none p-4 d-block opacity-card">
                                                <h5 class="text-bold">Professional Certificate Courses</h5>
                                                <p class="mb-3">The professional certificate courses are short CPD courses.</p>
                                                <p class="mb-0 text-bold text-green">View courses <i data-feather="chevron-right"></i></p>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="<?php echo $link_root_path; ?>category/advanced-professional-certificate-courses" class="text-decoration-none p-4 d-block opacity-card">
                                                <h5 class="text-bold">Advanced Professional Certificate Courses</h5>
                                                <p class="mb-3">The advanced professional certificate courses are short CPD courses.</p>
                                                <p class="mb-0 text-bold text-green">View courses <i data-feather="chevron-right"></i></p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-lg-6">
                                    <h6 class="text-bold ft-normal-2">Postgraduate Programmes</h6>

                                    <ul class="list-unstyled main-courses-box">
                                        <!-- <li>
                                            <a href="<?php echo ROOT_DIR; ?>category/masters-degrees" class="text-decoration-none p-4 d-block opacity-card">
                                                <h5 class="text-bold">Master's Degree</h5>
                                                <p class="mb-3">These programmes are 180 credit programme. Level 7 + Top up programme awarded by partner university.</p>
                                                <p class="mb-0 text-bold text-green">View courses <i data-feather="chevron-right"></i></p>
                                            </a>
                                        </li> -->
                                        <li>
                                            <a href="<?php echo ROOT_DIR; ?>category/international-postgraduate-diplomas" class="text-decoration-none p-4 d-block opacity-card">
                                                <h5 class="text-bold">International Postgraduate Diploma</h5>
                                                <p class="mb-3">These programmes are 120 credit and available in various subjects.</p>
                                                <p class="mb-0 text-bold text-green">View courses <i data-feather="chevron-right"></i></p>
                                            </a>
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

<div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="countryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-8 bg-white">
            <div class="modal-header border-0">
                <h1 class="modal-title text-bold text-dark fs-5" id="countryModalLabel">Choose country</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="country-list-container text-dark">
                    <div class="row">
                        <?php if ($coresite_curr_country_name) { ?>
                            <div class="col-lg-12">
                                <p>Selected country: <strong><em><?php echo $coresite_curr_country_name; ?></em></strong></p>
                            </div>
                        <?php } ?>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <ul class="list-unstyled col-count-country-2 country-list-view">
                                    <?php do { ?> 
                                        <li><a href="<?php echo $country_switch_url.$row_CtrySwitch['url_name'].$switch_page_to; ?>" class="text-decoration-none"><?php echo $row_CtrySwitch['countryName']; ?></a></li>
                                    <?php } while($row_CtrySwitch = mysqli_fetch_assoc($CtrySwitch)); ?> 
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>