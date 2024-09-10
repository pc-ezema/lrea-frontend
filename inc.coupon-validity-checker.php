<?php 
    // Check if coupon is valid
    mysqli_select_db($config, $database_config);
    $query_checkifCouponValid = "SELECT *
                                FROM coupon_codes
                                WHERE coupon_code = '".sql_string($coupon_to_check)."'
                                    AND deleted  = '0'
                                LIMIT 1";
    $checkifCouponValid = mysqli_query($config, $query_checkifCouponValid) or die(mysqli_error($config));
    $row_checkifCouponValid = mysqli_fetch_assoc($checkifCouponValid);
    $totalRows_checkifCouponValid = mysqli_num_rows($checkifCouponValid);

    if ($totalRows_checkifCouponValid) {
        if ($row_checkifCouponValid['login_id']) { // If the type of coupon is a loyalty discount.
            if ($user_signed_in && ($row_checkifCouponValid['login_id'] != $sess_user_id)) { // Check if it belongs to someone else
                $totalRows_checkifCouponValid = 0;
            }
            elseif ($user_signed_in && ($row_checkifCouponValid['login_id'] == $sess_user_id)) { // If the coupon belongs to the student who is signed in.
                // Do nothing
            }
            else {
                $totalRows_checkifCouponValid = 0;
            }
        }
        else { // If coupon is not a loyalty discount type.
            if ($row_checkifCouponValid['coupon_expiry_date'] < $date_now) { // Check its expiry date.
                $totalRows_checkifCouponValid = 0;
            }
        }
    }