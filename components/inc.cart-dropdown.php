<?php if ($no_of_courses_in_cart == 0 ) { ?>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title text-bold" id="offcanvasCartLabel">Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <img src="<?php echo $link_root_path; ?>images/no-content.svg" class="img-fluid d-block mx-auto mb-4">
        
        <?php if (($sess_user_id > 0) && !in_array($_SESSION['user_type'], array('student', 'applicant'))) { ?>
            <p class='text-center'>You are signed in as a staff. You cannot add an item to cart.</p>
        <?php } else { ?>
            <p class='text-center'>No course in your cart.</p>
        <?php } ?>
    </div>
<?php } else { ?>
    <?php if (count($user_cart_info['programme_info'])) { ?>
        <?php 
            $cart_item_label = ($no_of_courses_in_cart > 1) ? 'Items' : 'Item';
            $student_cart_currency = get_passed_currency_symbol($user_cart_info['payment_currency']);
        ?>
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-bold" id="offcanvasCartLabel">Subtotal (<?php echo $no_of_courses_in_cart.' '.$cart_item_label; ?>): <?php echo $student_cart_currency.' '.show_amount($user_cart_info['payment_total']); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-3">
                <a href="<?php echo $link_root_path; ?>cart" class="btn-block btn btn-custom-color-1 btn-custom text-main-4 btn-custom-size text-bold">Proceed to cart (<?php echo $no_of_courses_in_cart.' '.strtolower($cart_item_label); ?>)</a>
            </div>
            <div>
                <?php foreach ($user_cart_info['programme_info'] as $key => $value) { ?>
                    <div class="card bg-light prog-row prog-card wishlist-card border-0 h-100 mb-3">
                        <div class="position-relative">
                            <div class="prog-img-4">
                                <img src="<?php echo $user_cart_info['programme_info'][$key]['thumbnail']; ?>" class="w-100 h-100 object-fit-cover object-position-center" alt="<?php echo $user_cart_info['programme_info'][$key]['prog_name']; ?>">
                            </div>
                        </div>
                        <div class="card-body prog-card-body-2 d-flex flex-column">
                            <h6 class="text-bold text-white ft-sm"><?php echo $user_cart_info['programme_info'][$key]['prog_name']; ?></h6>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-end">
                                    <h6 class="text-bold text-white mb-0 me-1" id="<?php echo $user_cart_info['programme_info'][$key]['prog_id']; ?>">
                                        <?php if ($user_cart_info['programme_info'][$key]['fee']) echo $student_cart_currency.' '.show_amount($user_cart_info['programme_info'][$key]['fee']); ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>