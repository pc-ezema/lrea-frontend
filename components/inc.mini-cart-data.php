<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$cart_programmes = [];
$total_cart_fee = 0;
$subtotal_cart_fee = 0; // initialize subtotal
$user_signed_in = false;
$retrieved_Cart_Info = [];
$current_fallback_image_id = 0;

// Determine if user is logged in as student
if (isset($sess_user_id) && $sess_user_id > 0 && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student') {
    $user_signed_in = true;

    // Fetch cart items from database
    mysqli_select_db($config, $database_config);
    $query_Cart_Info = "SELECT prog_id FROM saved_to_cart WHERE login_id = " . intval($sess_user_id);
    $Cart_Info = mysqli_query($config, $query_Cart_Info) or die(mysqli_error($config));

    while ($row = mysqli_fetch_assoc($Cart_Info)) {
        $retrieved_Cart_Info[] = intval($row['prog_id']);
    }
}
// If not logged in, check cookie
elseif (!empty($_COOKIE['s2cart'])) {
    $cookie_cart = explode(",", $_COOKIE['s2cart']);
    foreach ($cookie_cart as $pid) {
        $retrieved_Cart_Info[] = intval($pid);
    }
}

// Initialize discount session if not set
if (!isset($_SESSION['cart_discount_level'])) {
    $_SESSION['cart_discount_level'] = $_SESSION['discount_level'] ?? null;
}

// Process cart if there are items
if (!empty($retrieved_Cart_Info)) {

    // Determine country ID if applicable
    $sql_course_country = $coresite_curr_country_slug ? is_country($coresite_curr_country_slug, "countryID") : "";

    $country_join = "";
    $country_column = "";
    if ($sql_course_country) {
        $country_join = " LEFT JOIN content_info_to_country citc_install 
                          ON citc_install.cic_title = np.programme_id 
                         AND citc_install.cic_country_id = '" . intval($sql_course_country) . "' 
                         AND citc_install.cic_type = 'max-instalments' 
                         AND citc_install.cic_status = 'active' ";
        $country_column = ", citc_install.cic_body as country_max_instalment ";
    }

    // Sanitize IDs for SQL IN clause
    $programme_ids = implode(",", $retrieved_Cart_Info);

    // Fetch programme details
    $query_DisProg = "
        SELECT np.*, oprel.option_two, op.option_value, op.option_id as subcategory_id $country_column
        FROM option_relationships oprel
        JOIN options op ON oprel.option_two = op.option_id
        JOIN new_programmes np ON oprel.option_one = np.programme_id
        JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id
        JOIN options op_other ON option_prog_other.option_one = op_other.option_id
        $country_join
        WHERE oprel.relationship_name = 'programme type'
          AND np.programme_status = 'active'
          AND option_prog_other.relationship_name = 'old prog to new prog'
          AND op_other.status = 'active'
          AND np.programme_id IN ($programme_ids)
          AND np.deleted = '0'
    ";

    $Result_Programmes = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));

    // Define fallback thumbnails once
    $fallback_thumbnails = [
        ROOT_DIR . $fileCacheVersion . '-images/MA-Human-Resource-Management-.png',
        ROOT_DIR . $fileCacheVersion . '-images/Strategy.jpg',
        ROOT_DIR . $fileCacheVersion . '-images/' . rawurlencode('Master of Business Administration.png'),
        ROOT_DIR . $fileCacheVersion . '-images/' . rawurlencode('MA in Marketing & Innovation-2.png'),
        ROOT_DIR . $fileCacheVersion . '-images/bg2.jpg',
    ];

    // Loop through programmes
    while ($row_Programme = mysqli_fetch_assoc($Result_Programmes)) {

        // Determine thumbnail
        $thumbnail = $row_Programme['image_url'] ?: 'assets/images/default-course.png';
        if (!empty($row_Programme['country_image'])) {
            $thumbnail = $row_Programme['country_image'];
        }

        // Check file existence for localhost and live server
        if ($site_version == "localhost") {
            $file_path_local = '../' . PORTAL_FOLDER . '/uploads/course_image/' . $thumbnail;
            $thumbnail = file_exists($file_path_local) ? ROOT_DIR_PORTAL . 'uploads/course_image/' . $thumbnail : $fallback_thumbnails[$current_fallback_image_id++];
        } else {
            $file_path1 = '../' . PORTAL_FOLDER . '/uploads/course_image/' . $thumbnail;
            $file_path2 = PORTAL_FOLDER . '/uploads/course_image/' . $thumbnail;
            if (file_exists($file_path1)) {
                $thumbnail = ROOT_DIR_PORTAL . 'uploads/course_image/' . $thumbnail;
            } elseif (file_exists($file_path2)) {
                $thumbnail = ROOT_DIR_PORTAL . 'uploads/course_image/' . $thumbnail;
            } else {
                $thumbnail = $fallback_thumbnails[$current_fallback_image_id++];
            }
        }

        // Reset fallback index if exceeded
        if ($current_fallback_image_id >= count($fallback_thumbnails)) {
            $current_fallback_image_id = 0;
        }

        // Prepare currency
        if (!$coresite_curr_currency || !$todays_conversion_rate) {
            $coresite_curr_currency = !$coresite_curr_currency ? PREFERRED_CURRENCY : $coresite_curr_currency;

            if (defined("ROOT_PATH_TO_PORTAL") && file_exists(ROOT_PATH_TO_PORTAL.'./inc.currency-processing.php')) {
                require(ROOT_PATH_TO_PORTAL.'./inc.currency-processing.php');
            } else {
                require('./inc.currency-processing.php');
            }
        }
        $dis_programme_currency = $coresite_curr_currency;

        if ($dis_programme_currency && empty($symbol_for_programme_currency)) {
            $symbol_for_programme_currency = get_passed_currency_symbol($dis_programme_currency);
        }

        // Programme fees
        $dis_programme_fee = $row_Programme['discounted_fee'] ? round($row_Programme['discounted_fee'] * $todays_conversion_rate, 2) : 0;
        $dis_programme_original_fee = (float)$dis_programme_increment_percentage ? round($dis_programme_fee + ($dis_programme_fee * (float)$dis_programme_increment_percentage), 2) : round($dis_programme_fee + ($dis_programme_fee * 0.80), 2);
        $dis_programme_monthly_fee = $row_Programme['monthly_instalment'] ? round($row_Programme['monthly_instalment'] * $todays_conversion_rate, 2) : 0;

        // Programme URL
        $dis_programme_a_href = $dis_programme_pre_url.'/'.$row_Programme['url_name'];
        if ($dis_programme_category == 'training' && $row_Programme['prog_date_id']) {
            $dis_programme_a_href .= '?dt='.$row_Programme['prog_date_id'];
        }

        // Optional cosmetics discounts
        $cosmetics_dis_programme_original_fee = $cosmetics_dis_programme_fee = 0;
        if ($scholarship_cosmetics_is_on && (!in_array($dis_programme_subcategory_id, [883, 409, 884, 1253, 885]) || in_array($dis_programme_subcategory_id, $allow_semp_for_instalment_payment))) {
            $cosmetics_dis_programme_original_fee = $dis_programme_fee * 2;
            $cosmetics_dis_programme_fee = $dis_programme_original_fee;
        }

        // Add programme to cart array
        $cart_programmes[] = [
            'id' => $row_Programme['programme_id'],
            'name' => $row_Programme['programme_name'],
            'url' => $row_Programme['url_name'],
            'thumbnail' => $thumbnail,
            'discount_fee' => show_amount($dis_programme_fee),
            'original_fee' => show_amount($dis_programme_original_fee),
            'currency' => $symbol_for_programme_currency,
        ];

        // Update subtotal
        $subtotal_cart_fee += $dis_programme_fee;
    }

    // VAT / Total
    $vat = 0; // example
    $total_cart_fee = $subtotal_cart_fee + $vat;

    // Optional: debug
    /*
    echo '<pre>';
    var_dump($subtotal_cart_fee, $total_cart_fee, $cart_programmes);
    echo '</pre>';
    */
}

// Count total courses in cart
$total_courses_in_cart = count($cart_programmes);
?>
