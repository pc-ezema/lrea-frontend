<?php
    $user_signed_in = false;

    $retrieved_Cart_Info = array();
    $total_fees_in_cart = $subtotal_fees_in_cart = $total_discounted_fee = 0;

    if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') {
        $user_signed_in = true;
        
        mysqli_select_db($config, $database_config);
        $query_Cart_Info = "SELECT *
                            FROM saved_to_cart
                            WHERE login_id = ".sql_int($sess_user_id)."";
        $Cart_Info = mysqli_query($config, $query_Cart_Info) or die(mysqli_error($config));
        $row_Cart_Info = mysqli_fetch_assoc($Cart_Info);
        $totalRows_Cart_Info = mysqli_num_rows($Cart_Info);

        if ($totalRows_Cart_Info) {
            do { // loop through the cart
                $retrieved_Cart_Info[] = $row_Cart_Info['prog_id'];
            } while ($row_Cart_Info = mysqli_fetch_assoc($Cart_Info));
        }
    }
    elseif ($thisCartInfo = $_COOKIE['s2cart']) {
        $retrieved_Cart_Info = explode(",", $thisCartInfo);
    }

    if (!isset($_SESSION['cart_discount_level'])) {
        // Set the discount level to a new session variable so the portal variable remains for portal use
        $_SESSION['cart_discount_level'] = $_SESSION['discount_level'];
    }

    if (count($retrieved_Cart_Info)) {
        if ($coresite_curr_country_slug) {
            $sql_course_country = is_country($coresite_curr_country_slug, "countryID");
        }
        else {
            $sql_course_country = "";
        }

        if ($sql_course_country) {
            $country_max_no_of_instalments_join_additional = " LEFT JOIN content_info_to_country citc_install ON citc_install.cic_title = np.programme_id AND citc_install.cic_country_id = '".$sql_course_country."' AND citc_install.cic_type = 'max-instalments' AND citc_install.cic_status = 'active' ";
            $country_max_no_of_instalments_column = ", citc_install.cic_body as country_max_instalment ";
        }

        // Get all matching programmes 
        $query_DisProg = "SELECT np.*, oprel.option_two, op.option_value, op.option_id as subcategory_id ".$country_max_no_of_instalments_column."  
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                        JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                        JOIN options op_other ON option_prog_other.option_one = op_other.option_id 
                        ".$country_max_no_of_instalments_join_additional."
                        WHERE oprel.relationship_name = 'programme type' 
                            AND np.programme_status = 'active' 
                            AND option_prog_other.relationship_name = 'old prog to new prog' 
                            AND op_other.status = 'active' 
                            AND np.programme_id IN (".implode(",", $retrieved_Cart_Info).")
                            AND np.deleted = '0'"; 
        $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
        $row_DisProg = mysqli_fetch_assoc($DisProg);
        $totalRows_DisProg = mysqli_num_rows($DisProg);

        $no_of_courses_in_cart = $totalRows_DisProg;
        $thisCouponCodeInfo = $_COOKIE['coupon_code'];

        // Auto-apply the highest loyalty point the student has if a Coupon cookie does not exist.
        if ($user_signed_in && !$thisCouponCodeInfo && $_SESSION['cart_discount_level'] && array_key_exists($_SESSION['cart_discount_level'], $loyalty_discount_levels)) {
            // Get the highest loyalty coupon the student has.
            mysqli_select_db($config, $database_config);
            $query_Highest_Loyalty_Coupon = "SELECT *
                                            FROM coupon_codes
                                            WHERE deleted = '0'
                                                AND coupon_discount = '".sql_int($loyalty_discount_levels[$_SESSION['cart_discount_level']]['discount'])."'
                                                AND login_id = '".sql_int($sess_user_id)."'
                                            LIMIT 1";
            $Highest_Loyalty_Coupon = mysqli_query($config, $query_Highest_Loyalty_Coupon) or die(mysqli_error($config));
            $row_Highest_Loyalty_Coupon = mysqli_fetch_assoc($Highest_Loyalty_Coupon);
            $totalRows_Highest_Loyalty_Coupon = mysqli_num_rows($Highest_Loyalty_Coupon);

            if ($totalRows_Highest_Loyalty_Coupon) { // If student has a coupon.
                $couponCodeCookieDuration = time() + (3600 * 24); // 24 hours;
                
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie("coupon_code", $row_Highest_Loyalty_Coupon['coupon_code'], $couponCodeCookieDuration, "/", $uniqueDomainRoot);
                    }
                }

                // Reload the page so that the coupon will be applied after its cookie has been set.
                redirect(ROOT_DIR."cart");
            }
        }

        if ($thisCouponCodeInfo) {
            $coupon_to_check = $thisCouponCodeInfo; // This variable "coupon_to_check" is needed in the file inc.coupon-validity-checker.php included below.
            include('inc.coupon-validity-checker.php');

            if ($totalRows_checkifCouponValid) {
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


                $this_coupon_discount = $row_checkifCouponValid['coupon_discount'];
                $coupon_id = $row_checkifCouponValid['coupon_id'];
                $add_new_to_msg = " new";
                $removeCouponFullHTMLCode = '<a href="'.$link_root_path.'cart/remove-coupon" class="btn btn-sm btn-outline-danger rounded confirm_remove_coupon">Remove Coupon</a>';

                if ($user_signed_in && $row_checkifCouponValid['login_id'] && ($loyalty_discount_levels[$_SESSION['cart_discount_level']]['discount'] == $row_checkifCouponValid['coupon_discount'])) { // If the coupon is the one that was automatically applied for the student who has a loyalty discount level, display this message.
                    $show_coupon_applied_msg = '<div class = "alert alert-success">Congratulations. The best discount coupon <strong>'.$thisCouponCodeInfo.'</strong> has been applied to your item(s). '.$removeCouponFullHTMLCode.'</div>';
                }
                else {
                    $show_coupon_applied_msg = '<div class = "alert alert-success">Your coupon <strong>'.$thisCouponCodeInfo.'</strong> has been applied to the eligible items. '.$removeCouponFullHTMLCode.'</div>';
                }
            }
            else {// destroy the cookie
                foreach ($our_unique_site_domains as $key => $uniqueDomainRoot) {
                    if ($key == COOKIE_SITE_NAME) { // If this is the current domain
                        setcookie("coupon_code", "", time() - (3600 * 24), "/", $uniqueDomainRoot);
                    }
                }
            }
        }

        // Initialized here for dataHash "checkout 1" code.
        $data_layer_programme_items = array();
        $asset_position = 0;

        $execute_this_on_cart_page = true; // This is used to trigger the javascript that updates the DOM when an item is moved from wishlist to the cart.
    }