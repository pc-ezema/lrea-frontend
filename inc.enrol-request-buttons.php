<?php 
    // Don't show enrol button for contentious branches
    if (in_array($coresite_curr_state_slug, $states_to_avoid_payment)) {
        $revert_to_old_enrol_button = true;
    }

    // Don't show enrol button for some courses
    if (in_array($dis_old_prog_id, array(0))) {
        $revert_to_old_enrol_button = true;
    }

    if ($dis_programme_category == 'training') {
        $clients_can_enrol_for_this_course = true;
    }
    else {
        $clients_can_enrol_for_this_course = $site_version == 'localhost' ? 1 : does_programme_have_modules($dis_programme_id);
    }

    // Doctorates
    if (in_array($dis_programme_subcategory_id, array(405))) { 
        //$clients_can_enrol_for_this_course = false;
    }

    if (isset($is_full_width_buttons) && $is_full_width_buttons) {
        $use_column_6 = true;
    }
    else {
        $use_column_6 = false;
    }

    if (isset($is_ad_mode) && $is_ad_mode) {
        $centralise_the_button = true;
    }
?>

<?php if ($da_prog_is_active) { ?>
    <?php if (!$revert_to_old_enrol_button) { ?>
        <div class="row <?php if (!$use_column_6) echo 'mt-5 mb-5'; ?> cart-info" data-id="<?php echo $dis_programme_id; ?>">
            <?php if (!isset($is_ad_mode) || (isset($is_ad_mode) && !$is_ad_mode)) { // Display if not ad mode ?>
                <?php if ($clients_can_enrol_for_this_course) { ?>
                    <?php if ((!$displayNewlyDesignedForm || true) && !$use_column_6) { ?>
                        <div class="col-lg-2"></div>
                    <?php } ?>

                    <?php if ($showSaveButton_v2) { ?>
                        <div class="col-lg-4 text-center show-cart-button">
                            <button class="btn btn-danger btn-block btn-width-sm mb-1 save-to-cart" data-toggle="modal" data-target="#incartModalScrollable">Add to Cart</button>
                        </div>
                    <?php } else { ?>
                        <?php if ($displayNewlyDesignedForm && false) { ?>
                            <div class="col-sm-6 mb-sm-0 mb-3">
                                <button class="btn btn-danger custom-red-btn make-bold btn-block rounded btn-lg one-click-enrol save-to-cart" data-toggle="modal" data-target="#incartModalScrollable">Enrol Now</button>
                            </div>
                        <?php } else { ?>
                            <div class="<?php echo $use_column_6 ? 'col-lg-6' : 'col-lg-4'; ?> text-center">
                                <button class="btn btn-danger btn-block btn-width-sm mb-1 one-click-enrol save-to-cart" data-toggle="modal" data-target="#incartModalScrollable">Enrol Now</button>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php if ($displayNewlyDesignedForm && false) { ?>
                <div class="col-sm-6 mb-sm-0 mb-3 <?php if (!$clients_can_enrol_for_this_course) echo 'mx-auto' ?>">
                    <a href="#request-section" class="btn btn-outline-dark make-bold btn-block rounded btn-lg">Request follow-up</a>
                </div>
            <?php } else { ?>
                <div class="<?php echo $use_column_6 ? 'col-lg-6' : 'col-lg-4'; ?> <?php if (!$clients_can_enrol_for_this_course || $centralise_the_button) echo 'mx-auto' ?>">
                    <?php if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // Submit some elementary info ?>
                        <p class="text-center">
                            <a href="#request-section" class="btn btn-dark btn-block btn-width-sm mb-1">Request a follow-up</a>
                        </p>
                    <?php } else { // Jump to the form ?>
                        <p class="text-center">
                            <a href="#request-section" class="btn btn-dark btn-block btn-width-sm mb-1">Request a follow-up</a>
                        </p>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="row mt-5 mb-5">
            <div class="col-lg-4"></div>

            <div class="col-lg-4 text-center">
                <p class="text-center">
                    <a href="#" class="btn btn-dark btn-block btn-width-sm mb-1">Request a follow-up</a>
                </p>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <?php /* ?>
    <div class="row mt-5 mb-5">
        <div class="col-lg-8 mx-auto mt-3 text-center">
            <img src="<?php echo MEDIA_ROOT_DIR.$fileCacheVersion.'-'; ?>images/coming-soon.png" style="height: 150px;" alt="Coming soon" />
        </div>
    </div>
    <?php */ ?>
<?php } ?>