<?php

require_once('./connections/config.php');
require_once('./components/misc.inc.php');
require_once('./components/doctype.inc.php');

// Set currency if passed via AJAX
if (isset($_GET['currency'])) {
    $_SESSION['coresite_curr_currency'] = $_GET['currency'];
}

// Ensure the session variable exists
$coresite_curr_currency = $_SESSION['coresite_curr_currency'] ?? PREFERRED_CURRENCY;
$coresite_curr_country_slug = $_SESSION['coresite_curr_country_slug'] ?? null;

include_once('./components/end-of-head.inc.php');

include_once('./components/beginning-of-body.inc.php');

include_once('./components/end-of-body.inc.php');

// Include the mini-cart HTML
require_once('./inc.mini-cart.php');
