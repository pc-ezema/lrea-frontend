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
    <div class="cart-info mt-5" data-id="<?php echo $dis_programme_id; ?>">
        <?php if (!isset($is_ad_mode) || (isset($is_ad_mode) && !$is_ad_mode)) { // Display if not ad mode ?>
            <?php if ($clients_can_enrol_for_this_course) { ?>
                <a href="<?php echo ROOT_DIR; ?>cart" class="btn btn-custom-size bg-color-3 text-white btn-custom one-click-enrol save-to-cart">
                    Enrol now - <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?>
                </a>
            <?php } ?>
            <a href="#request-info" class="btn btn-light btn-custom-size btn-custom">Request Follow-up</a>
        <?php } ?>
    </div>
<?php } ?>