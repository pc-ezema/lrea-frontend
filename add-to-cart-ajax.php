<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');

    // Prepare ajax url variable
    if (SITE_NAME_LOWER != 'lrea.co.uk' && !$_POST['url_location']) {
        $_POST['url_location'] = $_SESSION['location_of_choice'].'/';
    }

    $link_root_path = defined('ROOT_DIR_UK') ? ROOT_DIR_UK.$_POST['url_location'] : ROOT_DIR.$_POST['url_location'];
    $link_root_default = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;

    if (isset($_GET['remove_coupon']) && $_GET['remove_coupon']) {
        // Delete the cookie
        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                setcookie('coupon_code', "", time() - (3600 * 24), "/", $uniqueDomainRoot);
            }
        }

        // Empty the session so it does not auto apply
        $_SESSION['cart_discount_level'] = "";

        // Success message to indicate coupon removal
        $_SESSION['userfeedback'] = "Coupon Removed Successfully.";
        $_SESSION['feedbacktype'] = "success";

        redirect(ROOT_DIR."cart");
    }

    if (isset($_POST['url_location']) && $_POST['url_location']) {
        $this_loc_val = str_ireplace('/', '', $_POST['url_location']);

        if (is_state($this_loc_val)) {
            $coresite_curr_currency = get_state_currency($this_loc_val);
        }
        elseif (is_country($this_loc_val)) {
            $coresite_curr_currency = get_country_currency($this_loc_val);
        }
    }
    else {
        $coresite_curr_currency = get_country_currency('united-kingdom');
    }

    include('inc.currency-processing.php');

    if (isset($_POST['saveTxRef']) && $_POST['saveTxRef']) { // record transaction reference sent from the ajax function on dynamic enrolment
        record_payment_transaction_reference($_POST['txref_id'], $_POST['texRef'], $_POST['gateway']);
        exit();
    }

    if (isset($this_loc_val)) { // This is done so that the urls that the cart and wishlist dropdowns will generate won't have these words on them since they're the root domain. 
        if (in_array($this_loc_val, array("", "united-kingdom", "nigeria", "uae", "usa"))) {
            $link_root_path = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;
        }
    }
  
    if (isset($_POST['action']) && $_POST['action'] == "addToCart") {
        // 405 is the Subcategory ID for Doctorate degree courses.
        $array_of_restricted_category_for_enrolment_through_cart = array(0);

        if ($_POST['longitude']) {
            $prog_subcategory_id = get_programme_subcategory_id($_POST['longitude']);

            if ($prog_subcategory_id && !in_array($prog_subcategory_id, $array_of_restricted_category_for_enrolment_through_cart)) { // If the programme category is not among those restricted from being added to cart.
                date_default_timezone_set("Europe/London");

                if ($sess_user_id > 0) { // If user is logged in
                    $return_success_msg = false;

                    if ($_SESSION['user_type'] == 'student') { // Make sure the logged in user is a student.
                        // Submit chosen course to the database
                        if (!programme_in_cart_already_added_before($sess_user_id, $_POST['longitude'])) { // Do not include programme that has already been added to the database before
                            $insertSQL = "INSERT INTO saved_to_cart (login_id, prog_id, date_added)
                                        VALUES (
                                            ".sql_int($sess_user_id).",
                                            ".sql_int($_POST['longitude']).",
                                            '".sql_string($time_now)."'
                                        )";
                            mysqli_select_db($config, $database_config);
                            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                            $insertStatus = mysqli_affected_rows($config);

                            if ($insertStatus >= 0) { // If INSERT is successful
                                $return_success_msg = true;
                            }
                            else { // If INSERT failed
                                echo "error";
                                exit();
                            }
                        }
                        else {
                            $return_success_msg = true;
                        }
                    }
                    else { // Since the logged in user is not a student, just return ok.
                        $return_success_msg = true;
                    }

                    if ($return_success_msg) {
                        echo "ok";
                        exit();
                    }
                }
                else { // user not logged in
                    if (!$_COOKIE['s2cart']) { // First time item is being added.
                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie('s2cart', $_POST['longitude'], $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }

                        echo "ok";
                        exit();
                    }
                    else { // More items being added.
                        $retrieved_Cart_Info = explode(",", $_COOKIE['s2cart']);

                        if (count($retrieved_Cart_Info)) {
                            if ($_POST["longitude"] && !in_array($_POST["longitude"], $retrieved_Cart_Info)) {
                                $retrieved_Cart_Info[] = $_POST["longitude"];
            
                                $add_New_Prgramme_To_Cookie = implode(",", $retrieved_Cart_Info);
            
                                if ($add_New_Prgramme_To_Cookie) {
                                    foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                                        if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                            setcookie('s2cart', $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                                        }
                                    }

                                    echo "ok";
                                    exit();
                                }
                            }
                            else {
                                echo "ok";
                                exit();
                            }
                        } 
                        else {
                            echo "error";
                            exit();
                        }
                    }
                }
            }
        }

        echo 'error';
        exit();
    }
    
    if (isset($_POST['action']) && $_POST['action'] == "removeFromCart") {
        $check_extra_fees = false;

        if ($sess_user_id > 0) { // If user is logged in
            if ($_SESSION['user_type'] == 'student') { // Make sure the logged in user is a student
                // Describe_what_is_going_on
                mysqli_select_db($config, $database_config);
                $deleteSQL = "DELETE FROM saved_to_cart
                            WHERE login_id = ".sql_int($sess_user_id)."
                                AND prog_id = ".sql_int($_POST['longitude'])."
                            LIMIT 1";
                $deleteResult = mysqli_query($config, $deleteSQL) or die(mysqli_error($config));
                $deleteStatus = mysqli_affected_rows($config);

                if ($deleteStatus > 0) { // If DELETE is successful
                    $check_extra_fees = true;
                }
                else { // If DELETE failed
                    echo "error";
                }
            }
        }
        else { // user not logged in
            $this_s2cart = $_COOKIE['s2cart'];

            if ($this_s2cart) {
                $retrieved_Cart_Info = explode(",", $this_s2cart);

                if (count($retrieved_Cart_Info)) {
                    $programme_Id_Key_In_Cookie = array_search($_POST['longitude'], $retrieved_Cart_Info);

                    if ($programme_Id_Key_In_Cookie !== false) {
                        unset($retrieved_Cart_Info[$programme_Id_Key_In_Cookie]);

                        $add_New_Prgramme_To_Cookie = implode(",", $retrieved_Cart_Info);

                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie("s2cart", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }

                        $check_extra_fees = true;
                    }
                }
            }
        }

        if ($check_extra_fees) {
            $this_s2certextrafee = $_COOKIE['s2certextrafee'];

            if ($this_s2certextrafee && $enable_prog_certificate_extra_fees) { // Remove from certificate extra fee if it was chosen.
                $progs_in_certificate_extra_fees_array = explode(",", $this_s2certextrafee);

                if (count($progs_in_certificate_extra_fees_array)) {
                    $programme_Id_Key_In_Cookie = array_search($_POST['longitude'], $progs_in_certificate_extra_fees_array);

                    if ($programme_Id_Key_In_Cookie !== false) {
                        unset($progs_in_certificate_extra_fees_array[$programme_Id_Key_In_Cookie]);

                        $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_extra_fees_array);

                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie("s2certextrafee", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }
                    }
                }
            }
        }

        echo "ok";
        exit();
    }

    if (isset($_POST['fetch_item'])) { // get items in the cart so we can display in the navbar cart dropdown.
        $user_cart_info = get_user_cart_info();
        $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];
        
        include("components/inc.cart-dropdown.php");
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'count') { // get number of items in the cart
        $user_cart_info = get_user_cart_info();
        $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];

        exit(json_encode(array('count'=>$no_of_courses_in_cart)));
    }

    if (isset($_POST['get_discount'])) {
        validate("get_discount", "string");

        if (empty($form_errors)) {
            $user_signed_in = false;
            
            if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') {
                $user_signed_in = true;
            }

            $coupon_to_check = $_POST['get_discount'];
            include('inc.coupon-validity-checker.php');

            if ($totalRows_checkifCouponValid) {
                $user_cart_info = get_user_cart_info();
                $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];

                $programmme_info = $user_cart_info['programme_info'];
                $prog_has_coupon = array();

                // check if coupon is valid for all programmes
                mysqli_select_db($config, $database_config);
                $query_couponValidForAllProgs = "SELECT *
                                                FROM coupon_programmes
                                                WHERE coupon_id = ".sql_int($row_checkifCouponValid['coupon_id'])."
                                                    AND prog_id > 7000000
                                                LIMIT 1";
                $couponValidForAllProgs = mysqli_query($config, $query_couponValidForAllProgs) or die(mysqli_error($config));
                $row_couponValidForAllProgs = mysqli_fetch_assoc($couponValidForAllProgs);
                $totalRows_couponValidForAllProgs = mysqli_num_rows($couponValidForAllProgs);

                foreach ($programmme_info as $key => $value) {
                    if ($totalRows_couponValidForAllProgs) {
                        $prog_has_coupon[] = $value['prog_id'];
                    }
                    else {
                        // check if prog is applicable for coupon entered
                        $if_programme_matches_coupon = check_if_programme_matches_coupon($row_checkifCouponValid['coupon_id'], $value['prog_id']);

                        if ($if_programme_matches_coupon) {
                            $prog_has_coupon[] = $value['prog_id'];
                        }
                    }
                }

                if (empty($prog_has_coupon)) {
                    exit('This discount code is not applicable to any item in your cart');
                }
                else {
                    $add_new_to_msg = " new";
                    // set coupon to a cookie
                    $couponCodeCookieDuration = time() + (3600 * 24); // 24 hours;
                    
                    foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                        if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                            setcookie("coupon_code", $_POST['get_discount'], $couponCodeCookieDuration, "/", $uniqueDomainRoot);
                        }
                    }

                    $this_prog_discount = $row_checkifCouponValid['coupon_discount']; // coupon discount in percentage
                    $total_discounted_fee = 0;
                    $output = "<div class = 'returned-cart-info'>";

                    $output .= '<h4 class="make-bold mb-4">Cart ';
                    if ($no_of_courses_in_cart > 1) {
                        $output .= '('.$no_of_courses_in_cart.' Items) </h4>';
                    }
    
                    $output .= '<div class="row cart-content">
                                    <div class="col-lg-8 mb-3">
                                    <div class = "alert alert-success">Your coupon <strong>'.$_POST['get_discount'].'</strong> has been applied to the eligible items</div>';

                                        foreach ($user_cart_info['programme_info'] as $key => $value) {
                                            if (in_array($value['prog_id'], array_values($prog_has_coupon))) {
                                                $total_discounted_fee += ($this_prog_discount/100) * $user_cart_info['programme_info'][$key]['fee'];
                                            }

                                            $output .= '
                                            <div class="cart-view-sect">
                                                <div class="row">
                                                    <div class="col-lg-7 mb-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-2">
                                                                <div class="cart-image-view">
                                                                    <img src="'.$user_cart_info['programme_info'][$key]['thumbnail'].'" class="card-image-set" />
                                                                </div>
                                                            </div>
                                                            <div class="col-10">
                                                                <h6 class="ml-3">'.$user_cart_info['programme_info'][$key]['prog_name'].'</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-lg-3 mb-3">
                                                        <h6 class="make-bold">';
                                                            if ($user_cart_info['programme_info'][$key]['fee']) $output .= $user_cart_info['payment_currency'].' '.show_amount($user_cart_info['programme_info'][$key]['fee']);
                                                            if (in_array($value['prog_id'], array_values($prog_has_coupon))) $output .= "<br/><span class='badge badge-success'>Eligible</span>";
                                                        $output .= '
                                                        </h6>
                                                    </div>';
    
                                                    if ($allow_multiple_of_each_course_at_checkout) {
                                                        $output .= '
                                                        <div class="col-lg-2 mb-3">
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <button type="button" class="quantity-left-minus btn btn-secondary btn-number"  data-type="minus" data-field="">
                                                                    <i class="fa fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                                <input type="text" id="quantity" name="quantity" class="form-control input-number text-center" value="10" min="1" max="100">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="quantity-right-plus btn btn-secondary btn-number" data-type="plus" data-field="">
                                                                        <span class="fa fa-plus"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                    }
    
                                                    $output .= '
                                                    <div class="col-lg-2 mb-3">
                                                        <button class="btn btn-outline-danger btn-block remove-from-page" id="'.$user_cart_info['programme_info'][$key]['prog_id'].'"><i class="fa fa-times"></i> Remove</button>
                                                    </div>
                                                </div>
                                            </div>';
                                            $subtotal_fees_in_cart += $user_cart_info['programme_info'][$key]['fee'];
                                        }
                                    $output .= '
                                    </div>'; 
                                    
                                    if ($total_discounted_fee) {
                                        $total_fees_in_cart = $subtotal_fees_in_cart - $total_discounted_fee;
                                    }
                        
                                    $output .= '
                                    <div class="col-lg-4 mb-3">
                                        <div class="checkout-card-view-sect">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <div class="card-total-view d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="text-muted mb-0 make-bold">SubTotal</p>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 make-bold">'.$user_cart_info['payment_currency'].' '.show_amount($subtotal_fees_in_cart).'</p>
                                                        </div>
                                                    </div>

                                                    <div class="card-total-view d-flex justify-content-between align-items-center mt-3">
                                                        <div>
                                                            <p class="text-muted mb-0 make-bold">Discount</p>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 make-bold">- '.$user_cart_info['payment_currency'].' '.show_amount($total_discounted_fee).'</p>
                                                        </div>
                                                    </div>
                                                        
                                                    <div class="card-total-view d-flex justify-content-between align-items-center mt-3">
                                                        <div>
                                                            <p class="text-muted mb-0 make-bold">Total</p>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 make-bold">'.$user_cart_info['payment_currency'].' '.show_amount($total_fees_in_cart).'</p>
                                                        </div>
                                                    </div>
    
                                                    <hr />
                                                    <div>
                                                        <img src="'.ROOT_DIR.'images/icons/payment-logos.svg" class="img-fluid" />
                                                    </div>
                                                    <hr />';
    
                                                    if ($allow_discount_code) {
                                                        $output .= '
                                                        <div class="discount-sect mb-3">
                                                            <button class="btn btn-outline-info btn-block" id="btnDiscount">I have a'.$add_new_to_msg.' discount code</button>
    
                                                            <div class="mt-4 coupon-apply-sect">
                                                                <form>
                                                                    <div class="form-row">
                                                                        <div class="form-group col-lg-9">
                                                                            <input type="text" class="form-control" name="discount_code" id="discount_code" value="'.$_POST['get_discount'].'" placeholder="Enter your coupon code..." />
                                                                            <span class="text-danger" id="is-invalid"></span>
                                                                        </div>
                                                                        <div class="form-group col-lg-3">
                                                                            <button class="btn btn-dark btn-block" id="discount_btn">Apply</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>';
                                                    }
    
                                                    $output .= '
                                                    <div class="discount-sect mb-3">
                                                        <a href="'.$link_root_path.'checkout" class="btn btn-dark btn-block">
                                                            <i class="fa fa-lock"></i> Secure Checkout
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                        $output .= '</div>';
                    echo $output;
                    exit();

                }
            }
            else {
                exit('Invalid Discount Code');
            }
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == 'clear_cart') {
        $check_extra_fees = false;

        if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in
            // Describe_what_is_going_on
            mysqli_select_db($config, $database_config);
            $deleteSQL = "DELETE FROM saved_to_cart
                        WHERE login_id = ".sql_int($sess_user_id)."";
            $deleteResult = mysqli_query($config, $deleteSQL) or die(mysqli_error($config));
            $deleteStatus = mysqli_affected_rows($config);

            if ($deleteStatus > 0) { // If DELETE is successful
                $check_extra_fees = true;
            }
            else { // If DELETE failed
                echo "error";
            }
        }
        else { // user not logged in
            if ($_COOKIE['s2cart']) {
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('s2cart', '', time() - (3600 * 24), '/', $uniqueDomainRoot);
                    }
                }
                $check_extra_fees = true;
            }
        }

        if ($check_extra_fees) {
            if ($_COOKIE['s2certextrafee'] && $enable_prog_certificate_extra_fees) { // Remove from certificate extra fee
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('s2certextrafee', '', time() - (3600 * 24), '/', $uniqueDomainRoot);
                    }
                }
            }

            if ($_COOKIE['s2shipdetails'] && $enable_prog_certificate_extra_fees) { // Remove the shipping details.
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie('s2shipdetails', '', time() - (3600 * 24), '/', $uniqueDomainRoot);
                    }
                }
            }

            echo "ok";
            exit();
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "addToWishlist") {    
        if ($sess_user_id > 0) { // If user is logged in
            $return_success_msg = false;

            if ($_SESSION['user_type'] == 'student') { // Make sure the logged in user is a student
                // submit chosen course to the database
                if (!programme_in_wishlist_already_added_before($sess_user_id, $_POST['longitude']) ) { // Do not include programme that has already been added to the database before
                    $insertSQL = "INSERT INTO saved_to_wishlist (login_id, prog_id, date_added)
                                VALUES (
                                    ".sql_int($sess_user_id).",
                                    ".sql_int($_POST['longitude']).",
                                    '".sql_string($time_now)."'
                                )";
                    mysqli_select_db($config, $database_config);
                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                    $insertStatus = mysqli_affected_rows($config);

                    if ($insertStatus >= 0) { // If INSERT is successful
                        $return_success_msg = true;
                    }
                    else { // If INSERT failed
                        echo "error";
                    }
                }
                else {
                    $return_success_msg = true;
                }
            }
            else { // Since the logged in user is not a student, just return ok.
                $return_success_msg = true;
            }

            if ($return_success_msg) {
                echo "ok";
            }
            
            exit();
        }
        else { // user not logged in
            if (!$_COOKIE['wishlist']) { // First time item is being added.
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie("wishlist", $_POST['longitude'], $addToCartCookieDuration, "/", $uniqueDomainRoot);
                    }
                }
                echo 'ok';
            }
            else { // More items are being added.
                $retrieved_Wishlist_Info = explode(",", $_COOKIE['wishlist']);

                if (count($retrieved_Wishlist_Info)) {
                    if ($_POST["longitude"] && !in_array($_POST["longitude"], $retrieved_Wishlist_Info)) {
                        $retrieved_Wishlist_Info[] = $_POST["longitude"];
    
                        $add_New_Prgramme_To_Cookie = implode(",", $retrieved_Wishlist_Info);
    
                        if ($add_New_Prgramme_To_Cookie) {
                            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                    setcookie("wishlist", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                                }
                            }
                        }
                    }
                    echo "ok";
                }
                else {
                    echo "error";
                }
            }
            
            exit();
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "removeFromWishlist") {
        if ($sess_user_id > 0) { // If user is logged in
            if ($_SESSION['user_type'] == 'student') { // Make sure the logged in user is a student.
                // Remove programme from wishlist
                mysqli_select_db($config, $database_config);
                $deleteSQL = "DELETE FROM saved_to_wishlist
                            WHERE login_id = ".sql_int($sess_user_id)."
                                AND prog_id = ".sql_int($_POST['longitude'])."
                            LIMIT 1";
                $deleteResult = mysqli_query($config, $deleteSQL) or die(mysqli_error($config));
                $deleteStatus = mysqli_affected_rows($config);

                if ($deleteStatus > 0) { // If DELETE is successful
                    echo "ok";
                }
                else { // If DELETE failed
                    echo "error";
                }   
            }
            else {  // Since the logged in user is not a student, just return ok.
                echo "ok";
            }         

            exit();
        }
        else { // user not logged in
            $this_wishlist = $_COOKIE['wishlist'];
            
            if ($this_wishlist) {
                $retrieved_Wishlist_Info = explode(",", $this_wishlist);

                if (count($retrieved_Wishlist_Info)) {
                    $programme_Id_Key_In_Cookie = array_search($_POST['longitude'], $retrieved_Wishlist_Info);

                    if ($programme_Id_Key_In_Cookie !== false) {
                        unset($retrieved_Wishlist_Info[$programme_Id_Key_In_Cookie]);

                        $add_New_Prgramme_To_Cookie = implode(",", $retrieved_Wishlist_Info);

                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie("wishlist", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }

                        echo "ok";
                    }
                }
                exit();
            }
        }
    }

    if ((isset($_GET['fetch_Wishlist']) && $_GET['fetch_Wishlist'] == 'In_Dropdown') || (isset($_POST['action']) && $_POST['action'] == 'countWishlist')) { // Display wishlist item in dropdown or Get total number of wishlist items.
        $user_wishlist_info = get_user_wishlist_info();
        $no_of_courses_in_wishlist = $user_wishlist_info['no_of_courses_in_wishlist'];

        if ($_GET['fetch_Wishlist'] == 'In_Dropdown') {
            include("components/inc.wishlist-dropdown.php");

            exit();
        }
        elseif ($_POST['action'] == 'countWishlist') {
            exit(json_encode(array('count'=> $no_of_courses_in_wishlist)));
        }
    }

    if (isset($_GET['fetch_Wishlist']) && $_GET['fetch_Wishlist'] == 'on_page') { // Get contents to display on the wishlist page.
        include('inc.wishlist-page-query.php');
        include('inc.wishlist-page-contents.php');
        exit();
    }

    if (isset($_POST['fetch_cart']) && $_POST['fetch_cart'] == 'on_page') { // Get contents to display on the cart page.
        include('inc.cart-page-query.php');
        include('inc.cart-page-contents.php');
        exit();
    }

    if (isset($_POST['action']) && ($_POST['action'] == "addToCertExtraFees") && $enable_prog_certificate_extra_fees) {
        if (!$_COOKIE['s2certextrafee']) { // First time item is being added
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie("s2certextrafee", $_POST['longitude'], $addToCartCookieDuration, "/", $uniqueDomainRoot);
                }
            }
        }
        else { // More items are being added.
            $progs_in_certificate_extra_fees_array = explode(",", $_COOKIE['s2certextrafee']);

            if (count($progs_in_certificate_extra_fees_array)) {
                if ($_POST["longitude"] && !in_array($_POST["longitude"], $progs_in_certificate_extra_fees_array)) {
                    $progs_in_certificate_extra_fees_array[] = $_POST["longitude"];

                    $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_extra_fees_array);

                    if ($add_New_Prgramme_To_Cookie) {
                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie("s2certextrafee", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }
                    }
                }
            }
        }
        
        echo "ok";
        exit();
    }

    if (isset($_POST['action']) && ($_POST['action'] == "removeFromCertExtraFees") && $enable_prog_certificate_extra_fees) { 
        $this_s2certextrafee = $_COOKIE['s2certextrafee'];

        if ($this_s2certextrafee) {
            $progs_in_certificate_extra_fees_array = explode(",", $this_s2certextrafee);

            if (count($progs_in_certificate_extra_fees_array)) {
                $programme_Id_Key_In_Cookie = array_search($_POST['longitude'], $progs_in_certificate_extra_fees_array);

                if ($programme_Id_Key_In_Cookie !== false) {
                    unset($progs_in_certificate_extra_fees_array[$programme_Id_Key_In_Cookie]);

                    $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_extra_fees_array);

                    foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                        if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                            setcookie("s2certextrafee", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                        }
                    }
                } 
            }
            echo "ok";               
            exit();
        }
    }

    if (isset($_POST['action']) && ($_POST['action'] == "addToOTHMExtraFees") && $enable_prog_certificate_extra_fees) {
        if (!$_COOKIE['s2othmextrafee']) { // First time item is being added
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie("s2othmextrafee", $_POST['longitude'], $addToCartCookieDuration, "/", $uniqueDomainRoot);
                }
            }
        }
        else { // More items are being added.
            $progs_in_certificate_extra_fees_array = explode(",", $_COOKIE['s2othmextrafee']);

            if (count($progs_in_certificate_extra_fees_array)) {
                if ($_POST["longitude"] && !in_array($_POST["longitude"], $progs_in_certificate_extra_fees_array)) {
                    $progs_in_certificate_extra_fees_array[] = $_POST["longitude"];

                    $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_extra_fees_array);

                    if ($add_New_Prgramme_To_Cookie) {
                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie("s2othmextrafee", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }
                    }
                }
            }
        }
        
        echo "ok";
        exit();
    }

    if (isset($_POST['action']) && ($_POST['action'] == "removeFromOTHMExtraFees") && $enable_prog_certificate_extra_fees) { 
        $this_s2othmextrafee = $_COOKIE['s2othmextrafee'];

        if ($this_s2othmextrafee) {
            $progs_in_certificate_extra_fees_array = explode(",", $this_s2othmextrafee);

            if (count($progs_in_certificate_extra_fees_array)) {
                $programme_Id_Key_In_Cookie = array_search($_POST['longitude'], $progs_in_certificate_extra_fees_array);

                if ($programme_Id_Key_In_Cookie !== false) {
                    unset($progs_in_certificate_extra_fees_array[$programme_Id_Key_In_Cookie]);

                    $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_extra_fees_array);

                    foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                        if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                            setcookie("s2othmextrafee", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                        }
                    }
                } 
            }
            echo "ok";               
            exit();
        }
    }

    if (isset($_POST['action']) && ($_POST['action'] == "addShippingAddress") && $enable_prog_certificate_extra_fees) { 
        $_POST["shipping_postal_code"] = trim($_POST["shipping_postal_code"]);

        validate("shipping_postal_code", "strlen", 4);
        validate("shipping_city");
        validate("shipping_country");
        validate("shipping_address");

        if (empty($form_errors)) {
            $values_to_serialize = array();
            
            $values_to_serialize["postal_code"] = $_POST["shipping_postal_code"];
            $values_to_serialize["city"] = $_POST["shipping_city"];
            $values_to_serialize["country"] = $_POST["shipping_country"];
            $values_to_serialize["address"] = $_POST["shipping_address"];
    
            $serialized_values = serialize($values_to_serialize);
            
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie("s2shipdetails", $serialized_values, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                }
            }

            echo "ok";
            exit();
        }
        else {
            if (!empty($form_errors["shipping_postal_code"])) {
                $response = array(
					'field' => 'postal_code',
					'message' => $form_errors['shipping_postal_code']
				);
            }
            elseif (!empty($form_errors['shipping_city'])) {
				$response = array(
					'field' => 'city',
					'message' => $form_errors['shipping_city']
				);
			} 
            elseif (!empty($form_errors['shipping_country'])) {
				$response = array(
					'field' => 'country',
					'message' => $form_errors['shipping_country']
				);
			} 
            elseif (!empty($form_errors['shipping_address'])) {
				$response = array(
					'field' => 'address',
					'message' => $form_errors['shipping_address']
				);
			}

			echo json_encode($response);
			exit();
        }

    }

    if (isset($_POST['action']) && $_POST['action'] == 'requestCourseAccess') { // Business account landing page request course access.
        $response = "error"; // Initialised this here.
        
        if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { // If student is logged in
            $landing_page_biz_id = $_POST['reqid'] - BIZ_ACCOUNT_REQUEST_ID_MASK;
            $landing_page_biz_prog_id = $_POST['src'] - BIZ_ACCOUNT_TARGET_COURSE_MASK;

            // Make sure that the programme is among those allowed for the business.
            mysqli_select_db($config, $database_config);
            $query_BizInfo = "SELECT ba.biz_url_slug, bvc.prog_id
                            FROM business_account ba
                            JOIN business_account_valid_courses bvc ON bvc.biz_id = ba.biz_id
                            WHERE ba.biz_id = ".sql_int($landing_page_biz_id)."
                                AND ba.biz_status = 'active'
                                AND bvc.prog_id = ".sql_int($landing_page_biz_prog_id)."
                            LIMIT 1";
            $BizInfo = mysqli_query($config, $query_BizInfo) or die(mysqli_error($config));
            $row_BizInfo = mysqli_fetch_assoc($BizInfo);
            $totalRows_BizInfo = mysqli_num_rows($BizInfo);

            if (!$totalRows_BizInfo) {
                // check if this business owns this course
                mysqli_select_db($config, $database_config);
                $query_Actions = "SELECT cc.*, np.programme_name
                                FROM business_account_role bar 
                                JOIN business_account ba ON ba.biz_id = bar.biz_id 
                                JOIN checkout chk ON chk.trainer_id = bar.biz_id
                                JOIN checkout_courses cc ON cc.checkout_id = chk.checkout_id
                                JOIN new_programmes np ON np.programme_id = cc.prog_id
                                WHERE bar.biz_id = ".sql_int($landing_page_biz_id)."
                                    AND cc.prog_id = '".sql_int($landing_page_biz_prog_id)."'
                                    AND chk.is_linkable = '0'
                                    AND chk.paid = '1'
                                    AND np.deleted = '0'";
                $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
                $row_Actions = mysqli_fetch_assoc($Actions);
                $totalRows_Actions = mysqli_num_rows($Actions);
        
                if ($totalRows_Actions) {
                    // Loop through each of the business' purchased courses
                    do {
                        $checkout_id = $row_Actions['checkout_id'];
                        
                        // Get number of staff that have been given the course
                        mysqli_select_db($config, $database_config);
                        $query_Used_Slots = "SELECT COUNT(p_id) as number_of_slots
                                        FROM prospects
                                        WHERE checkout_id = ".sql_int($checkout_id)."";
                        $Used_Slots = mysqli_query($config, $query_Used_Slots) or die(mysqli_error($config));
                        $row_Used_Slots = mysqli_fetch_assoc($Used_Slots);
                
                        // Get slots used for staffs that have been sent an email but have not registered on the website yet.
                        mysqli_select_db($config, $database_config);
                        $query_Unreg_Slots = "SELECT COUNT(be.checkout_id) as unreg_slots
                                            FROM business_account_courses_unreg_emails be
                                            WHERE be.checkout_id = ".sql_int($checkout_id)."
                                                AND be.deleted = '0'";
                        $Unreg_Slots = mysqli_query($config, $query_Unreg_Slots) or die(mysqli_error($config));
                        $row_Unreg_Slots = mysqli_fetch_assoc($Unreg_Slots);
                
                        // Check for the number of slots left
                        $available_slots_in_curr_checkout = $row_Actions['how_many'] - ($row_Used_Slots['number_of_slots'] + $row_Unreg_Slots['unreg_slots']);
        
                        if ($available_slots_in_curr_checkout) { // We have found at least one available slot
                            // Make sure that the programme has available slots for the request
                            mysqli_select_db($config, $database_config);
                            $query_BizInfo = "SELECT ba.biz_url_slug, '".sql_int($landing_page_biz_prog_id)."' as prog_id
                                            FROM business_account ba
                                            WHERE ba.biz_id = ".sql_int($landing_page_biz_id)."
                                                AND ba.biz_status = 'active' 
                                            LIMIT 1";
                            $BizInfo = mysqli_query($config, $query_BizInfo) or die(mysqli_error($config));
                            $row_BizInfo = mysqli_fetch_assoc($BizInfo);
                            $totalRows_BizInfo = mysqli_num_rows($BizInfo);

                            break; // Stop looping through the checkout courses since we have found an available slot
                        }
                    } while ($row_Actions = mysqli_fetch_assoc($Actions));

                    if (!$totalRows_BizInfo) {
                        $response = "Overbooked";
                    }
                }
                else { // The business has never purchased this course
                    $response = "Unavailable";
                }
            }
            
            if ($totalRows_BizInfo) {
                $student_has_requested = has_student_enrolled_or_requested_course_from_business($landing_page_biz_id, $landing_page_biz_prog_id, $sess_user_id);

                if (!$student_has_requested) { // Student hasn't requested for the course from any business account and is not enrolled with us as well.
                    // Record the student's request for the course
                    $insertSQL = "INSERT INTO business_account_course_requests (biz_id, course_id, login_id, `status`, date_added)
                                VALUES (
                                    ".sql_int($landing_page_biz_id).",
                                    ".sql_int($landing_page_biz_prog_id).",
                                    ".sql_int($_SESSION['user_id']).",
                                    'pending',
                                    '".sql_string($time_now)."'
                                )";
                    mysqli_select_db($config, $database_config);
                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                    $insertStatus = mysqli_affected_rows($config);

                    if ($insertStatus >= 0) { // If INSERT is successful
                        $response = "Pending";
                    }
                }
                else {
                    if ($student_has_requested == "pending") { // Already requested for the course from the organisation.
                        $response = "Pending";
                    }
                    elseif ($student_has_requested == "unavailable") { // Student has either already been enrolled in the course or already requested the same course from another organisation
                        $response = "Unavailable";
                    }
                }
            }
        }

        echo $response;
        
        exit();
    }

    if (isset($_POST['action']) && $_POST['sanitize_celuload']) { // This segment is meant for Cellulant payment gateway. The payload is encrypted here before being sent to the service provider.
        header("Access-Control-Allow-Origin: *");

        if (preg_match('/subscription/i', $_POST['order'])) { // If the payment is for subscription
            $cellulant_transaction_ref = time().mt_rand(111, 999).$_POST['order'];
        }
        else { // Payment is not for subscription.
            $cellulant_transaction_ref = time().mt_rand(111, 999).'CH'.$_POST['order'];
        }

        $names = explode(" ", $_POST['name']);

        mysqli_select_db($config, $database_config);
        $query_Country_Web_Code = "SELECT mmc.webCode
                                    FROM myip_ms_countries mmc
                                    JOIN loccountries loc ON loc.cntry = mmc.countryID
                                    WHERE mmc.api_currency_code = '".sql_string($_POST['currency'])."'
                                    LIMIT 1";
        $Country_Web_Code = mysqli_query($config, $query_Country_Web_Code) or die(mysqli_error($config));
        $row_Country_Web_Code = mysqli_fetch_assoc($Country_Web_Code);
        $totalRows_Country_Web_Code = mysqli_num_rows($Country_Web_Code);

        if ($totalRows_Country_Web_Code) {
            $country_code = $row_Country_Web_Code['webCode'];
        }
        else {
            $country_code = "";
        }

        if ($country_code) {
            if (!preg_match('/subscription/i', $_POST['order'])) { // If the payment is not for subscription
                // Save the transaction reference.
                record_payment_transaction_reference($_POST['order'], $cellulant_transaction_ref, "cellulant");
            }

            $cellulant_payload = array(
                "merchantTransactionID" => $cellulant_transaction_ref,
                "requestAmount" => $_POST['fee'],
                "currencyCode" => $_POST['currency'],
                "accountNumber" => $_POST['order'],
                "serviceCode" => CELLULANT_SERVICE_CODE,
                "dueDate" => date("Y-m-d H:i:s", strtotime("+30 Minutes")), //Must be a future date
                "requestDescription" => $_POST['item'],
                "countryCode" => $country_code,
                "languageCode" => "en",
                "customerFirstName" => $names[0],
                "customerLastName" => $names[1],
                "customerEmail" => $_POST['email'],
                "successRedirectUrl" => ROOT_DIR."cellulant-transaction-complete.php?oid=".$_POST['order']."&cid=".$_POST['product']."&dcon=".$_POST['dcon'].$_POST['extra'],
                "failRedirectUrl" => ROOT_DIR."cellulant-transaction-complete.php?oid=".$_POST['order']."&cid=".$_POST['product']."&dcon=".$_POST['dcon'].$_POST['extra'],
                "paymentWebhookUrl" => ROOT_DIR."cellulant-payment-webhook.php",
            );

            $result = encrypt_cellulant_payload($cellulant_payload, $country_code);

            echo $result;
        }

        exit();
    }

    if (isset($_POST['action']) && ($_POST['action'] == "addToCertAttestationFee") && $enable_cert_attestation) {
        if (!$_COOKIE['s2certattestion']) { // First time item is being added
            foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                    setcookie("s2certattestion", $_POST['longitude'], $addToCartCookieDuration, "/", $uniqueDomainRoot);
                }
            }
        }
        else { // More items are being added.
            $progs_in_certificate_attestation_array = explode(",", $_COOKIE['s2certattestion']);

            if (count($progs_in_certificate_attestation_array)) {
                if ($_POST["longitude"] && !in_array($_POST["longitude"], $progs_in_certificate_attestation_array)) {
                    $progs_in_certificate_attestation_array[] = $_POST["longitude"];

                    $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_attestation_array);

                    if ($add_New_Prgramme_To_Cookie) {
                        foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                            if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                                setcookie("s2certattestion", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                            }
                        }
                    }
                }
            }
        }
        
        echo "ok";
        exit();
    }

    if (isset($_POST['action']) && ($_POST['action'] == "removeFromCertAttestationFee") && $enable_cert_attestation) { 
        $this_s2certattestion = $_COOKIE['s2certattestion'];

        if ($this_s2certattestion) {
            $progs_in_certificate_attestation_array = explode(",", $this_s2certattestion);

            if (count($progs_in_certificate_attestation_array)) {
                $programme_Id_Key_In_Cookie = array_search($_POST['longitude'], $progs_in_certificate_attestation_array);

                if ($programme_Id_Key_In_Cookie !== false) {
                    unset($progs_in_certificate_attestation_array[$programme_Id_Key_In_Cookie]);

                    $add_New_Prgramme_To_Cookie = implode(",", $progs_in_certificate_attestation_array);

                    foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                        if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                            setcookie("s2certattestion", $add_New_Prgramme_To_Cookie, $addToCartCookieDuration, "/", $uniqueDomainRoot);
                        }
                    }
                } 
            }
            echo "ok";               
            exit();
        }
    }

    if (isset($_POST['action']) && ($_POST['action'] == 'calendarYear') && isset($_POST['year'])) {
        $selected_year = $current_year = $_POST['year'];
        $start_of_selected_year = $selected_year.'-01-01';
        $end_of_selected_year = $selected_year.'-12-31';

        $start_of_following_year = date('Y-m-d', strtotime('+1 year', strtotime($start_of_selected_year)));

        if (!$include_past_trainings) {
            if ($_POST['year'] == date('Y')) { // Use current month, ignore past trainings.
                $start_of_selected_year = $selected_year.'-'.date('m').'-01';
            }
        }

        $training_programme_contents = array();
        $csv_counter = 0;

        $csv_array_item[0] = array("Month", "Programme Title", "Date", "Programme Venue", "Full Price", "Discounted Price", "Covers"); // The headers of the CSV file.

        function prepare_feature_title(Array $features) { // This is just for the array_map function. That is why it is not in custom.php file.
            return ucwords($features['title']);
        }

        $location_slug = '';

        if ($_POST['url_location'] && ($_POST['url_location'] != 'united-kingdom')) {
            $location_slug =  str_ireplace('/', '', $_POST['url_location']).'/';
        }

        // Get all the trainings for the selected year.
        mysqli_select_db($config, $database_config);
        $query_otherDates = "SELECT DISTINCT YEAR(pd.start_date) start_year, MONTH(pd.start_date) start_month 
                            FROM programme_dates pd 
                            WHERE pd.start_date >= '".sql_string($start_of_selected_year)."' 
                                AND pd.start_date < '".sql_string($start_of_following_year)."'  
                                AND pd.countryID <> '' 
                            ORDER BY pd.start_date ASC";
        $otherDates = mysqli_query($config, $query_otherDates) or die(mysqli_error($config));
        $row_otherDates = mysqli_fetch_assoc($otherDates);
        $totalRows_otherDates = mysqli_num_rows($otherDates);

        if ($totalRows_otherDates) {
            do {
                $target_date_period = $row_otherDates['start_year'].'-'.$row_otherDates['start_month'].'-01';
    
                $prog_box_display_type = 'training';
                $prog_query_limit = "LIMIT 4000";
                $prog_query_where_additional = " AND pd.start_date >= '".$target_date_period."' AND pd.start_date  < '".date('Y-m', strtotime($target_date_period.' + 1 month'))."-01' ";
                $prog_query_order_by = "ORDER BY pd.start_date ASC, myctry.countryName DESC";
                include('inc.programme-query.php');
    
                if ($totalRows_DisProg) {
                    include('inc.training-calendar-prog-array-content.php'); // This file will prepare all the programme details into an array 'training_programme_contents'
                }
            } while ($row_otherDates = mysqli_fetch_assoc($otherDates));
        }

        // Store the values of the CSV/PDF array in the SESSION to be read back later.
        $csv_session_identifier = 'training_calendar_'.$current_year;
        if (count($training_programme_contents) && $download_trainings_calendar_as_pdf) {
            $_SESSION[$csv_session_identifier] = json_encode(iso88591_to_utf8($training_programme_contents));
        }
        elseif (!$download_trainings_calendar_as_pdf) { // CSV
            $_SESSION[$csv_session_identifier] = json_encode(iso88591_to_utf8($csv_array_item));
        }

        /**
         * Get the years of all upcoming trainings.
         */
        $start_of_this_year = date('Y').'-01-01';

        mysqli_select_db($config, $database_config);
        $query_UpcomingTrainingYears = "SELECT DISTINCT YEAR(pd.start_date) start_year 
                                        FROM programme_dates pd 
                                        WHERE pd.start_date >= '".sql_string($start_of_this_year)."' 
                                            AND pd.countryID <> '' 
                                        ORDER BY pd.start_date ASC";
        $UpcomingTrainingYears = mysqli_query($config, $query_UpcomingTrainingYears) or die(mysqli_error($config));
        $row_UpcomingTrainingYears = mysqli_fetch_assoc($UpcomingTrainingYears);
        $totalRows_UpcomingTrainingYears = mysqli_num_rows($UpcomingTrainingYears);

        ob_start();

        include('inc.trainings-calendar-page-contents.php');

        $selected_year_training_contents = compress_page(ob_get_contents());

        ob_end_clean();

        echo $selected_year_training_contents;

        exit();
    }

    if (isset($_POST['calendarForm']) && isset($_POST['currentYear']) && ($_POST['calendarForm'] == 'sent')) {
        $individual_info = array();

        foreach ($_POST as $key => $value) { // Iterate through values
            if ($key != 'email') {
                if (!in_array($key, array('currentYear', 'calendarForm', 'url_location'))) {
                    validate($key, 'string');
                    $individual_info[] = strtoupper($key).' : '.$value;
                }
            } 
            else {
                validate('email', 'email');
                $individual_info[] = strtoupper($key).' : '.$value;
            }
        }

        if (!$form_errors) {
            $subject = $_POST['currentYear']." Traning Calendar Downloaded at ".date("Y-m-d H:i:s");
            
            //email body
            $message_body = implode("\r\n", $individual_info);

            $to_email = INFO_SUPPORT_EMAIL.', '.TECH_SUPPORT_EMAIL;

            $email_parameters_to_serialize = array();

            $all_recipients = explode(",", $to_email);

            $from_name_to_use = 'LREA Trainings Calendar Page';
            $from_email_to_use = 'no-reply@lrea.co.uk';

            $email_parameters_to_serialize['subject'] = $subject;
            $email_parameters_to_serialize['recipient'] = $all_recipients;
            $email_parameters_to_serialize['from_name'] = $from_name_to_use;
            $email_parameters_to_serialize['from_email'] = $from_email_to_use;
            $email_parameters_to_serialize['reply_to_name'] = $from_name_to_use;
            $email_parameters_to_serialize['reply_to_email'] = $from_email_to_use;
            $email_parameters_to_serialize['mail_body'] = $message_body;

            $serialized_mail_content = serialize($email_parameters_to_serialize);
            $cron_name = 'business-solution '.$time_now;
            
            // Insert into the cron_execution table
            $insertSQL = "INSERT INTO cron_execution (cron_name, last_run, additional_info)
                        VALUES (
                            '".sql_string($cron_name)."',
                            '".sql_string($time_now)."',
                            '".sql_string_br_extended($serialized_mail_content)."'
                        )                                
                        ON DUPLICATE KEY UPDATE last_run = VALUES(last_run),
                                                additional_info = VALUES(additional_info)";
            mysqli_select_db($config, $database_config);
            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
            $insertStatus = mysqli_affected_rows($config);

            if ($insertStatus > 0) { // If INSERT is successful
                $_SESSION['cal_first_name'] = $_POST['first_name'];
                $_SESSION['cal_last_name'] = $_POST['last_name'];
                $_SESSION['cal_gender'] = $_POST['gender'];
                $_SESSION['cal_email'] = $_POST['email'];
                $_SESSION['cal_phone'] = $_POST['phone'];
                $_SESSION['cal_country'] = $_POST['country'];
                $_SESSION['cal_company'] = $_POST['company'];
                $_SESSION['cal_size'] = $_POST['company_size'];
                $_SESSION['cal_job_title'] = $_POST['job_title'];
                $_SESSION['cal_tra_need'] = $_POST['training_needs'];
                $_SESSION['cal_decision'] = $_POST['do_you_make_training_decisions'];
                $_SESSION['cal_pro_date'] = $_POST['proposed_date'];
                $_SESSION['cal_pro_co'] = $_POST['proposed_location'];
                $_SESSION['allow_calendar_download'] = true;

                echo "ok";
            }
        }

        exit();
    }
?>
