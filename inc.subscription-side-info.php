<div class="card border-0 card-body border bg-light font-fam rounded text-start px-4 py-4 mb-4 mt-lg-5 sticky-top" style="top: 100px;z-index:unset;">
    <h2 class="make-bold"><?php echo $monthly_subscription_amount_plus_currency; ?>/month</h2>
    <h5 class="make-bold mb-3">Billed <?php echo $billing_duration_text; ?> at <?php echo $subscription_amount; ?></h5>

    <p class="ft-sm">We are dedicated to delivering the most cost-effective learning opportunity.</p>

    <p class="ft-sm"><?php echo SITE_NAME; ?> membership includes:</p>
    <ul class="list-group list-group-flush text-start">
        <li class="list-group-item bg-transparent d-flex justify-content-start align-items-center px-0 ft-sm">
            <div class="circle-dot mr-2 flex-shrink-0"></div>
            Access all courses within the learning programme.
        </li>

        <li class="list-group-item bg-transparent d-flex align-items-center px-0 ft-sm">
            <div class="circle-dot mr-2 flex-shrink-0"></div> Earn a certificate upon completion.
        </li>
    </ul>

    <div class="flex-grow-1 mt-4">
        <?php if (!$already_subscribed_to_this_package) { ?>
            <?php if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { ?>
                <a href="<?php echo ROOT_DIR."subscription-courses/".$coresite_page_segment_2_slug."?auth_status=confirmed&trx_action=subscribe"; ?>" class="btn btn-custom-size bg-color-3 w-100">Subscribe now</a>
            <?php } else { ?>
                <a href="<?php echo ROOT_DIR."subscription-courses/".$coresite_page_segment_2_slug."?auth_status=unknown&trx_action=authenticate"; ?>" class="btn btn-custom-size bg-color-3 w-100">Subscribe now</a>
            <?php } ?>
        <?php } ?>
    </div>
</div>