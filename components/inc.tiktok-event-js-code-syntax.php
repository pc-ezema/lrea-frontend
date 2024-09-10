<?php // ======================== TikTok API Event scripts =============================== ?>
  <?php // ViewContent Event
    if ($tiktok_view_content_event && $enable_tiktok_events_tracking && $is_allowed_to_track_tiktok_view_content_event) {
      if ($debug_tiktok_script) {
        echo '<!--'.$tiktok_view_content_event.'-->'.PHP_EOL;
      }
      else {
        echo $tiktok_view_content_event.PHP_EOL;
      }
    } 
  ?>

  <?php // InitiateCheckout Event
    if ($tiktok_initiate_checkout_event && $enable_tiktok_events_tracking && $is_allowed_to_track_tiktok_initiate_checkout_event) {
      if ($debug_tiktok_script) {
        echo '<!--'.$tiktok_initiate_checkout_event.'-->'.PHP_EOL;
      }
      else {
        echo $tiktok_initiate_checkout_event.PHP_EOL;
      }
    }
  ?>

  <?php // AddPaymentInfo Event
    if ($tiktok_add_payment_info_event && $enable_tiktok_events_tracking && $is_allowed_to_track_tiktok_add_payment_info_event) {
      if ($debug_tiktok_script) {
        echo '<!--'.$tiktok_add_payment_info_event.'-->'.PHP_EOL;
      }
      else {
        echo $tiktok_add_payment_info_event.PHP_EOL;
      }
    }
  ?>

  <?php // CompleteRegistration Event
    if ($tiktok_complete_registration_event && $enable_tiktok_events_tracking && ($sess_user_id > 0) && ($_SESSION['user_type'] == 'student')) {
      if ($debug_tiktok_script) {
        echo '<!--'.$tiktok_complete_registration_event.'-->'.PHP_EOL;
      }
      else {
        echo $tiktok_complete_registration_event.PHP_EOL;
      }
    }
  ?>

  <?php // SubmitForm Event
    if ($tiktok_submit_form_event && $enable_tiktok_events_tracking && $is_allowed_to_track_tiktok_submit_form_event) {
      if ($debug_tiktok_script) {
        echo '<!--'.$tiktok_submit_form_event.'-->'.PHP_EOL;
      }
      else {
        echo $tiktok_submit_form_event.PHP_EOL;
      }
    }
  ?>

  <?php // CompletePayment Event
    if ($tiktok_complete_payment_event && $enable_tiktok_events_tracking && $is_allowed_to_track_tiktok_complete_payment_event) {
      if ($debug_tiktok_script) {
        echo '<!--'.$tiktok_complete_payment_event.'-->'.PHP_EOL;
      }
      else {
        echo $tiktok_complete_payment_event.PHP_EOL;
      }
    }
  ?>
<?php // ======================== End of TikTok API Event scripts ========================= ?>