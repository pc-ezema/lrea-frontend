<?php if ($clients_can_enrol_for_this_course && $da_prog_is_active) { ?>
    <?php if (!isset($is_ad_mode) || (isset($is_ad_mode) && !$is_ad_mode)) { // Display if not ad mode ?>
        <?php if ($programme_covered_by_subscription) { ?>
            <div class="col-lg-4 mx-auto mt-3">
                <a href="<?php echo $course_ecomm_link_action_url; ?>" class="btn btn-outline-dark mb-2 btn-block"><?php echo $course_ecomm_link_action_text; ?></a>
            </div>
        <?php } else { ?>
            <?php if ($showSaveButton) { ?>
                <div class="col-lg-<?php if ($use_new_website_look) echo 6; else echo 8; ?> mx-auto">
                    <div class="action-buttons-dual-btn">
                        <div class="row cart-info" data-id="<?php echo $dis_programme_id; ?>">
                            <div class="col-lg-6 mt-3 move-to-center">
                                <button class="btn btn-dark mb-2 btn-block one-click-enrol save-to-cart" data-toggle="modal" data-target="#incartModalScrollable">Enrol Now</button>
                            </div>
                            <div class="col-lg-6 mt-3 show-cart-button">
                                <button class="btn btn-outline-danger mb-2 btn-block save-to-cart" data-toggle="modal" data-target="#incartModalScrollable">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-lg-4 mx-auto mt-3">
                    <button class="btn btn-dark mb-2 btn-block one-click-enrol save-to-cart" data-toggle="modal" data-target="#incartModalScrollable">Enrol Now</button>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <?php /* ?>
    <div class="col-lg-8 mx-auto mt-3 text-center">
        <img src="<?php echo MEDIA_ROOT_DIR.$fileCacheVersion.'-'; ?>images/coming-soon.png" style="height: 150px;" alt="Coming soon" />
    </div>
    <?php */ ?>
<?php } ?>