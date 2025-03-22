<?php 
  $link_root_default = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;
  $link_root_path = defined('ROOT_DIR_UK') ? ROOT_DIR_UK.$location_slug : ROOT_DIR.$location_slug;
  $link_portal_path = defined('ROOT_DIR_PORTAL') ? ROOT_DIR_PORTAL : ROOT_DIR;
?>

<a href="<?php echo $link_root_path; ?>cart" class="float d-flex justify-content-center align-items-center">
    <div class="position-relative">
        <div class="text-white"><i data-feather="shopping-cart"></i></div>
        <div class="in-cart-indicator bg-green cart-badge" <?php if ($no_of_courses_in_cart == 0 ) echo 'style="display: none;"'; ?>>
            <span class="count-wish count-wish-2"><?php echo $no_of_courses_in_cart; ?></span>
        </div>
    </div>
</a>

<footer class="main-footer-2 bg-black pt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <img src="<?php echo ROOT_DIR.'images/main-logo.png?v='.$fileCacheVersion; ?>" class="img-fluid" width="200" />
            </div>
        </div>

        <hr />

        <div class="row my-4">
            <div class="col-lg-3 mb-lg-0 mb-4">
                <h5 class="text-bold text-white mb-4">Topics</h5>
                <ul class="list-unstyled mb-0 footer-list ft-sm">
                    <li><a href="<?php echo $link_root_path; ?>subject/real-estate-management" class="text-decoration-none">Real Estate Management</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/business-management" class="text-decoration-none">Business Management</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/health-and-psychology" class="text-decoration-none">Health and Psychology</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/business-law" class="text-decoration-none">Business Law</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/information-technology" class="text-decoration-none">Information Technology</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/personal-development" class="text-decoration-none">Personal Development</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/teaching-and-education" class="text-decoration-none">Teaching and Education</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/health-and-safety" class="text-decoration-none">Health and Safety</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/finance-and-accounting" class="text-decoration-none">Finance and Accounting</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/office-productivity" class="text-decoration-none">Office Productivity</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/sales-and-marketing" class="text-decoration-none">Sales and Marketing</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/leadership-and-entrepreneurship" class="text-decoration-none">Leadership and Entrepreneurship</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/human-resources" class="text-decoration-none">Human Resources</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/hotel-travel-and-hospitality-management" class="text-decoration-none">Hotel, Travel and Hospitality Management</a></li>
                    <li><a href="<?php echo $link_root_path; ?>subject/logistics-and-supply-chain-management" class="text-decoration-none">Logistics and Supply Chain Management</a></li>
                </ul>
            </div>

            <div class="col-lg-3 mb-lg-0 mb-4">
                <h5 class="text-bold text-white mb-4">About</h5>
                <ul class="list-unstyled mb-0 footer-list ft-sm">
                    <li><a href="<?php echo $link_root_path; ?>faq" class="text-decoration-none">FAQ</a></li>
                    <li><a href="<?php echo $link_root_path; ?>about-lrea" class="text-decoration-none">About us</a></li>
                    <li><a href="<?php echo $link_root_path; ?>about-lrea/our-mission" class="text-decoration-none">Our Mission and Vision</a></li>
                    <li><a href="<?php echo $link_root_path; ?>what-we-offer" class="text-decoration-none">What We Offer</a></li>
                    <li><a href="<?php echo $link_root_path; ?>contact-us" class="text-decoration-none">Contact us</a></li>
                    
                    <?php $total_visible_reviews = get_total_visible_reviews(); ?>
                    <?php if ($total_visible_reviews > $least_expected_total_reviews) { ?>
                        <li><a href="<?php echo $link_root_path; ?>reviews" class="text-decoration-none">Reviews</a></li>
                    <?php } ?>

                    <li><a href="<?php echo $link_root_path; ?>subscriptions" class="text-decoration-none">Subscriptions</a></li>

                    <li><a href="<?php echo $link_root_path; ?>membership-verification" class="text-decoration-none">Membership Verification</a></li>
                </ul>
            </div>

            <div class="col-lg-3 mb-lg-0 mb-4">
                <h5 class="text-bold text-white mb-4">Legal</h5>
                <ul class="list-unstyled mb-0 footer-list ft-sm">
                    <li><a href="<?php echo $link_root_path; ?>terms" class="text-decoration-none">Terms of Service</a></li>
                    <li><a href="<?php echo $link_root_path; ?>terms/privacy-policy" class="text-decoration-none">Privacy Policy</a></li>
                    <li><a href="<?php echo $link_root_path; ?>learning-with-lrea/refund-policy" class="text-decoration-none">Refund Policy</a></li>
                    <li><a href="<?php echo $link_root_path; ?>terms/accessibility-policy" class="text-decoration-none">Acceptable User Policy</a></li>
                </ul>
            </div>

            <div class="col-lg-3 mb-lg-0 mb-4">
                <h5 class="text-bold text-white mb-4">Categories</h5>
                <ul class="list-unstyled mb-0 footer-list ft-sm">
                    <li><a href="<?php echo $link_root_path; ?>category/short-courses" class="text-decoration-none">Professional Certificate Courses</a></li>
                    <li><a href="<?php echo $link_root_path; ?>category/advanced-professional-certificate-courses" class="text-decoration-none">Advanced Professional Certificate Courses</a></li>
                    <!-- <li><a href="<?php echo $link_root_path; ?>category/masters-degrees" class="text-decoration-none">Master's Degrees</a></li> -->
                    <li><a href="<?php echo $link_root_path; ?>category/international-postgraduate-diplomas" class="text-decoration-none">International Postgraduate Diplomas</a></li>
                </ul>
            </div>
        </div>

        <div class="row align-items-center border-top">
            <div class="col-lg-6">
                <p class="ft-sm py-3 text-secondary mb-0">&copy; LREA <?php echo date('Y'); ?>. All Rights Reserved. LREA is powered by <a class="text-secondary" href="https://77rivers.com/" target="_blank">77Rivers Inc</a>.</span></p>
            </div>
            <div class="col-lg-3 offset-lg-3">
                <ul class="list-unstyled d-flex footer-social-icon align-items-center justify-content-lg-end justify-content-start mb-0" style="gap: 15px">
                    <li><a href="#"><i data-feather="facebook" style="width: 20px; height: 20px;"></i></a></li>
                    <li><a href="#"><i data-feather="linkedin" style="width: 20px; height: 20px;"></i></a></li>
                    <li><a href="#"><i data-feather="instagram" style="width: 20px; height: 20px;"></i></a></li>
                    <li><a href="#"><i data-feather="twitter" style="width: 20px; height: 20px;"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<?php 
    $end_timing = microtime(TRUE);
    echo "	<!-- Page processed in ".round($end_timing - $start_timing, 8)." secs. ".$memcache_availability." -->";

    if (isset($_SESSION['images_for_cross_domain'])) {
        echo $_SESSION['images_for_cross_domain'];
        unset($_SESSION['images_for_cross_domain']);
    }
?>