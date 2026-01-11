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
$link_root_path = defined('ROOT_DIR_UK') ? ROOT_DIR_UK . $location_slug : ROOT_DIR . $location_slug;
$link_portal_path = defined('ROOT_DIR_PORTAL') ? ROOT_DIR_PORTAL : ROOT_DIR;

$remove_text_from_cart_icon = true;
?>

<?php
// Sanitize POST and GET variables before using in page content
if (!function_exists('recursive_user_value_sanitization')) {
    function recursive_user_value_sanitization($value)
    {
        if (is_array($value)) {
            foreach ($value as $index => $item) {
                $value[$index] = recursive_user_value_sanitization($item);
            }
        } elseif (is_object($value)) {
            $object_vars = get_object_vars($value);
            foreach ($object_vars as $property_name => $property_value) {
                $value->$property_name = recursive_user_value_sanitization($property_value);
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
$switch_page_to = $coresite_page_segment_slug_combined ? '/' . $coresite_page_segment_slug_combined : '';

$switchingFinalParams = array();

if ($_COOKIE['a_pub_id']) {
    $switchingFinalParams[] = 'affPubId=' . $_COOKIE['a_pub_id'];
}

if (isset($_COOKIE['s2cart'])) {
    $switchingFinalParams[] = 'crtCntntIDs=' . $_COOKIE['s2cart'];
}

if (count($switchingFinalParams)) {
    $switch_page_to .= '?' . implode("&", $switchingFinalParams);
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

<style>
    .menu-item {
        position: relative;
    }

    .menu-item a {
        color: #000;
        padding: 15px;
        text-decoration: none;
        font-weight: bold;
    }

    .menu-item:hover .mega-menu {
        display: flex;
    }

    .mega-menu {
        display: flex;
        padding: 30px;
        z-index: 1000;
    }


    .mega-menu h4 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #1d1d1d;
        font-weight: bold;
        height: 15%;
    }

    .mega-menu p {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }

    .menu-link {
        display: inline-block;
        margin-top: 15px;
        color: #1d1d1d;
        font-size: 14px;
        text-decoration: none;
        font-weight: bold;
    }

    .menu-link:hover {
        text-decoration: underline;
    }

    .mega-menu h4,
    .mega-menu p,
    .menu-link {
        transition: color 0.3s ease;
    }

    .mega-menu h4:hover,
    .mega-menu p:hover,
    .menu-link:hover {
        color: #c10000;
        /* Matching hover color */
    }

    .cta-btn {
        background-color: #ea383e;
        color: white;
        text-align: center;
        padding: 12px 30px;
        border-radius: 30px;
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
    }

    .cta-btn:hover {
        background-color: #000;
        text-decoration: none;
        color: white;
    }
</style>

<nav class="navbar navbar-expand-lg bg-white main-navbar border-bottom navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand apply-grow-sm" href="<?php echo $link_root_path; ?>">
            <img src="<?php echo ROOT_DIR . 'images/main-logo-2.png?v=' . $fileCacheVersion; ?>" class="img-fluid" width="100" />
        </a>

        <div class="d-lg-none d-flex justify-content-end gap-3">
            <!-- Globe + Cart Wrapper -->
            <div class="d-flex gap-3 me-3">
                <!-- Globe Icon -->
                <a class="nav-link p-0 position-relative cursor-pointer" data-bs-toggle="modal" data-bs-target="#countryModal">
                    <i data-feather="globe" style="height:25px;width:25px;" class="text-black"></i>
                </a>

                <!-- Cart Icon with Badge and Dropdown -->
                <div class="dropdown" id="miniCartMobile">
                    <a class="nav-link p-0 position-relative cursor-pointer text-black" id="miniCartDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">

                        <i data-feather="shopping-cart" style="height:25px;width:25px;"></i>

                        <!-- Cart Badge -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge count-wish"
                            style="<?php echo ($no_of_courses_in_cart > 0) ? '' : 'display:none;'; ?>">
                            <?php echo (int)$no_of_courses_in_cart; ?>
                        </span>
                    </a>

                    <!-- Mobile Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end p-0 mobileMiniCart" aria-labelledby="miniCartDropdown" style="min-width: 0!important;">
                        <li>
                            <div class="mini-cart-wrapper" >
                                <?php include('inc.mini-cart.php'); ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Hamburger Button -->
            <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i data-feather="menu" class="text-black"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav main-nav-list align-items-center me-auto mb-2 mb-lg-0 gap-lg-4 gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="#menuModal" data-bs-toggle="modal">Course categories</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#membershipModal" data-bs-toggle="modal">Membership</a>
                </li> -->

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_root_path; ?>subscriptions">Subscriptions</a>
                </li>

                <!-- Search Bar -->
                <li class="nav-item flex-grow-1 mx-lg-4 mx-0 mb-lg-0 mb-3">
                    <div class="d-flex align-items-center bg-light text-dark rounded-8 p-1 border">

                        <form action="<?php echo $link_root_default . $location_slug; ?>search"
                            method="GET"
                            class="d-flex w-100 align-items-center">

                            <div class="form-floating w-100">
                                <input type="search"
                                    name="k"
                                    class="form-control text-dark bg-light rounded-pill border-0"
                                    id="floatingSearch"
                                    placeholder="What do you want to learn?"
                                    required>
                                <label for="floatingSearch">What do you want to learn?</label>
                            </div>

                            <button class="btn btn-custom-size-2 rounded-8 btn-custom-color-1 text-main-4 btn-custom text-bold ms-2">
                                <i data-feather="search" class="text-white"></i>
                            </button>

                        </form>
                    </div>
                </li>
                
                <!-- <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_root_path; ?>payment">Payment</a>
                </li> -->
            </ul>

            <div class="d-flex" style="gap: 20px;">
                <ul class="navbar-nav main-nav-list me-auto mb-2 mb-lg-0" style="gap: 20px;">
                    <li class="nav-item d-lg-block d-none">
                        <a class="nav-link position-relative cursor-pointer text-black" data-bs-toggle="modal" data-bs-target="#countryModal">
                            <i data-feather="globe" style="height: 25px; width: 25px;"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav main-nav-list cart-hover-wrapper me-auto mb-2 mb-lg-0" style="gap: 20px;">
                    <li class="nav-item d-lg-block d-none">
                        <a class="nav-link position-relative cursor-pointer text-black">

                            <i data-feather="shopping-cart" style="height: 25px; width: 25px;"></i>

                            <!-- Cart Badge (ALWAYS EXISTS) -->
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge count-wish"
                                style="<?php echo ($no_of_courses_in_cart > 0) ? '' : 'display:none;'; ?>">
                                <?php echo (int) $no_of_courses_in_cart; ?>
                            </span>

                        </a>

                        <!-- Desktop / Full Width -->
                        <div class="mini-cart-dropdown" id="desktopMiniCart">
                            <div class="row cart-view-sect mini-cart-wrapper" id="miniCartDesktop">
                                <?php include('inc.mini-cart.php'); ?>
                            </div>
                        </div>
                    </li>
                </ul>

                <?php if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in 
                ?>
                    <!-- Logged in -->
                    <a class="btn btn-custom-size-2 bg-green text-white btn-custom text-bold w-sm-100" 
                        href="<?php echo $link_portal_path; ?>">
                        My profile
                    </a>
                    <a class="btn btn-custom-size-2 bg-green text-white btn-custom text-bold w-sm-100" 
                        href="<?php echo $link_root_path; ?>logout">
                        Logout
                    </a>
                <?php } elseif (!isset($hide_signin_option) || (isset($hide_signin_option) && !$hide_signin_option)) { ?>
                    <a class="btn btn-custom-size-2 text-dark btn-custom text-bold w-sm-100" href="<?php echo $link_portal_path . "user/access" . $url_extend; ?>">Log in</a>
                    <a class="btn btn-custom-size-2 bg-green text-white btn-custom text-bold w-sm-100" href="<?php echo $link_portal_path . "user/register" . $url_extend; ?>">Sign up</a>
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
                                        <li><a href="<?php echo $country_switch_url . $row_CtrySwitch['url_name'] . $switch_page_to; ?>" class="text-decoration-none"><?php echo $row_CtrySwitch['countryName']; ?></a></li>
                                    <?php } while ($row_CtrySwitch = mysqli_fetch_assoc($CtrySwitch)); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="membershipModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header border-bottom-0">
                <h1 class="modal-title fs-5" id="menuModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mega-menu row">
                    <div class="col mb-5">
                        <h4><a href="<?php echo $link_root_path; ?>landing" style="color: #c10000; text-decoration: none;">Why Join London Real Estate Academy?</a></h4>
                        <hr>
                        <p>Becoming a member of LREA gives you exclusive access to expert insights, industry resources, and tailored support to accelerate your career. Elevate your skills, expand your network, and stay ahead in the real estate industry</p>
                        <p>LREA Membership Tiers – Unlock Exclusive Opportunities</p>
                    </div>
                    <div class="col mb-5">
                        <h4><a href="<?php echo $link_root_path; ?>landing" style="color: #c10000; text-decoration: none;">Top Management Membership</a></h4>
                        <hr>
                        <p>For visionary leaders ready to shape the future of real estate. Gain access to bespoke services, strategic insights, and an elite network of industry pioneers. Elevate your decision-making with expert guidance and innovative solutions tailored for top executives.</p>
                    </div>
                    <div class="col mb-5">
                        <h4><a href="<?php echo $link_root_path; ?>landing" style="color: #c10000; text-decoration: none;">Professional Membership</a></h4>
                        <hr>
                        <p>Designed for both emerging leaders and seasoned professionals, this membership keeps you informed, engaged, and connected to a thriving real estate network. Stay ahead with exclusive resources, expert-led discussions, and career-enhancing opportunities.</p>
                    </div>
                    <div class="col mb-5">
                        <h4><a href="<?php echo $link_root_path; ?>landing" style="color: #c10000; text-decoration: none;">Student Membership</a></h4>
                        <hr>
                        <p>Bridge the gap between learning and leadership. Connect with industry experts, gain invaluable insights, and build essential skills for a successful real estate career.</p>
                    </div>
                    <div class="col mb-5">
                        <h4><a href="<?php echo $link_root_path; ?>landing" style="color: #c10000; text-decoration: none;">Join Now</a></h4>
                        <hr>
                        <p>Sign up today and gain access to exclusive resources, industry connections, and tailored support designed to fuel your ambitions in real estate. CTA: Join us today</p>
                        <a href="<?php echo $link_root_path; ?>payment" class="cta-btn">Become a Member</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
