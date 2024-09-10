<?php // ======================== Facebook CAPI scripts =============================== ?>
  <?php // PageView Event
    if (($fb_pageview_browser_script || $fb_pageview_server_script) && $enable_facebook_capi_events_tracking) {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_pageview_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_pageview_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_pageview_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_pageview_server_script.PHP_EOL;
      }    
    } 
  ?>

  <?php // ViewContent Event
    if (($fb_view_content_browser_script || $fb_view_content_server_script) && $enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_view_content_event) {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_view_content_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_view_content_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_view_content_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_view_content_server_script.PHP_EOL;
      }
    } 
  ?>

  <?php // Lead Event
    if (($fb_lead_browser_script || $fb_lead_server_script) && $enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_lead_event) {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_lead_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_lead_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_lead_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_lead_server_script.PHP_EOL;
      }
    } 
  ?>

  <?php // Purchase Event
    if (($fb_purchase_browser_script || $fb_purchase_server_script) && $enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_purchase_event) {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_purchase_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_purchase_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_purchase_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_purchase_server_script.PHP_EOL;
      }
    } 
  ?>

  <?php // AddPaymentInfo Event
    if (($fb_add_payment_info_browser_script || $fb_add_payment_info_server_script) && $enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_add_payment_info_event) { // Event only fires on the checkout page.
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_add_payment_info_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_add_payment_info_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_add_payment_info_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_add_payment_info_server_script.PHP_EOL;
      }
    }
  ?>

  <?php // CompleteRegistration Event
    if (($fb_complete_registration_browser_script || $fb_complete_registration_server_script) && $enable_facebook_capi_events_tracking && $sess_user_id > 0 && $_SESSION['user_type'] == 'student') {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_complete_registration_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_complete_registration_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_complete_registration_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_complete_registration_server_script.PHP_EOL;
      }
    }
  ?>

  <?php // InitiateCheckout Event
    if (($fb_initiate_checkout_browser_script || $fb_initiate_checkout_server_script) && $enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_initiate_checkout_event) {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'above') echo '<!--'.$fb_initiate_checkout_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_initiate_checkout_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'above') echo $fb_initiate_checkout_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_initiate_checkout_server_script.PHP_EOL;
      }
    }
  ?>

  <?php // Search Event
    if (($fb_search_event_browser_script || $fb_search_event_server_script) && $enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_search_event) {
      if ($debug_facebook_capi_script) {
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_search_event_browser_script.'-->'.PHP_EOL;
        if ($fb_event_code_display == 'below') echo '<!--'.$fb_search_event_server_script.'-->'.PHP_EOL;
      }
      else {
        if ($fb_event_code_display == 'below') echo $fb_search_event_browser_script.PHP_EOL;
        if ($fb_event_code_display == 'below') echo $fb_search_event_server_script.PHP_EOL;
      }
    }
  ?>
<?php // ======================== End of Facebook CAPI scripts ========================= ?>