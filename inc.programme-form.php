<?php 
    $submit_form_to = !$same_page_landing_form ? FORM_ENGINE : '';
    $link_root_default = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR;

    if ($sess_user_id > 0 && $_SESSION['user_type'] == 'student') { 
        $already_existing_values = true;

        // Get the profile info for the student
        mysqli_select_db($config, $database_config);
        $query_UserInfo = "SELECT usi.* 
                        FROM users_students_info usi 
                        WHERE usi.login_id = ".sql_int($sess_user_id)." 
                        LIMIT 1";
        $UserInfo = mysqli_query($config, $query_UserInfo) or die(mysqli_error($config));
        $row_UserInfo = mysqli_fetch_assoc($UserInfo);
        $totalRows_UserInfo = mysqli_num_rows($UserInfo);

        $existing_info_email = $_SESSION['email'];
        $existing_info_full_name = $row_UserInfo['full_name'] ? $row_UserInfo['full_name'] : get_student_exact_db_value('full_name', $existing_info_email);
        $existing_info_phone = $row_UserInfo['phone_number'] ? $row_UserInfo['phone_number'] : get_student_exact_db_value('phone', $existing_info_email);
    }

    $highest_education_array =  array(
        'Doctoral degree', 
        'Masters degree', 
        'Graduate diploma', 
        'Graduate certificate', 
        'Bachelor degree', 
        'Advanced diploma / Associates degree', 
        'Diploma',
        'Vocational graduate diploma',
        'Vocational graduate certificate',
        'Advanced diploma',
        'Certificate IV',
        'Certificate III',
        'Certificate II',
        'Certificate I',
        'Senior school certificate'
    );

    if (isset($_GET['univibes-click-id']) && $_GET['univibes-click-id']) {
        $track_univibes_click_id_for_coresite = $_GET['univibes-click-id'];
    } 
    elseif (!isset($_GET['univibes-click-id']) && isset($_COOKIE['univibes-click-id']) && $_COOKIE['univibes-click-id']) {
        $track_univibes_click_id_for_coresite = $_COOKIE['univibes-click-id'];
    }
    else {
        $track_univibes_click_id_for_coresite = "";
    }
?>

<form action="<?php echo $submit_form_to; ?>" id="request-section" class="ft-sm" method="post">
    <?php if ($_GET['request'] == 'received') { ?>
        <div class="alert alert-success" role="alert" style="margin-top: 27px;height: 300px;display: flex;flex-direction: column;justify-content: center;text-align: center;">
            <i class="fa fa-check-circle fa-3x mb-2"></i>
            <h4><strong>Your request has been received</strong></h4>
            <p style="margin-top: 33px;font-size: 1.3rem;">An admissions expert will be in touch with you soon.</p>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-floating mb-3">
                    <input type="text" name="full_name" id="full_name" value="<?php if ($existing_info_full_name) echo $existing_info_full_name; else echo trim($_POST["full_name"]); ?>" class="form-control rounded-8" placeholder="Full Name" pattern=".{3,}" title="Must be at least 3 characters." <?php if ($existing_info_full_name) echo 'disabled'; else echo 'required'; ?> />
                    <label for="full_name">Full Name</label>
                    <?php if (isset($form_errors['full_name'])) echo $form_errors["full_name"]; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-floating mb-3">
                    <input type="email" id="email" name="email" value="<?php if ($existing_info_email) echo $existing_info_email; else echo trim($_POST["email"]); ?>" class="form-control rounded-8" placeholder="Email" pattern="[a-zA-Z0-9]+(([_+.-]{1})?[a-zA-Z0-9]+)*@[a-zA-Z0-9]+((-{1})?[a-zA-Z0-9]+)*(\.[a-zA-Z0-9]{2,})+" title="Kindly confirm that there is no error in your email address." <?php if ($existing_info_email) echo 'disabled'; else echo 'required'; ?>>
                    <label for="email">Email Address</label>
                    <?php if (isset($form_errors['email'])) echo $form_errors["email"]; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-floating mb-3">
                    <input type="text" id="phone" name="phone" maxlength="15" value="<?php if ($existing_info_phone) echo $existing_info_phone; else echo trim($_POST["phone"]); ?>" class="form-control rounded-8" placeholder="Phone" pattern="[0-9]{7,}" title="Must be at least 7 digits. No space or symbol." <?php if ($existing_info_phone) echo 'disabled'; else echo 'required'; ?> />
                    <label for="phone">Phone Number</label>
                    <?php if (isset($form_errors['phone'])) echo $form_errors["phone"]; ?>
                </div>
            </div>
            <?php if (!$already_existing_values) { ?>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="country" class="form-select rounded-8" id="country" required>
                            <option value="">Nationality</option>
                            <?php foreach($loccountries as $value) { ?>
                                <option value="<?php echo $value['countryName'] ?>"<?php if ($_POST['country'] == $value['countryName']) echo ' selected="selected"' ?>><?php echo $value['countryName']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="country">Nationality</label>
                        <?php if (isset($form_errors['country'])) echo $form_errors["country"]; ?>
                    </div>
                </div>
            <?php } ?>
        
            <?php if ($already_existing_values) { ?>
                <input type="hidden" name="email" value="<?php echo $existing_info_email ?>" />
            <?php } ?>
            
            <?php if ($dis_old_prog_name) { // Show the backend programme name ?>
                <input type="hidden" id="progName" name="programme" value="<?php echo $dis_old_prog_name; ?>" />
            <?php } else { // Show the website programme name ?>
                <input type="hidden" id="progName" name="programme" value="<?php echo $dis_programme_name; ?>" />
            <?php } ?>

            <?php if (isset($_GET['dt'])) { // Store the date that the prospect is interested in ?>
                <input type="hidden" name="date_interested" value="<?php echo $_GET['dt'] ?>" />
            <?php } ?>

            <?php if (!$already_existing_values) { ?>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="country_of_residence" class= "form-select rounded-8" id="country_of_residence" required>
                            <option value="">Country of residence</option>
                            <?php foreach($loccountries as $value) { ?>
                                <option value="<?php echo $value['countryName'] ?>"<?php if ($_POST['country_of_residence'] == $value['countryName']) echo ' selected="selected"' ?>><?php echo $value['countryName']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="country_of_residence">Country of residence</label>
                        <?php if (isset($form_errors['country_of_residence'])) echo $form_errors["country_of_residence"]; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="years_experience" class= "form-select rounded-8" id="years_experience" required>
                            <option value="">Years of experience</option>
                            <?php for ($i = 1; $i <= 40; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if ($_POST['years_experience'] == $i) echo ' selected="selected"'; ?>><?php echo $i; ?></option>
                            <?php } ?> 
                        </select>
                        <label for="years_experience">Years of experience</label>
                        <?php if (isset($form_errors['years_experience'])) echo $form_errors["years_experience"]; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="highest_education" class= "form-select rounded-8" id="" required>
                            <option value="">Highest level of education</option>
                            <?php foreach($highest_education_array as $highest_education) { ?>
                                <option value="<?php echo $highest_education ?>"<?php if ($_POST['highest_education'] == $highest_education) echo ' selected="selected"' ?>><?php echo $highest_education; ?></option>
                            <?php } ?>
                        </select>
                        <label for="highest_education">Highest level of education</label>
                        <?php if (isset($form_errors['highest_education'])) echo $form_errors["highest_education"]; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="gender" class= "form-select rounded-8" id="gender" required>
                            <option value="">Gender</option>
                            <option value="m"<?php if ($_POST['gender'] == 'm') echo ' selected="selected"' ?>>Male</option>
                            <option value="f"<?php if ($_POST['gender'] == 'f') echo ' selected="selected"' ?>>Female</option>
                        </select>
                        <label for="gender">Gender</label>
                        <?php if (isset($form_errors['gender'])) echo $form_errors["gender"]; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="job_position" class= "form-select rounded-8" id="job_position" required>
                            <option value="">Job Position/Title</option>
                            <?php foreach($position as $value) { ?>
                                <option value="<?php echo $value['option_value'] ?>"<?php if ($_POST['job_position'] == $value['option_value']) echo ' selected="selected"' ?>><?php echo $value['option_value']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="job_position">Job Position/Title</label>
                        <?php if (isset($form_errors['job_position'])) echo $form_errors["job_position"]; ?>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select name="how_did_hear" class= "form-select rounded-8" id="how_did_hear" required>
                            <option value="">How did you hear about us?</option>
                            <?php foreach($source as $value) { ?>
                                <option value="<?php echo $value['option_value'] ?>"<?php if ($_POST['how_did_hear'] == $value['option_value']) echo ' selected="selected"' ?>><?php echo $value['option_value']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="how_did_hear">How did you hear about us?</label>
                        <?php if (isset($form_errors['how_did_hear'])) echo $form_errors["how_did_hear"]; ?>
                    </div>
                </div>

                <?php if (isset($form_is_for_general_advert) && $form_is_for_general_advert && is_array($list_of_programmes_in_category) && count($list_of_programmes_in_category)) { // If it is a page that list all the programmes of available partner schools. ?>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <select type="text" id="generalProg" class= "form-select rounded-8">
                                <?php foreach ($list_of_programmes_in_category as $programmes) { ?>
                                    <option value="<?php echo $programmes['prog_url']; ?>" nu-name="<?php echo $programmes['new_prog_name']; ?>" ol-name="<?php echo $programmes['old_prog_name']; ?>" has-inst="<?php echo $programmes['has_instalment']; ?>" duration="<?php echo $programmes['duration']; ?>" 
                                    pedagogy="<?php echo $programmes['pedagogy']; ?>" tuition-fee="<?php echo $programmes['original_fee']; ?>" discount-percent="<?php echo $programmes['discount_percent']; ?>" full-fee="<?php echo $programmes['one_time_payment_fee']; ?>" inst-no="<?php echo $programmes['no_of_instalment']; ?>" 
                                    amount-saved="<?php echo $programmes['instalment_amount_saved']; ?>" mon-fee="<?php echo $programmes['estimated_monthly_fee']; ?>" instalment-discount="<?php echo $programmes['instalment_percent']; ?>" course-bg="<?php echo $programmes['programme_banner']; ?>">
                                        <?php echo $programmes['new_prog_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label for="generalProg">Select a programme</label>
                        </div>

                        <div class="alert alert-warning border-0 sticky-alert-notification rounded-8 fee-change-alert" style="display: none;">
                            <p class="mb-0 make-bold text-center">Please scroll down to see the fee <i class="fa fa-arrow-down"></i></p>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <?php if (isset($form_is_for_general_advert) && $form_is_for_general_advert && is_array($list_of_programmes_in_category) && count($list_of_programmes_in_category)) { // If it is a page that list all the programmes of available partner schools. ?>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <select type="text" id="generalProg" class= "form-select rounded-8">
                                <?php foreach ($list_of_programmes_in_category as $programmes) { ?>
                                    <option value="<?php echo $programmes['prog_url']; ?>" nu-name="<?php echo $programmes['new_prog_name']; ?>" ol-name="<?php echo $programmes['old_prog_name']; ?>" has-inst="<?php echo $programmes['has_instalment']; ?>" duration="<?php echo $programmes['duration']; ?>" 
                                    pedagogy="<?php echo $programmes['pedagogy']; ?>" tuition-fee="<?php echo $programmes['original_fee']; ?>" discount-percent="<?php echo $programmes['discount_percent']; ?>" full-fee="<?php echo $programmes['one_time_payment_fee']; ?>" inst-no="<?php echo $programmes['no_of_instalment']; ?>" 
                                    amount-saved="<?php echo $programmes['instalment_amount_saved']; ?>" mon-fee="<?php echo $programmes['estimated_monthly_fee']; ?>" instalment-discount="<?php echo $programmes['instalment_percent']; ?>" course-bg="<?php echo $programmes['programme_banner']; ?>">
                                        <?php echo $programmes['new_prog_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <label for="generalProg">Select a programme</label>
                        </div>

                    <div class="alert alert-warning border-0 sticky-alert-notification rounded-8 fee-change-alert" style="display: none;">
                        <p class="mb-0 make-bold text-center">Please scroll down to see the fee <i class="fa fa-arrow-down"></i></p>
                    </div>
                </div>
                <?php } ?>
            <?php } ?>
        
            <?php if (!empty($array_of_dis_programme_partner_schools)) { ?>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <input type="hidden" name="partnered_programme" value="<?php echo $dis_programme_id; ?>">
                        
                        <select name="my_partner_school" class= "form-select rounded-8" id="my_partner_school" required>
                            <option value="">Choose a Partner School</option>
                            <?php foreach($array_of_dis_programme_partner_schools as $key => $value) { ?>
                                <option value="<?php echo $key ?>"<?php if ($_POST['my_partner_school'] == $key) echo ' selected="selected"' ?>><?php echo ucfirst($value); ?></option>
                            <?php } ?>
                        </select>
                        <label for="my_partner_school">Choose a Partner School</label>
                        <?php if (isset($form_errors['my_partner_school'])) echo $form_errors['my_partner_school']; ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php if (!$already_existing_values) { ?>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="read_accept" required>
                <label class="form-check-label text-dark" for="read_accept">I have read and agree to the <a href="<?php echo $link_root_default.$location_slug."terms"; ?>" class="text-bold text-green" target="_blank">Terms and Conditions</a> and <a href="<?php echo $link_root_default.$location_slug."terms/privacy-policy"; ?>" target="_blank" class="text-bold text-green">Privacy Policy</a></label>
                <?php if (isset($form_errors['read_accept'])) echo $form_errors["read_accept"]; ?>
            </div>
        <?php } ?>

        <?php if ($enable_captcha_here) { ?>
            <div class="form-group">
                <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?php echo CAPTCHA_SITE_KEY; ?>"></div>
                <?php echo $form_errors["captcha"]; ?>
                <p id="myCaptchaError" class="text-danger"></p>
            </div>
        <?php } ?>

        <input type="hidden" name="branch" value="<?php echo $branch ?>" />
        <input type="hidden" name="xtr" value="<?php echo $to_email ?>" />
        <input type="hidden" name="url" value="<?php echo curPageURL() ?>" />

        <?php if ($custom_tracking == "wedigitalx") { ?>
            <input type="hidden" name="tracking_source" value="wedigitalx" />
        <?php } else { ?> 
            <?php if (isset($_GET['utm_source'])) { ?>
                <input type="hidden" name="tracking_source" value="<?php echo $_GET['utm_source'] ?>" />
            <?php } ?>
        <?php } ?> 
        
        <?php if (isset($_GET['utm_country'])) { ?>
            <input type="hidden" name="tracking_country" value="<?php echo $_GET['utm_country'] ?>" />
        <?php } ?>

        <?php if (isset($_GET['utm_campaign'])) { ?>
            <input type="hidden" name="tracking_campaign" value="<?php echo $_GET['utm_campaign'] ?>" />
        <?php } ?>

        <?php if ($track_univibes_click_id_for_coresite) { ?>
            <input type="hidden" name="univibes_click_id" value="<?php echo $track_univibes_click_id_for_coresite; ?>">
        <?php } ?>

        <div class="row">
            <div class="col-lg-4 offset-lg-8">
                <button class="btn btn-outline-main btn-custom btn-custom-size text-bold w-100">
                    Submit
                </button>
            </div>
        </div>
    <?php } ?>
</form>