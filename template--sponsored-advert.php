<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');
require_once(ROOT_PATH_TO_PORTAL . 'PHPMailer/PHPMailerAutoload.php');

$use_phpmailer = true;

require_once('inc.no-location-in-url.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize an array to store form errors
    $form_errors = [];

    // Get form data and sanitize inputs
    $first_name = htmlspecialchars($_POST['firstName']);
    $middle_name = htmlspecialchars($_POST['middleName']);
    $last_name = htmlspecialchars($_POST['lastName']);
    $gender = htmlspecialchars($_POST['gender']);
    $phone_number = htmlspecialchars($_POST['phoneNumber']);
    $email = htmlspecialchars($_POST['email']);
    $nationality = htmlspecialchars($_POST['nationality']);
    $referral = htmlspecialchars($_POST['referral']);

    // Perform validation
    if (empty($first_name)) {
        $form_errors[] = "First name is required.";
    }
    if (empty($last_name)) {
        $form_errors[] = "Last name is required.";
    }
    if (empty($gender)) {
        $form_errors[] = "Gender is required.";
    }
    if (empty($phone_number)) {
        $form_errors[] = "Phone number is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (empty($nationality)) {
        $form_errors[] = "Nationality is required.";
    }
    if (empty($referral)) {
        $form_errors[] = "Referral information is required.";
    }

    // If there are no errors, proceed with form processing
    if (empty($form_errors)) {
        // Here you can process the form data (e.g., save it to the database)
        mysqli_select_db($config, $database_config);
        $query_AccountExists = "SELECT us.login_id
                                    FROM users_students us 
                                    WHERE us.email = '" . sql_string($_POST['email']) . "' 
                                    LIMIT 1";
        $AccountExists = mysqli_query($config, $query_AccountExists) or die(mysqli_error($config));
        $row_AccountExists = mysqli_fetch_assoc($AccountExists);
        $totalRows_AccountExists = mysqli_num_rows($AccountExists);

        if ($totalRows_AccountExists) {
            $form_errors['email'] = '<p class="help-block text-danger">The email you submitted already has an account.</p>';
        }

        if ($site_version == "online") {
            $mailgun_validation_before_continuing = true;
        }

        if ($mailgun_validation_before_continuing) {
            // Perform email validation
            $email_to_validate = $_POST['email'];
            require('components/inc.mailgun-validation.php');
        }

        if ($email_address_is_bad) {
            if ($user_form_error_msg) {
                $form_errors['email'] = '<p class="help-block text-danger">' . $user_form_error_msg . '</p>';
            } else {
                $form_errors['email'] = '<p class="help-block text-danger">Invalid email.</p>';
            }
        }

        if (empty($form_errors)) {
            $all_went_well = false;

            mysqli_begin_transaction($config) or die(mysqli_error($config)); // Start transaction

            // Get the free course
            mysqli_select_db($config, $database_config);
            $query_LastInfo = "SELECT * 
                            FROM new_programmes np 
                            WHERE np.programme_id = 200 
                            ORDER BY np.programme_id DESC
                            LIMIT 1";
            $LastInfo = mysqli_query($config, $query_LastInfo) or die(mysqli_error($config));
            $row_LastInfo = mysqli_fetch_assoc($LastInfo);
            $totalRows_LastInfo = mysqli_num_rows($LastInfo);
            if ($row_LastInfo) {

                // Fetch the prog_date from programme_dates table
                $query_ProgDate = "SELECT entry_id, start_date, end_date  
                    FROM programme_dates 
                    WHERE programme_id = " . sql_int($row_LastInfo['programme_id']) . " 
                    LIMIT 1";
                $ProgDateResult = mysqli_query($config, $query_ProgDate) or die(mysqli_error($config));
                $row_ProgDate = mysqli_fetch_assoc($ProgDateResult);

                if ($row_ProgDate) {
                    $prog_date = sql_int($row_ProgDate['entry_id']);
                    $start_date = $row_ProgDate['start_date'];
                    $end_date = $row_ProgDate['end_date'];

                    // Create DateTime objects
                    $startDateTime = new DateTime($start_date);
                    $endDateTime = new DateTime($end_date);

                    // Calculate the duration
                    $duration = $startDateTime->diff($endDateTime);

                    // Generate new start date and end date starting from now
                    $currentDate = new DateTime();
                    $newStartDate = $currentDate->format('Y-m-d');
                    $newEndDate = $currentDate->add($duration)->format('Y-m-d');

                    $this_student_number = generate_random_student_number();

                    $query_Insert = "INSERT INTO prospects (
                        full_name, 
                        first_name, 
                        middle_name, 
                        last_name, 
                        email, 
                        phone, 
                        gender, 
                        nationality, 
                        source, 
                        marketer_id, 
                        prog_interest, 
                        prog_date, 
                        stage, 
                        entry_date,
                        sess_start_date, 
                        sess_end_date,  
                        study_center, 
                        branch, 
                        emp_company, 
                        emp_position, 
                        no_followup_emails, 
                        country_of_residence, 
                        student_number
                    ) 
                    VALUES (
                        '" . sql_string(trim($first_name . ' ' . $middle_name . ' ' . $last_name)) . "',  /* full_name */
                        '" . sql_string(trim($first_name)) . "',  /* first_name */
                        '" . sql_string(trim($middle_name)) . "',  /* middle_name */
                        '" . sql_string(trim($last_name)) . "',  /* last_name */
                        '" . sql_string($email) . "',  /* email */
                        '" . sql_string($phone_number) . "',  /* phone */
                        '" . sql_string($gender) . "',  /* gender */
                        '" . sql_string($nationality) . "',  /* nationality */
                        0,  /* source */
                        0,  /* marketer_id */
                        " . sql_int($row_LastInfo['programme_id']) . ",  /* prog_interest */
                        " . $prog_date . ",  /* prog_date */
                        21,  /* stage */
                        '" . sql_string($time_now) . "',  /* entry_date */
                        '" . $newStartDate . "', /* sess_start_date */
                        '" . $newEndDate . "', /* sess_end_date */
                        95,  /* study_center */
                        1,  /* branch */
                        '" . sql_string($_POST['company']) . "',  /* emp_company */
                        " . sql_int($_POST['position']) . ",  /* emp_position */
                        '1',  /* no_followup_emails */
                        " . sql_int($_POST['country_of_residence']) . ",  /* country_of_residence */
                        " . sql_int($this_student_number) . "  /* student_number */
                    )";
                    mysqli_select_db($config, $database_config);
                    $Insert = mysqli_query($config, $query_Insert) or die(mysqli_error($config));
                    $insertStatus1 = mysqli_affected_rows($config);

                    if ($insertStatus1 > 0) { // If INSERT is successful
                        // Get information about insert
                        $query_lastInsert = "SELECT p_id, full_name
                                                FROM prospects 
                                                WHERE email = '" . sql_string($_POST['email']) . "' 
                                                    AND prog_interest = '" . sql_int($row_LastInfo['programme_id']) . "' 
                                                    AND prog_date = '" . sql_int($prog_date) . "' 
                                                ORDER BY p_id DESC 
                                                LIMIT 1";
                        mysqli_select_db($config, $database_config);
                        $lastInsert = mysqli_query($config, $query_lastInsert) or die(mysqli_error($config));
                        $row_lastInsert = mysqli_fetch_assoc($lastInsert);
                        $totalRows_lastInsert = mysqli_num_rows($lastInsert);

                        if ($totalRows_lastInsert) {
                            $last_enrol_id = $row_lastInsert['p_id'];

                            // Create a user account for the student
                            $username = create_username(trim($row_lastInsert['full_name']));
                            $password = create_password();

                            mysqli_select_db($config, $database_config);
                            $insertSQL = "INSERT INTO users_students (username, password, default_password, email, status, last_enrol_id, modified_by, adsStatus, modified_date) 
                                            VALUES (
                                                '" . sql_string($username) . "', 
                                                '" . sql_string(sha1($password)) . "', 
                                                '" . sql_string($password) . "', 
                                                '" . sql_string($_POST['email']) . "', 
                                                'active',
                                                '" . sql_int($last_enrol_id) . "', 
                                                1,
                                                0, 
                                                '" . sql_string(date("Y-m-d H:i:s")) . "'
                                            )";
                            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                            $insertStatus2 = mysqli_affected_rows($config);

                            if ($insertStatus2 > 0) { // If INSERT is successful
                                $all_went_well = true;

                                // Get the login ID that was just created for the user
                                mysqli_select_db($config, $database_config);
                                $query_NuLogin = "SELECT login_id 
                                                    FROM users_students 
                                                    WHERE username = '" . sql_string($username) . "' 
                                                            AND default_password = '" . sql_string($password) . "' 
                                                    ORDER BY login_id DESC 
                                                    LIMIT 1";
                                $NuLogin = mysqli_query($config, $query_NuLogin) or die(mysqli_error($config));
                                $row_NuLogin = mysqli_fetch_assoc($NuLogin);
                                $totalRows_NuLogin = mysqli_num_rows($NuLogin);

                                if ($totalRows_NuLogin) {
                                    // Insert into users_students_prospect table
                                    $insertStudentProspectSQL = "INSERT INTO users_students_prospect (
                                        login_id,
                                        p_id,
                                        welcome_triggered,
                                        modified_date
                                    ) VALUES (
                                        " . sql_int($row_NuLogin['login_id']) . ",
                                        " . sql_int($last_enrol_id) . ",
                                        '1',
                                        '" . sql_string($time_now) . "'
                                    )";
                                    mysqli_query($config, $insertStudentProspectSQL) or die(mysqli_error($config));

                                    record_log('prospect', $sess_user_id, $row_NuLogin['login_id'], 'Created student login record');
                                }
                            }
                        }
                    }

                    if ($all_went_well) {
                        mysqli_commit($config) or die(mysqli_error($config));

                        $_SESSION['student_login_username'] = $_POST['email'];
                        $_SESSION['student_login_firstname'] = $_POST['first_name'];
                        $_SESSION['student_login_password'] = $password;

                        $_SESSION['userfeedback'] = "Thank you for signing up, " . $_SESSION['student_login_firstname'] . "! "
                            . "Your account has been created successfully. "
                            . "You will receive an email shortly with your login details. "
                            . "For your reference, your email address is " . $_SESSION['student_login_username'] . " and password: " . $_SESSION['student_login_password'];
                        $_SESSION['feedbacktype'] = "success";

                        // Construct the email message
                        $subject = "Welcome to LREA!";

                        // Load and replace placeholders in the email template
                        $template = file_get_contents('welcome_email_template.html');

                        $placeholders = array(
                            '{{full_name}}' => htmlspecialchars($row_lastInsert['full_name']),
                            '{{email}}' => htmlspecialchars($_SESSION['student_login_username']),
                            '{{password}}' => htmlspecialchars($_SESSION['student_login_password']),
                            '{{current_year}}' => date("Y")  // Add the current year placeholder
                        );

                        foreach ($placeholders as $placeholder => $value) {
                            $template = str_replace($placeholder, $value, $template);
                        }

                        // Send the email using PHPMailer
                        $mail = new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPAuth = true;
                        $mail->Host = 'mail.lrea.co.uk';
                        $mail->Port = 465;
                        $mail->SMTPSecure = 'ssl';
                        $mail->Username = 'no-reply@lrea.co.uk';
                        $mail->Password = 'SCqc2B4Vq({B';

                        // $mail->Host = 'sandbox.smtp.mailtrap.io';
                        // $mail->Port = 2525;
                        // $mail->SMTPSecure = 'TLS';
                        // $mail->Username = '011a587122008d';
                        // $mail->Password = '65b15e1a2f0e29';

                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

                        $mail->IsHTML(true);
                        $mail->SetFrom('no-reply@lrea.co.uk', 'LREA');
                        $mail->AddReplyTo('info@lrea.co.uk', 'LREA');
                        $mail->Subject = $subject;
                        $mail->MsgHTML($template);
                        $mail->AltBody = strip_tags($template);  // Plain text version for non-HTML email clients

                        $mail->AddAddress($_SESSION['student_login_username']);  // Send to the registered email

                        // Debugging
                        $mail->SMTPDebug = 2;  // Enable debugging (optional)
                        $mail->Debugoutput = 'html';  // Debug output in HTML

                        // Attempt to send the email
                        if (!$mail->Send()) {
                            // Handle email send failure (optional)
                            error_log("Failed to send registration email to " . $_SESSION['student_login_username']. "Mailer Error: " . $mail->ErrorInfo);
                        }
                    } else {
                        $_SESSION['userfeedback'] = "Failed to register. Please try again later.";
                        $_SESSION['feedbacktype'] = "error";

                        mysqli_rollback($config) or die(mysqli_error($config));
                    }
                } else {
                    $_SESSION['userfeedback'] = "Registration failed. Please try again later.";
                    $_SESSION['feedbacktype'] = "error";
                }
            } else {
                $_SESSION['userfeedback'] = "An errror occurred. Please try again later.";
                $_SESSION['feedbacktype'] = "error";
            }
        }
    }
}

$page_title = "Sponsored Advert";
require_once('components/doctype.inc.php');
?>

<style>
    /* Add some padding and margin for better readability */
    .container {
        padding: 20px;
    }

    .form-container {
        max-width: 700px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-image: linear-gradient(to right, #ea383e, #56d5c2);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px;
    }

    .form-label {
        font-weight: bold;
    }

    .btn {
        background-color: #ea383e;
        border: none;
        transition: background-color 0.3s ease;
        color: #fff !important;
    }

    .btn:hover {
        background-color: #000;
        color: #fff !important;
    }

    .form-control:focus {
        border-color: #ea383e;
        box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
    }
    .spinner-border {
        display: none;
    }
    .spinner-text {
        display: none;
    }
    .button-loading .button-text {
        display: none;
    }
    .button-loading .spinner-border,
    .button-loading .spinner-text {
        display: inline-block;
    }
    .spinner-text {
        margin-left: 10px;
    }
</style>

<?php include_once('components/end-of-head.inc.php'); ?>
</head>

<body>
    <?php include_once('components/beginning-of-body.inc.php'); ?>

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="card">
                    <div class="card-header text-center">
                        <h2 class="text-white"><i class="fas fa-graduation-cap"></i> Free Professional Certificate Course</h2>
                        <h5>In Sales Promotion and Management</h5>
                        <p>Sign Up Now and Get Access for Free!</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($form_errors)): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($form_errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php elseif (!empty($success_message)): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" id="registrationForm">
                            <!-- First Name -->
                            <div class="mb-3">
                                <label for="firstName" class="form-label"><i class="fas fa-user"></i> First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" placeholder="Enter your first name" required>
                            </div>
                            <!-- Middle Name -->
                            <div class="mb-3">
                                <label for="middleName" class="form-label"><i class="fas fa-user"></i> Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo isset($middle_name) ? htmlspecialchars($middle_name) : ''; ?>" placeholder="Enter your middle name">
                            </div>
                            <!-- Last Name -->
                            <div class="mb-3">
                                <label for="lastName" class="form-label"><i class="fas fa-user"></i> Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>" placeholder="Enter your last name" required>
                            </div>
                            <!-- Gender -->
                            <div class="mb-3">
                                <label for="gender" class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled <?php echo !isset($gender) ? 'selected' : ''; ?>>Select your gender</option>
                                    <option value="m" <?php echo isset($gender) && $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="f" <?php echo isset($gender) && $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>" placeholder="Enter your phone number" required>
                            </div>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="Enter your email" required>
                            </div>
                            <!-- Nationality -->
                            <div class="mb-3">
                                <label for="nationality" class="form-label"><i class="fas fa-globe"></i> Nationality</label>
                                <select type="text" class="form-control" id="nationality" name="nationality" required>
                                    <option value="">-- Select --</option>
                                    <?php
                                    // Fetch all countries from the database
                                    mysqli_select_db($config, $database_config);
                                    $query_Countries = "SELECT lc.countryID, lc.countryName FROM loccountries lc GROUP BY lc.countryID";
                                    $Countries = mysqli_query($config, $query_Countries) or die(mysqli_error($config));

                                    while ($row_Countries = mysqli_fetch_assoc($Countries)) {
                                        // Check if this countryID matches the previously submitted value
                                        $selected = (isset($nationality) && $nationality == $row_Countries['countryID']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row_Countries['countryID']) . '" ' . $selected . '>' . htmlspecialchars($row_Countries['countryName']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- How Did You Hear About Us -->
                            <div class="mb-3">
                                <label for="referral" class="form-label"><i class="fas fa-info-circle"></i> How Did You Hear About Us?</label>
                                <input type="text" class="form-control" id="referral" name="referral" value="<?php echo isset($referral) ? htmlspecialchars($referral) : ''; ?>" placeholder="E.g., Social Media, Friend, Website" required>
                            </div>
                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-lg" id="submitButton">
                                    <i class="fas fa-paper-plane"></i>
                                    <span class="button-text">Register</span>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span class="spinner-text">Please wait...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /Main Content -->

    <?php require_once('components/footer.inc.php'); ?>

    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            var button = document.getElementById('submitButton');
            button.classList.add('button-loading');
            button.disabled = true;
        });
    </script>
</body>

</html>