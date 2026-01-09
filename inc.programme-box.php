<?php
if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL . 'inc.programme-variables.php')) {
    require(ROOT_PATH_TO_PORTAL . 'inc.programme-variables.php');
} else {
    require('inc.programme-variables.php');
}

if (defined('ROOT_DIR_UK')) {
    $link_root_default = ROOT_DIR_UK;
    $link_root_path = ROOT_DIR_UK . $location_slug;
} else {
    $link_root_default = ROOT_DIR;
    $link_root_path = ROOT_DIR . $location_slug;
}

$show_find_more_button = false;

$this_box_url = $link_root_path . $dis_programme_a_href;
?>

<?php // Prepare the wishlist icon to use.
$wishlist_icon = "fa-heart-o";

if (is_array($user_wishlist_info['programme_info']) && count($user_wishlist_info['programme_info'])) {
    foreach ($user_wishlist_info['programme_info'] as $key => $value) {
        if ($user_wishlist_info['programme_info'][$key]['prog_id'] == $dis_programme_id) {
            $wishlist_icon = "fa-heart text-danger";
            break;
        }
    }
}

$show_new_add_to_cart_button = false;

if ($display_new_add_to_cart_button && in_array($dis_programme_subcategory_id, array(883, 409))) { // Only show the add to cart button on PCC and APCC
    $show_new_add_to_cart_button = true;
    $has_been_added_to_cart = false;

    if (count($user_cart_info['programme_info'])) {
        foreach ($user_cart_info['programme_info'] as $key => $value) {
            if ($user_cart_info['programme_info'][$key]['prog_id'] == $dis_programme_id) {
                $has_been_added_to_cart = true;
                break;
            }
        }
    }
}
?>

<?php if (!$not_linked_box) { ?>
    <a href="<?php echo $this_box_url; ?>" <?php if ($open_prog_in_new_tab) echo 'target="_blank"'; ?> class="text-decoration-none text-black">
    <?php } ?>
    <div class="card course-new-card position-relative bg-white border rounded-8 h-100 shadow-sm">
        <!-- Course Image with duration badge -->
        <div class="position-relative">
            <img src="<?php echo $dis_programme_thumbnail; ?>"
                class="card-img-top img-fluid object-fit-cover object-position-center"
                alt="<?php echo $dis_programme_name; ?>">
        </div>

        <!-- Card Body -->
        <div class="card-body d-flex flex-column p-3">
            <!-- Title -->
            <h5 class="card-title text-dark fw-bold mb-2">
                <?php echo $dis_programme_name; ?>
            </h5>

            <!-- Excerpt -->
            <p class="card-text text-muted small mb-3 line-clamp-3">
                <?php echo $dis_programme_excerpt; ?>
            </p>

            <!-- Rating (static for now, can be dynamic if rating stored) -->
            <div class="d-flex align-items-center mb-3">
                <span class="me-1 text-warning">&#9733;</span>
                <span class="me-1 text-warning">&#9733;</span>
                <span class="me-1 text-warning">&#9733;</span>
                <span class="me-1 text-warning">&#9733;</span>
                <span class="me-2 text-muted">&#9733;</span>
                <small class="text-muted">(832)</small>
            </div>

            <!-- Footer (Enroll button + price) -->
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <a href="<?php echo $this_box_url; ?>" class="btn btn-sm btn-danger">Course Details</a>

                <?php if ($dis_programme_fee) { // Display original fee if programme is not a free course. ?>                        
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-end">
                            <h5 class="text-bold text-white mb-0 me-1" style="color: var(--green) !important">
                                <?php if ($show_monthly_fee_on_prog_boxes && $cosmetics_dis_programme_fee) { // Show the monthly fee for programmes that the cosmetics fee is enabled for. ?>
                                    <?php // Instalment fee
                                        if ($dis_country_programme_max_instalments) { // Use the max number of instalments value that has been set for the country if there's one.
                                            $instalmental_fee_percentage_increase = get_increment_percentage($dis_country_programme_max_instalments);
                                            $estimated_monthly_fee = round(($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))/$dis_country_programme_max_instalments, 2);
                                        }
                                        elseif ($dis_programme_max_instalments > 1) { // Using the max number of instalments value
                                            $instalmental_fee_percentage_increase = get_increment_percentage($dis_programme_max_instalments);
                                            $estimated_monthly_fee = round(($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))/$dis_programme_max_instalments, 2);
                                        }
                                        else {
                                            $estimated_monthly_fee = highest_instalment_amount($dis_programme_instalment_duration, $dis_programme_fee); 
                                        }
                                    ?>

                                    <?php if ($estimated_monthly_fee) echo $symbol_for_programme_currency.' '.show_amount($estimated_monthly_fee); ?><span class="desc">/mo</span>
                                <?php } else { ?>
                                    <?php if ($cosmetics_dis_programme_fee) { ?>
                                        <?php if ($cosmetics_dis_programme_fee) echo $symbol_for_programme_currency.' '.show_amount($cosmetics_dis_programme_fee); ?>
                                    <?php } else { ?>
                                        <?php if ($dis_programme_fee) echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); elseif ($enable_free_courses && !$dis_programme_fee) echo 'Free'; ?>
                                    <?php } ?>
                                <?php } ?>
                            </h5>
                        </div>
                        
                        <?php if ($dis_programme_fee) { // Display original fee if programme is not a free course. ?>                        
                            <div class="d-flex justify-content-between align-items-end">
                                <strike class="text-white">
                                    <h6 class="ft-md text-white mb-0 me-1" style="color: var(--green) !important">
                                        <?php if ($show_monthly_fee_on_prog_boxes && $cosmetics_dis_programme_fee && $da_prog_has_instalment) { // Show the cosmetics programme fee here for programmes that cosmetics fee is enabled for. ?>
                                            <?php if ($cosmetics_dis_programme_fee) echo $symbol_for_programme_currency.' '.show_amount($cosmetics_dis_programme_fee); ?>
                                        <?php } else { ?>
                                            <?php if ($cosmetics_dis_programme_original_fee) { ?> 
                                                <?php echo $symbol_for_programme_currency.' '.show_amount($cosmetics_dis_programme_original_fee); ?>
                                            <?php } else { ?>
                                                <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_original_fee); ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </h6>
                                </strike>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if (!$not_linked_box) { ?>
    </a>
<?php } ?>