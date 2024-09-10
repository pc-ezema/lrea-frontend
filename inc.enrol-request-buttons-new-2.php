<?php 
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
?>

<?php if ($da_prog_is_active) { ?>
    <div class="row cart-info" data-id="<?php echo $dis_programme_id; ?>">
        <?php if (!isset($is_ad_mode) || (isset($is_ad_mode) && !$is_ad_mode)) { // Display if not ad mode ?>
            <?php if ($clients_can_enrol_for_this_course) { ?>
                <div class="col-lg-6">
                    <div>
                        <a href="<?php echo ROOT_DIR; ?>cart" class="btn btn-custom-color-1 btn-custom btn-custom-size text-white mb-lg-0 mb-2 w-100 one-click-enrol save-to-cart">
                            Enrol now - <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
            <div class="col-lg-6">
                <div>
                    <a href="#request-info" class="btn btn-custom-size border btn-outline-<?php echo (isset($request_follow_up_button_outline_dark) && $request_follow_up_button_outline_dark) ? 'dark' : 'light'; ?> w-100">Request Follow-up</a>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php $request_follow_up_button_outline_dark = false; ?>
<?php } ?>