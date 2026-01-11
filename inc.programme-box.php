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
            <div class="course-footer d-flex justify-content-between align-items-center mt-auto">
                <!-- Course Details Button -->
                <a href="<?php echo $this_box_url; ?>"
                    class="btn btn-sm btn-danger px-3 fw-semibold shadow-sm flex-grow-0">
                    Course Details
                </a>

                <?php if ($dis_programme_fee) : ?>
                    <?php
                    /* -----------------------------
                        | Resolve fees safely
                    ----------------------------- */
                    $current_fee  = $cosmetics_dis_programme_fee ?: $dis_programme_fee;
                    $original_fee = $cosmetics_dis_programme_original_fee ?: $dis_programme_original_fee;
                    ?>

                    <div class="price-wrapper d-flex flex-column align-items-end text-end flex-grow-0">

                        <!-- Current / Monthly Price -->
                        <div class="current-price d-flex align-items-baseline gap-1">
                            <span class="currency-symbol"><?php echo $symbol_for_programme_currency; ?></span>
                            <span class="price-value">
                                <?php
                                if ($show_monthly_fee_on_prog_boxes && $cosmetics_dis_programme_fee) {

                                    if ($dis_country_programme_max_instalments) {
                                        $instalmental_fee_percentage_increase = get_increment_percentage($dis_country_programme_max_instalments);
                                        $estimated_monthly_fee = round(
                                            ($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))
                                                / $dis_country_programme_max_instalments,
                                            2
                                        );
                                    } elseif ($dis_programme_max_instalments > 1) {
                                        $instalmental_fee_percentage_increase = get_increment_percentage($dis_programme_max_instalments);
                                        $estimated_monthly_fee = round(
                                            ($dis_programme_fee + ($dis_programme_fee * $instalmental_fee_percentage_increase))
                                                / $dis_programme_max_instalments,
                                            2
                                        );
                                    } else {
                                        $estimated_monthly_fee = highest_instalment_amount(
                                            $dis_programme_instalment_duration,
                                            $dis_programme_fee
                                        );
                                    }

                                    echo show_amount($estimated_monthly_fee);
                                } else {
                                    echo show_amount($current_fee);
                                }
                                ?>
                            </span>

                            <?php if ($show_monthly_fee_on_prog_boxes && $cosmetics_dis_programme_fee) : ?>
                                <span class="price-period">/mo</span>
                            <?php endif; ?>
                        </div>

                        <!-- Original (Strike) Price -->
                        <?php if ($original_fee && $current_fee && $original_fee > $current_fee) : ?>
                            <div class="original-price mt-1">
                                <del><?php echo $symbol_for_programme_currency . ' ' . show_amount($original_fee); ?></del>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php if (!$not_linked_box) { ?>
    </a>
<?php } ?>