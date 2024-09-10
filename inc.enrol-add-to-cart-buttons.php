<?php if ($da_prog_is_active && $clients_can_enrol_for_this_course) { ?>
    <div class="row cart-info" data-id="<?php echo $dis_programme_id; ?>">
        <div class="col-lg-6 mx-auto">
            <div>
                <a href="<?php echo ROOT_DIR; ?>cart" class="btn btn-custom-color-1 btn-custom btn-custom-size text-white mb-lg-0 mb-2 w-100 one-click-enrol save-to-cart">Enrol now - <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?></a>
            </div>
        </div>
            
        <?php if ($showSaveButton) { ?>
            <div class="col-lg-6">
                <div class="show-cart-button">
                    <a href="javascript:void(0)" class="btn btn-custom-size bg-color-3 text-white w-100 save-to-cart">Add to cart</a>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>