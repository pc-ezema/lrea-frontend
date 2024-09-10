<?php if ($no_of_courses_in_wishlist == 0) { ?>
    <?php if (($sess_user_id > 0) && !in_array($_SESSION['user_type'], array('student', 'applicant'))) { ?>
        <p class='text-center'>You are signed in as a staff. You cannot add an item to wishlist.</p>
    <?php } else { ?>
        <p class='text-center'>Your wishlist is empty</p>
    <?php } ?>
<?php } else { ?>
    <?php if (count($user_wishlist_info['programme_info'])) { ?>
        <div class="in-cart-view-container-sect in-cart-view-list-item overflow-auto minimized-height">
            <?php foreach ($user_wishlist_info['programme_info'] as $wishlist_key => $wishlist_value) { $student_wishlist_currency = get_passed_currency_symbol($user_wishlist_info['payment_currency']); ?>    
                <div class="in-cart-view-sect mb-2 cart-info" data-id="<?php echo $user_wishlist_info['programme_info'][$wishlist_key]['prog_id']; ?>">
                    <div class="row align-items-center">
                        <div class="col-lg-12 mb-3">
                            <div class="media mb-2">
                                <div class="in-cart-image-view-container mr-3">
                                    <img src="<?php echo $user_wishlist_info['programme_info'][$wishlist_key]['thumbnail']; ?>" class="card-image-set" />
                                </div>
                                <div class="media-body">
                                    <h6 class="mt-0 make-bold in-cart-text-2">
                                        <a href="<?php echo $link_root_path.$user_wishlist_info['programme_info'][$wishlist_key]['prog_url']?>" target="_blank" class="text-dark"><?php echo $user_wishlist_info['programme_info'][$wishlist_key]['prog_name']; ?></a>
                                    </h6>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-9">
                                    <button class="btn btn-block btn-info btn-block btn-bord-radius btn-sm make-bold save-to-cart move-wishlist-to-cart">Add to cart</button>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-outline-danger btn-bord-radius btn-block btn-sm remove-dropdown-wishlist" id="<?php echo $user_wishlist_info['programme_info'][$wishlist_key]['prog_id']; ?>" title="Remove from wishlist"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <hr/>

    <div class="in-cart-amount-container p-3">
        <a href="<?php echo $link_root_path; ?>wishlist" class="btn btn-purple btn-bord-radius btn-block make-bold">Go to wishlist</a>
    </div>
<?php } ?>