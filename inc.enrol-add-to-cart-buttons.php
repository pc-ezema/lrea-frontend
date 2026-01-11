<?php if ($da_prog_is_active && $clients_can_enrol_for_this_course) : ?>
    <div class="cart-info d-flex flex-column flex-lg-row gap-2" data-id="<?php echo $dis_programme_id; ?>">

        <!-- Enrol Now Button -->
        <div class="flex-fill">
            <a href="<?php echo ROOT_DIR; ?>cart" 
               class="btn btn-custom-color-1 btn-custom btn-custom-size text-white w-100">
               Enrol now - <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?>
            </a>
        </div>

        <?php if ($showSaveButton) : ?>
            <!-- Add to Cart Button -->
            <div class="flex-fill">
                <a href="javascript:void(0)" 
                   class="btn btn-custom-size bg-color-3 text-white w-100">
                   Add to cart
                </a>
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>