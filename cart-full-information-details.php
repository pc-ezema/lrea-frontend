<?php 
    require_once('connections/config.php');
    require_once('components/misc.inc.php');
    $suggestAPCCInfoInCart = false;
?>

<?php if ($_GET['display_cart_info']) { ?>
    <?php  
        $coresite_curr_currency = $_GET["mycurr"]; // Get the student's currency

        // Get the student cart details
        $user_cart_info = get_user_cart_info(); 
        $no_of_courses_in_cart = $user_cart_info['no_of_courses_in_cart'];

        $numberOfItemsInCart = ($no_of_courses_in_cart > 1) ? $no_of_courses_in_cart.' Items' : $no_of_courses_in_cart.' Item';

        if (defined('ROOT_DIR_UK')) {
            $link_root_default = ROOT_DIR_UK;
            $link_root_path = ROOT_DIR_UK.$location_slug;
        }
        else {
            $link_root_default = ROOT_DIR;
            $link_root_path = ROOT_DIR.$location_slug;
        }
    ?>

    <?php if($no_of_courses_in_cart == 0 ) { ?>
        <?php if (($sess_user_id > 0) && !in_array($_SESSION['user_type'], array('student', 'applicant'))) { ?>
            <p class='text-center'><i class="fa fa-info-circle text-danger fa-3x"></i><br>You are signed in as a staff. You cannot add an item to cart.</p>
        <?php } else { ?>
            <p class='text-center'>No course in your cart.</p>
        <?php } ?>
    <?php } else { $groupOfProgIDs = array(); $student_cart_currency = get_passed_currency_symbol($user_cart_info['payment_currency']); ?>
        <div class="mb-4">
            <h5 class="text-center make-bold">Subtotal (<?php echo $numberOfItemsInCart; ?>): <?php echo $student_cart_currency.' '.show_amount($user_cart_info['payment_total']); ?></h5>
            <a href="<?php echo $link_root_path.'cart'; ?>" class="btn btn-success make-bold btn-lg btn-block btn-bord-radius">Proceed to cart (<?php echo $numberOfItemsInCart; ?>)</a>
        </div>

        <div class="in-cart-container-sect mb-4">
            <?php foreach ($user_cart_info['programme_info'] as $key => $value) { ?>
                <?php $groupOfProgIDs[] = $user_cart_info['programme_info'][$key]['prog_id']; ?>
                <?php if ($user_cart_info['programme_info'][$key]['prog_category'] == 'training') $suggestAPCCInfoInCart = true; ?>
                <div class="in-cart-list-group-item py-3 px-2">
                    <div class="media">
                        <div class="in-cart-image-view-2 mr-3">
                            <img src="<?php echo $user_cart_info['programme_info'][$key]['thumbnail']; ?>" class="card-image-set">
                        </div>

                        <div class="media-body">
                            <h6 class="make-bold cart-title-name ft-md">
                                <a href="<?php echo $link_root_path.$user_cart_info['programme_info'][$key]['prog_url']?>" target="_blank">
                                    <?php echo $user_cart_info['programme_info'][$key]['prog_name']; ?>
                                </a>
                            </h6>

                            <p class="mb-1 make-bold">
                                <?php if ($user_cart_info['programme_info'][$key]['fee']) echo $student_cart_currency.' '.show_amount($user_cart_info['programme_info'][$key]['fee']); ?>

                                <?php if ($user_cart_info['programme_info'][$key]['cosmetics_original_fee']) { ?>
                                    <small>(Get 50% scholarship for one-time payment.)</small>
                                <?php } ?>
                            </p>

                            <span class="text-muted ft-xs">
                                <i class="fa fa-check-circle text-success ft-xs"></i> Added to cart
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php 
            if ($suggestAPCCInfoInCart) {
                // Get one APPC Course ID to suggest since trainings were added to the cart
                mysqli_select_db($config, $database_config);
                $query_suggestAPCCCourse = "SELECT np.programme_id
                                            FROM option_relationships oprel 
                                            JOIN options op ON oprel.option_one = op.option_id 
                                            JOIN new_programmes np ON oprel.option_two = np.programme_id 
                                            WHERE oprel.relationship_name = 'old prog to new prog' 
                                                AND op.option_name LIKE 'advanced professional c%' 
                                                AND np.deleted = '0'
                                            ORDER BY np.programme_id DESC
                                            LIMIT 1";
                $suggestAPCCCourse = mysqli_query($config, $query_suggestAPCCCourse) or die(mysqli_error($config));
                $row_suggestAPCCCourse = mysqli_fetch_assoc($suggestAPCCCourse);
                $totalRows_suggestAPCCCourse = mysqli_num_rows($suggestAPCCCourse);

                if ($totalRows_suggestAPCCCourse) {
                    $groupOfProgIDs[] = $row_suggestAPCCCourse['programme_id'];
                }
            }

            // Get Programmes similar to the course(s) in the cart
            mysqli_select_db($config, $database_config);
            $query_DisProg = "SELECT np.*, oprel.option_two, option_other.option_id as subcategory_id, option_other.parent_option_id as parent_category_id, op.option_value 
                            FROM option_relationships oprel 
                            JOIN options op ON oprel.option_one = op.option_id 
                            JOIN new_programmes np ON oprel.option_two = np.programme_id 
                            JOIN option_relationships oprel_other ON oprel_other.option_one = np.programme_id 
                            JOIN options option_other ON option_other.option_id = oprel_other.option_two 
                            WHERE oprel.relationship_name = 'old prog to new prog' 
                                AND np.programme_status = 'active' 
                                AND oprel_other.relationship_name = 'programme type' 
                                AND op.status = 'active' 
                                AND op.option_name IN (
                                    SELECT DISTINCT op.option_name
                                    FROM option_relationships oprel 
                                    JOIN options op ON oprel.option_one = op.option_id 
                                    JOIN new_programmes np ON oprel.option_two = np.programme_id 
                                    WHERE oprel.relationship_name = 'old prog to new prog' 
                                        AND np.programme_id IN (".implode(", ", $groupOfProgIDs).")
                                        AND np.deleted = '0'
                                )
                                AND np.programme_id NOT IN ( /* Do not suggest trainings since there can be expired trainings suggested */
                                    SELECT np.programme_id  
                                    FROM option_relationships option_prog_category 
                                    JOIN options op_category ON option_prog_category.option_two = op_category.option_id 
                                    JOIN new_programmes np ON option_prog_category.option_one  = np.programme_id 
                                    WHERE option_prog_category.relationship_name = 'programme category' 
                                        AND op_category.option_value = 'training' 
                                        AND np.deleted = '0'
                                )
                                AND np.programme_id NOT IN (".implode(", ", $groupOfProgIDs).")
                                AND np.deleted = '0'
                            ORDER BY RAND()
                            LIMIT 4";
            $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
            $row_DisProg = mysqli_fetch_assoc($DisProg);
            $totalRows_DisProg = mysqli_num_rows($DisProg);
        ?>

        <?php if ($totalRows_DisProg) { ?>
            <hr/>

            <div class="in-cart-container-sect mt-4">
                <h6 class="make-bold pt-4">You might also like...</h6>

                <?php do { ?>
                    <?php 
                        $prog_var_file_dir = array('', '../', '../../', '../../../', '../lrea/', '../new_portal/');
                        $prog_var_file_name = 'inc.programme-variables.php';
                    
                        foreach ($prog_var_file_dir as $prog_var_file_path) {
                            if (@file_exists($prog_var_file_path.$prog_var_file_name)) {
                                require($prog_var_file_path.$prog_var_file_name);
                                break;
                            }
                        }
                    ?>
                    <div class="in-cart-list-group-item py-3 px-2 cart-info" data-id="<?php echo $dis_programme_id; ?>">
                        <div class="media">
                            <div class="in-cart-image-view-2 mr-3">
                                <img src="<?php echo $dis_programme_thumbnail; ?>" class="card-image-set">
                            </div>

                            <div class="media-body">
                                <h6 class="make-bold cart-title-name ft-md">
                                    <a href="<?php echo $link_root_path.$dis_programme_a_href; ?>" target="_blank">
                                        <?php echo $dis_programme_name; ?>
                                    </a>
                                </h6>

                                <p class="mb-3 make-bold">
                                    <?php echo $symbol_for_programme_currency.' '.show_amount($dis_programme_fee); ?>

                                    <?php if ($cosmetics_dis_programme_fee) { ?>
                                        <small>(Get 50% scholarship for one-time payment.)</small>
                                    <?php } ?>
                                </p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <button class="btn btn-purple btn-sm btn-bord-radius btn-block one-click-enrol save-to-cart">
                                            Add to cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } while($row_DisProg = mysqli_fetch_assoc($DisProg)); ?>
            </div>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <div class="text-center">
        <img src="<?php echo MEDIA_ROOT_DIR; ?>images/banners/loader.gif" class="img-fluid" width="20%" />
        <p class="make-bold">Loading...</p>
    </div>
<?php } ?>