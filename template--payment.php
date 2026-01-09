<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

require_once(ROOT_PATH_TO_PORTAL . 'PHPMailer/PHPMailerAutoload.php');

// Paystack public key
$paystack_public_key = PAYSTACK_PUBLIC_KEY; // Replace with your actual public key
$paystack_secret_key = PAYSTACK_SECRET_KEY;
$stripe_public_key = STRIPE_PUBLIC_KEY;

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_begin_transaction($config) or die(mysqli_error($config)); // Start transaction

    $all_went_well = false;
    // $time_now = date('Y-m-d H:i:s');
    // $date_now = date('Y-m-d');

    // Get form data and sanitize inputs
    $first_name = htmlspecialchars($_POST['firstName']);
    $middle_name = htmlspecialchars($_POST['middleName']);
    $last_name = htmlspecialchars($_POST['lastName']);
    $gender = htmlspecialchars($_POST['gender']);
    $phone_number = htmlspecialchars($_POST['phoneNumber']);
    $email = htmlspecialchars($_POST['email']);
    $nationality = htmlspecialchars($_POST['nationality']);
    $referral = htmlspecialchars($_POST['referral']);
    $currency = htmlspecialchars($_POST['currency']);

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
            // Verify the payment with Paystack
            if (isset($_POST['reference'])) {
                if ($currency == 'naira') {
                    $reference = $_POST['reference'];

                    // Verify the payment on the server-side
                    $url = "https://api.paystack.co/transaction/verify/$reference";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $paystack_secret_key",
                        "Cache-Control: no-cache",
                    ));
                    $response = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else {
                        $result = json_decode($response, true);
                        $sub_id = 2;

                        if ($result['status'] && $result['data']['status'] === 'success') {
                            $amount = $result['data']['amount'] / 100; // Amount in naira
                            $currency = $result['data']['currency'];
                            $email = $result['data']['customer']['email'];
                            $status = $result['data']['status'];

                            // Validate and map status
                            $valid_statuses = ['pending', 'success', 'failed'];
                            if (!in_array($status, $valid_statuses)) {
                                $status = 'failed'; // Default or error status
                            }

                            if ($status = 'success') {
                                $query_payment_Insert = "INSERT INTO payments (
                                    reference, 
                                    amount, 
                                    currency, 
                                    email, 
                                    status
                                ) 
                                VALUES (
                                    '" . sql_string($reference) . "', 
                                    '" . sql_string($amount) . "', 
                                    '" . sql_string($currency) . "', 
                                    '" . sql_string($email) . "', 
                                    '" . sql_string($status) . "'
                                )";
                                mysqli_select_db($config, $database_config);
                                $Insert = mysqli_query($config, $query_payment_Insert) or die(mysqli_error($config));
                                $insertPaymentStatus = mysqli_affected_rows($config);

                                if ($insertPaymentStatus > 0) { // If INSERT is successful
                                    // Create a user account for the student
                                    $username = create_username(trim($first_name . ' ' . $middle_name . ' ' . $last_name));
                                    $password = create_password();

                                    mysqli_select_db($config, $database_config);
                                    $insertSQL = "INSERT INTO users_students (username, password, default_password, email, status, last_enrol_id, modified_by, modified_date) 
                                                    VALUES (
                                                        '" . sql_string($username) . "', 
                                                        '" . sql_string(sha1($password)) . "', 
                                                        '" . sql_string($password) . "', 
                                                        '" . sql_string($_POST['email']) . "', 
                                                        'active',
                                                        '" . sql_int($last_enrol_id) . "', 
                                                        1,
                                                        '" . sql_string(date("Y-m-d H:i:s")) . "'
                                                    )";
                                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                                    $insertStatus2 = mysqli_affected_rows($config);

                                    if ($insertStatus2 > 0) { // If INSERT is successful
                                        // Query to check if the email is an active student of the institution and if is already enrolled in this subscription
                                        mysqli_select_db($config, $database_config);
                                        $query_contextClientInfo = "SELECT us.login_id, u_sub.sub_id
                                                            FROM users_students us
                                                            LEFT JOIN users_subscriptions u_sub ON u_sub.login_id = us.login_id AND u_sub.sub_id = '" . sql_int($sub_id) . "'
                                                            WHERE us.email = '" . sql_string($email) . "' 
                                                                AND us.status = 'active'
                                                            LIMIT 1";
                                        $contextClientInfo = mysqli_query($config, $query_contextClientInfo) or die(mysqli_error($config));
                                        $row_contextClientInfo = mysqli_fetch_assoc($contextClientInfo);
                                        $totalRows_contextClientInfo = mysqli_num_rows($contextClientInfo);

                                        if ($totalRows_contextClientInfo) {
                                            // Locate the subscription details (duration)
                                            mysqli_select_db($config, $database_config);
                                            $query_thisSubscriptionDetails = "SELECT duration  
                                                                            FROM subscriptions
                                                                            WHERE sub_id = '" . sql_int($sub_id) . "' 
                                                                                AND status = 'active' 
                                                                            LIMIT 1";
                                            $thisSubscriptionDetails = mysqli_query($config, $query_thisSubscriptionDetails) or die(mysqli_error($config));
                                            $row_thisSubscriptionDetails = mysqli_fetch_assoc($thisSubscriptionDetails);
                                            $totalRows_thisSubscriptionDetails = mysqli_num_rows($thisSubscriptionDetails);

                                            if ($totalRows_thisSubscriptionDetails) {
                                                $duration = $row_thisSubscriptionDetails['duration'];
                                                $subscription_end_date = date('Y-m-d', strtotime("+$duration"));

                                                // Check if the record already exists
                                                mysqli_select_db($config, $database_config);
                                                $query_CheckExisting = "SELECT * 
                                                                        FROM users_subscriptions 
                                                                        WHERE sub_id = " . sql_int($sub_id) . " 
                                                                        AND login_id = " . sql_int($row_contextClientInfo['login_id']);
                                                $CheckExistingResult = mysqli_query($config, $query_CheckExisting) or die(mysqli_error($config));
                                                $insertEStatus = mysqli_affected_rows($config);

                                                if (mysqli_num_rows($CheckExistingResult) == 0) {
                                                    // Record does not exist, proceed with the insert
                                                    $insertSQL = "INSERT INTO users_subscriptions (
                                                                    sub_id, 
                                                                    login_id, 
                                                                    sub_status, 
                                                                    start_date, 
                                                                    end_date, 
                                                                    added_by, 
                                                                    date_added
                                                                ) VALUES (
                                                                    " . sql_int($sub_id) . ",
                                                                    " . sql_int($row_contextClientInfo['login_id']) . ",
                                                                    'active',
                                                                    '" . sql_string($date_now) . "',
                                                                    '" . sql_string($subscription_end_date) . "',
                                                                    " . sql_int($sess_user_id) . ",
                                                                    '" . sql_string($time_now) . "'
                                                                )";
                                                    $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                                                    $insertStatus = mysqli_affected_rows($config);
                                                } else {
                                                    $insertStatus = $insertEStatus;
                                                }

                                                if ($insertStatus > 0) { // If INSERT is successful
                                                    // Get all active programs of this subscription that have a module
                                                    $query_SubscriptionProgrammes = "SELECT sp.*
                                                                FROM subscriptions_programmes sp
                                                                JOIN modules m ON sp.prog_id = m.prog_id
                                                                JOIN new_programmes np ON sp.prog_id = np.programme_id
                                                                WHERE sp.sub_id = " . sql_int($sub_id) . " 
                                                                AND np.programme_status = 'active'";
                                                    $SubscriptionProgrammes = mysqli_query($config, $query_SubscriptionProgrammes) or die(mysqli_error($config));
                                                    $row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes);
                                                    $totalRows_SubscriptionProgrammes = mysqli_num_rows($SubscriptionProgrammes);

                                                    if ($totalRows_SubscriptionProgrammes) {
                                                        do {
                                                            // Check if the program already exists for the student
                                                            $query_ExistingProgram = "SELECT * 
                                                                                        FROM users_students_prospect usp
                                                                                        JOIN prospects p ON usp.p_id = p.p_id
                                                                                        WHERE usp.login_id = " . sql_int($row_contextClientInfo['login_id']) . "
                                                                                        AND p.prog_interest = " . sql_int($row_SubscriptionProgrammes['prog_id']);
                                                            $ExistingProgram = mysqli_query($config, $query_ExistingProgram) or die(mysqli_error($config));
                                                            $totalRows_ExistingProgram = mysqli_num_rows($ExistingProgram);

                                                            if ($totalRows_ExistingProgram == 0) {
                                                                // Fetch the prog_date from programme_dates table
                                                                $query_ProgDate = "SELECT entry_id, start_date, end_date  
                                                                                    FROM programme_dates 
                                                                                    WHERE programme_id = " . sql_int($row_SubscriptionProgrammes['prog_id']) . " 
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

                                                                    // Insert into prospects table
                                                                    $query_InsertProspect = "INSERT INTO prospects (
                                                                        stage, 
                                                                        full_name, 
                                                                        first_name,
                                                                        middle_name, 
                                                                        last_name, 
                                                                        email, 
                                                                        phone, 
                                                                        gender, 
                                                                        nationality, 
                                                                        prog_interest, 
                                                                        prog_date, 
                                                                        source, 
                                                                        entry_date, 
                                                                        marketer_id, 
                                                                        sess_start_date, 
                                                                        sess_end_date, 
                                                                        study_center, 
                                                                        branch, 
                                                                        no_followup_emails, 
                                                                        student_number
                                                                    ) VALUES (
                                                                        21,
                                                                        '" . sql_string(trim($first_name . ' ' . $middle_name . ' ' . $last_name)) . "',  /* full_name */
                                                                        '" . sql_string(trim($first_name)) . "',  /* first_name */
                                                                        '" . sql_string(trim($middle_name)) . "',  /* middle_name */
                                                                        '" . sql_string(trim($last_name)) . "',  /* last_name */
                                                                        '" . sql_string($email) . "',
                                                                        '" . sql_string($phone_number) . "',  /* phone */
                                                                        '" . sql_string($gender) . "',  /* gender */
                                                                        '" . sql_string($nationality) . "',  /* nationality */
                                                                        " . sql_int($row_SubscriptionProgrammes['prog_id']) . ",
                                                                        " . $prog_date . ",
                                                                        0,
                                                                        '" . sql_string($time_now) . "',
                                                                        0,
                                                                        '" . $newStartDate . "', /* sess_start_date */
                                                                        '" . $newEndDate . "', /* sess_end_date */
                                                                        95,
                                                                        1,
                                                                        '1',
                                                                        " . sql_int($this_student_number) . "
                                                                    )";
                                                                    $InsertProspect = mysqli_query($config, $query_InsertProspect) or die(mysqli_error($config));
                                                                    $prospectId = mysqli_insert_id($config);

                                                                    // Insert into users_students_prospect table
                                                                    $insertStudentProspectSQL = "INSERT INTO users_students_prospect (
                                                                                                    login_id,
                                                                                                    p_id,
                                                                                                    welcome_triggered,
                                                                                                    modified_date
                                                                                                ) VALUES (
                                                                                                    " . sql_int($row_contextClientInfo['login_id']) . ",
                                                                                                    " . sql_int($prospectId) . ",
                                                                                                    '1',
                                                                                                    '" . sql_string($time_now) . "'
                                                                                                )";
                                                                    mysqli_query($config, $insertStudentProspectSQL) or die(mysqli_error($config));
                                                                }
                                                            }
                                                        } while ($row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes));
                                                    }

                                                    $query_lastInsert = "SELECT p_id, full_name, email
                                                                        FROM prospects 
                                                                        WHERE email = '" . sql_string($email) . "' 
                                                                        ORDER BY p_id DESC 
                                                                        LIMIT 1";

                                                    mysqli_select_db($config, $database_config);
                                                    $lastInsert = mysqli_query($config, $query_lastInsert) or die(mysqli_error($config));
                                                    $row_lastInsert = mysqli_fetch_assoc($lastInsert);
                                                    $totalRows_lastInsert = mysqli_num_rows($lastInsert);

                                                    if ($totalRows_lastInsert) {
                                                        // Make sure you sanitize the input
                                                        $email_safe = sql_string($email);

                                                        // Construct the email message
                                                        $subject = "Welcome to London Real Estate Academy!";

                                                        // Load and replace placeholders in the email template
                                                        $template = file_get_contents('welcome_payment_template.html');

                                                        // Step 1: Get the current date
                                                        $now = new DateTime();  // Get the current date

                                                        // Step 2: Format the current date (e.g., "SEPTEMBER 15TH, 2024")
                                                        $formattedCurrentDate = strtoupper($now->format('F jS, Y'));  // e.g., "SEPTEMBER 15TH, 2024"

                                                        // Step 3: Add it to the placeholders
                                                        $placeholders = array(
                                                            '{{full_name}}' => htmlspecialchars($row_lastInsert['full_name']),
                                                            '{{email}}' => htmlspecialchars($email_safe),
                                                            '{{password}}' => htmlspecialchars($password),
                                                            '{{current_year}}' => date("Y"),
                                                            '{{start_date}}' => $formattedCurrentDate  // Add the formatted start date
                                                        );

                                                        foreach ($placeholders as $placeholder => $value) {
                                                            $template = str_replace($placeholder, $value, $template);
                                                        }

                                                        // Send the email using PHPMailer
                                                        $mail = new PHPMailer();
                                                        $mail->SMTPDebug = 0;
                                                        $mail->IsSMTP();
                                                        $mail->SMTPAuth = true;
                                                        $mail->Host = MAILGUN_HOST_NAME;
                                                        $mail->Port = MAILGUN_PORT;
                                                        $mail->SMTPSecure = MAILGUN_ENCRYPTION;
                                                        $mail->Username = MAILGUN_STAFF_EMAILS_USERNAME;
                                                        $mail->Password = MAILGUN_STAFF_EMAILS_PASSWORD;


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
                                                        $mail->AltBody = $template;  // Plain text version for non-HTML email clients

                                                        $mail->AddAddress($email_safe);  // Send to the registered email

                                                        // Attempt to send the email
                                                        if (!$mail->Send()) {
                                                            // Handle email send failure (optional)
                                                            error_log("Failed to send registration email to " . $email_safe . ". Error: " . $mail->ErrorInfo);
                                                        }

                                                        $all_went_well = true;
                                                    }
                                                } else {
                                                    $_SESSION['userfeedback'] = "We encountered an issue while processing your payment. Please reach out to the site administrator for assistance.";
                                                    $_SESSION['feedbacktype'] = "error";
                                                }
                                            } else {
                                                $_SESSION['userfeedback'] = "We encountered an issue while processing your payment. Please reach out to the site administrator for assistance.";
                                                $_SESSION['feedbacktype'] = "error";
                                            }
                                        } else {
                                            $_SESSION['userfeedback'] = "Student has already been enrolled in a subscription.";
                                            $_SESSION['feedbacktype'] = "error";
                                        }
                                    }
                                } else {
                                    // Failure: handle the insert failure case
                                    $_SESSION['userfeedback'] = "We encountered an issue while processing your payment. Please reach out to the site administrator for assistance.";
                                    $_SESSION['feedbacktype'] = "error";
                                }
                            }

                            if ($all_went_well) {
                                // mysqli_commit($config) or die(mysqli_error($config));

                                $_SESSION['userfeedback'] = "Your payment was successful, and you are now a member! Your login details have been sent to your email. Please log in to your dashboard to access your courses and exclusive member benefits. If you need assistance, feel free to contact us.";
                                $_SESSION['feedbacktype'] = "success";
                            } else {
                                $_SESSION['userfeedback'] = "Payment verification failed. Please try again or contact the site administrator if the issue persists.";
                                $_SESSION['feedbacktype'] = "error";

                                mysqli_rollback($config) or die(mysqli_error($config));
                            }
                        } else {
                            $form_errors[] = "Payment verification failed!";
                        }
                    }
                } else {
                    $reference = $_POST['reference'];

                    $result = json_decode($response, true);
                    $sub_id = 2;

                    $amount =  $_POST['amount']; // Amount in naira
                    $status = 'success';
                    $currency = 'GBP';


                    if ($status = 'success') {
                        $query_payment_Insert = "INSERT INTO payments (
                            reference, 
                            amount, 
                            currency, 
                            email, 
                            status
                        ) 
                        VALUES (
                            '" . sql_string($reference) . "', 
                            '" . sql_string($amount) . "', 
                            '" . sql_string($currency) . "', 
                            '" . sql_string($email) . "', 
                            '" . sql_string($status) . "'
                        )";
                        mysqli_select_db($config, $database_config);
                        $Insert = mysqli_query($config, $query_payment_Insert) or die(mysqli_error($config));
                        $insertPaymentStatus = mysqli_affected_rows($config);

                        if ($insertPaymentStatus > 0) { // If INSERT is successful
                            // Create a user account for the student
                            $username = create_username(trim($first_name . ' ' . $middle_name . ' ' . $last_name));
                            $password = create_password();

                            mysqli_select_db($config, $database_config);
                            $insertSQL = "INSERT INTO users_students (username, password, default_password, email, status, last_enrol_id, modified_by, modified_date) 
                                            VALUES (
                                                '" . sql_string($username) . "', 
                                                '" . sql_string(sha1($password)) . "', 
                                                '" . sql_string($password) . "', 
                                                '" . sql_string($_POST['email']) . "', 
                                                'active',
                                                '" . sql_int($last_enrol_id) . "', 
                                                1,
                                                '" . sql_string(date("Y-m-d H:i:s")) . "'
                                            )";
                            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                            $insertStatus2 = mysqli_affected_rows($config);

                            if ($insertStatus2 > 0) { // If INSERT is successful
                                // Query to check if the email is an active student of the institution and if is already enrolled in this subscription
                                mysqli_select_db($config, $database_config);
                                $query_contextClientInfo = "SELECT us.login_id, u_sub.sub_id
                                                    FROM users_students us
                                                    LEFT JOIN users_subscriptions u_sub ON u_sub.login_id = us.login_id AND u_sub.sub_id = '" . sql_int($sub_id) . "'
                                                    WHERE us.email = '" . sql_string($email) . "' 
                                                        AND us.status = 'active'
                                                    LIMIT 1";
                                $contextClientInfo = mysqli_query($config, $query_contextClientInfo) or die(mysqli_error($config));
                                $row_contextClientInfo = mysqli_fetch_assoc($contextClientInfo);
                                $totalRows_contextClientInfo = mysqli_num_rows($contextClientInfo);

                                if ($totalRows_contextClientInfo) {
                                    // Locate the subscription details (duration)
                                    mysqli_select_db($config, $database_config);
                                    $query_thisSubscriptionDetails = "SELECT duration  
                                                                    FROM subscriptions
                                                                    WHERE sub_id = '" . sql_int($sub_id) . "' 
                                                                        AND status = 'active' 
                                                                    LIMIT 1";
                                    $thisSubscriptionDetails = mysqli_query($config, $query_thisSubscriptionDetails) or die(mysqli_error($config));
                                    $row_thisSubscriptionDetails = mysqli_fetch_assoc($thisSubscriptionDetails);
                                    $totalRows_thisSubscriptionDetails = mysqli_num_rows($thisSubscriptionDetails);

                                    if ($totalRows_thisSubscriptionDetails) {
                                        $duration = $row_thisSubscriptionDetails['duration'];
                                        $subscription_end_date = date('Y-m-d', strtotime("+$duration"));

                                        // Check if the record already exists
                                        mysqli_select_db($config, $database_config);
                                        $query_CheckExisting = "SELECT * 
                                                                FROM users_subscriptions 
                                                                WHERE sub_id = " . sql_int($sub_id) . " 
                                                                AND login_id = " . sql_int($row_contextClientInfo['login_id']);
                                        $CheckExistingResult = mysqli_query($config, $query_CheckExisting) or die(mysqli_error($config));
                                        $insertEStatus = mysqli_affected_rows($config);

                                        if (mysqli_num_rows($CheckExistingResult) == 0) {
                                            // Record does not exist, proceed with the insert
                                            $insertSQL = "INSERT INTO users_subscriptions (
                                                            sub_id, 
                                                            login_id, 
                                                            sub_status, 
                                                            start_date, 
                                                            end_date, 
                                                            added_by, 
                                                            date_added
                                                        ) VALUES (
                                                            " . sql_int($sub_id) . ",
                                                            " . sql_int($row_contextClientInfo['login_id']) . ",
                                                            'active',
                                                            '" . sql_string($date_now) . "',
                                                            '" . sql_string($subscription_end_date) . "',
                                                            " . sql_int($sess_user_id) . ",
                                                            '" . sql_string($time_now) . "'
                                                        )";
                                            $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                                            $insertStatus = mysqli_affected_rows($config);
                                        } else {
                                            $insertStatus = $insertEStatus;
                                        }

                                        if ($insertStatus > 0) { // If INSERT is successful
                                            // Get all active programs of this subscription that have a module
                                            $query_SubscriptionProgrammes = "SELECT sp.*
                                                        FROM subscriptions_programmes sp
                                                        JOIN modules m ON sp.prog_id = m.prog_id
                                                        JOIN new_programmes np ON sp.prog_id = np.programme_id
                                                        WHERE sp.sub_id = " . sql_int($sub_id) . " 
                                                        AND np.programme_status = 'active'";
                                            $SubscriptionProgrammes = mysqli_query($config, $query_SubscriptionProgrammes) or die(mysqli_error($config));
                                            $row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes);
                                            $totalRows_SubscriptionProgrammes = mysqli_num_rows($SubscriptionProgrammes);

                                            if ($totalRows_SubscriptionProgrammes) {
                                                do {
                                                    // Check if the program already exists for the student
                                                    $query_ExistingProgram = "SELECT * 
                                                                                FROM users_students_prospect usp
                                                                                JOIN prospects p ON usp.p_id = p.p_id
                                                                                WHERE usp.login_id = " . sql_int($row_contextClientInfo['login_id']) . "
                                                                                AND p.prog_interest = " . sql_int($row_SubscriptionProgrammes['prog_id']);
                                                    $ExistingProgram = mysqli_query($config, $query_ExistingProgram) or die(mysqli_error($config));
                                                    $totalRows_ExistingProgram = mysqli_num_rows($ExistingProgram);

                                                    if ($totalRows_ExistingProgram == 0) {
                                                        // Fetch the prog_date from programme_dates table
                                                        $query_ProgDate = "SELECT entry_id, start_date, end_date  
                                                                            FROM programme_dates 
                                                                            WHERE programme_id = " . sql_int($row_SubscriptionProgrammes['prog_id']) . " 
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

                                                            // Insert into prospects table
                                                            $query_InsertProspect = "INSERT INTO prospects (
                                                                stage, 
                                                                full_name, 
                                                                first_name,
                                                                middle_name, 
                                                                last_name, 
                                                                email, 
                                                                phone, 
                                                                gender, 
                                                                nationality, 
                                                                prog_interest, 
                                                                prog_date, 
                                                                source, 
                                                                entry_date, 
                                                                marketer_id, 
                                                                sess_start_date, 
                                                                sess_end_date, 
                                                                study_center, 
                                                                branch, 
                                                                no_followup_emails, 
                                                                student_number
                                                            ) VALUES (
                                                                21,
                                                                '" . sql_string(trim($first_name . ' ' . $middle_name . ' ' . $last_name)) . "',  /* full_name */
                                                                '" . sql_string(trim($first_name)) . "',  /* first_name */
                                                                '" . sql_string(trim($middle_name)) . "',  /* middle_name */
                                                                '" . sql_string(trim($last_name)) . "',  /* last_name */
                                                                '" . sql_string($email) . "',
                                                                '" . sql_string($phone_number) . "',  /* phone */
                                                                '" . sql_string($gender) . "',  /* gender */
                                                                '" . sql_string($nationality) . "',  /* nationality */
                                                                " . sql_int($row_SubscriptionProgrammes['prog_id']) . ",
                                                                " . $prog_date . ",
                                                                0,
                                                                '" . sql_string($time_now) . "',
                                                                0,
                                                                '" . $newStartDate . "', /* sess_start_date */
                                                                '" . $newEndDate . "', /* sess_end_date */
                                                                95,
                                                                1,
                                                                '1',
                                                                " . sql_int($this_student_number) . "
                                                            )";
                                                            $InsertProspect = mysqli_query($config, $query_InsertProspect) or die(mysqli_error($config));
                                                            $prospectId = mysqli_insert_id($config);

                                                            // Insert into users_students_prospect table
                                                            $insertStudentProspectSQL = "INSERT INTO users_students_prospect (
                                                                                            login_id,
                                                                                            p_id,
                                                                                            welcome_triggered,
                                                                                            modified_date
                                                                                        ) VALUES (
                                                                                            " . sql_int($row_contextClientInfo['login_id']) . ",
                                                                                            " . sql_int($prospectId) . ",
                                                                                            '1',
                                                                                            '" . sql_string($time_now) . "'
                                                                                        )";
                                                            mysqli_query($config, $insertStudentProspectSQL) or die(mysqli_error($config));
                                                        }
                                                    }
                                                } while ($row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes));
                                            }

                                            $query_lastInsert = "SELECT p_id, full_name, email
                                                                FROM prospects 
                                                                WHERE email = '" . sql_string($email) . "' 
                                                                ORDER BY p_id DESC 
                                                                LIMIT 1";

                                            mysqli_select_db($config, $database_config);
                                            $lastInsert = mysqli_query($config, $query_lastInsert) or die(mysqli_error($config));
                                            $row_lastInsert = mysqli_fetch_assoc($lastInsert);
                                            $totalRows_lastInsert = mysqli_num_rows($lastInsert);

                                            if ($totalRows_lastInsert) {
                                                // Make sure you sanitize the input
                                                $email_safe = sql_string($email);

                                                // Construct the email message
                                                $subject = "Welcome to London Real Estate Academy!";

                                                // Load and replace placeholders in the email template
                                                $template = file_get_contents('welcome_payment_template.html');

                                                // Step 1: Get the current date
                                                $now = new DateTime();  // Get the current date

                                                // Step 2: Format the current date (e.g., "SEPTEMBER 15TH, 2024")
                                                $formattedCurrentDate = strtoupper($now->format('F jS, Y'));  // e.g., "SEPTEMBER 15TH, 2024"

                                                // Step 3: Add it to the placeholders
                                                $placeholders = array(
                                                    '{{full_name}}' => htmlspecialchars($row_lastInsert['full_name']),
                                                    '{{email}}' => htmlspecialchars($email_safe),
                                                    '{{password}}' => htmlspecialchars($password),
                                                    '{{current_year}}' => date("Y"),
                                                    '{{start_date}}' => $formattedCurrentDate  // Add the formatted start date
                                                );

                                                foreach ($placeholders as $placeholder => $value) {
                                                    $template = str_replace($placeholder, $value, $template);
                                                }

                                                // Send the email using PHPMailer
                                                $mail = new PHPMailer();
                                                $mail->SMTPDebug = 0;
                                                $mail->IsSMTP();
                                                $mail->SMTPAuth = true;
                                                $mail->Host = MAILGUN_HOST_NAME;
                                                $mail->Port = MAILGUN_PORT;
                                                $mail->SMTPSecure = MAILGUN_ENCRYPTION;
                                                $mail->Username = MAILGUN_STAFF_EMAILS_USERNAME;
                                                $mail->Password = MAILGUN_STAFF_EMAILS_PASSWORD;


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
                                                $mail->AltBody = $template;  // Plain text version for non-HTML email clients

                                                $mail->AddAddress($email_safe);  // Send to the registered email

                                                // Attempt to send the email
                                                if (!$mail->Send()) {
                                                    // Handle email send failure (optional)
                                                    error_log("Failed to send registration email to " . $email_safe . ". Error: " . $mail->ErrorInfo);
                                                }

                                                $all_went_well = true;
                                            }
                                        } else {
                                            $_SESSION['userfeedback'] = "We encountered an issue while processing your payment. Please reach out to the site administrator for assistance.";
                                            $_SESSION['feedbacktype'] = "error";
                                        }
                                    } else {
                                        $_SESSION['userfeedback'] = "We encountered an issue while processing your payment. Please reach out to the site administrator for assistance.";
                                        $_SESSION['feedbacktype'] = "error";
                                    }
                                } else {
                                    $_SESSION['userfeedback'] = "Student has already been enrolled in a subscription.";
                                    $_SESSION['feedbacktype'] = "error";
                                }
                            }
                        } else {
                            // Failure: handle the insert failure case
                            $_SESSION['userfeedback'] = "We encountered an issue while processing your payment. Please reach out to the site administrator for assistance.";
                            $_SESSION['feedbacktype'] = "error";
                        }
                    }

                    if ($all_went_well) {
                        mysqli_commit($config) or die(mysqli_error($config));

                        $_SESSION['userfeedback'] = "Your payment was successful, and you are now a member! Your login details have been sent to your email. Please log in to your dashboard to access your courses and exclusive member benefits. If you need assistance, feel free to contact us.";
                        $_SESSION['feedbacktype'] = "success";
                    } else {
                        $_SESSION['userfeedback'] = "Payment verification failed. Please try again or contact the site administrator if the issue persists.";
                        $_SESSION['feedbacktype'] = "error";

                        mysqli_rollback($config) or die(mysqli_error($config));
                    }
                }
            }
        }
    }
}

$page_title = SITE_NAME . ' Payment Information';
require_once('components/doctype.inc.php');
?>

<style>
    /* Add some padding and margin for better readability */
    .container {
        padding: 20px;
    }

    .card-body {
        padding: 20px;
    }

    .border-bottom {
        border-bottom: 1px solid #ddd;
    }

    .bold-font {
        font-weight: bold;
    }

    .text-green {
        color: #ea383e;
    }

    /* Animation for fadeIn */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .animated {
        animation-duration: 1s;
        animation-fill-mode: both;
    }

    .fadeIn {
        animation-name: fadeIn;
    }

    .card-header {
        /* background-image: linear-gradient(to right, #ea383e, #56d5c2); */
        background-color: #000;
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

    .is-invalid {
        border-color: #dc3545;
    }

    #stripePayment {
        background-color: #f9f9f9;
        /* Light background */
        border-radius: 8px;
        /* Rounded corners */
        padding: 20px;
        /* Spacing inside the card */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        /* Subtle shadow */
    }

    #stripePayment .mb-3 {
        margin-bottom: 15px;
        /* Space between fields */
    }

    #card-element {
        background-color: white;
        /* Card input background */
        border: 1px solid #ddd;
        /* Border for card input */
        border-radius: 4px;
        /* Rounded corners */
        padding: 10px;
        /* Padding inside the input */
        transition: border 0.3s ease;
        /* Smooth border transition */
    }

    #card-element:focus {
        border-color: #007bff;
        /* Border color on focus */
        outline: none;
        /* Remove default outline */
    }

    .invalid-feedback {
        display: none;
        /* Hide initially */
        color: #dc3545;
        /* Bootstrap danger color */
    }
</style>

<?php include_once('components/end-of-head.inc.php'); ?>
</head>

<body>
    <?php include_once('components/beginning-of-body.inc.php'); ?>

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>

    <div class="main-content">

        <div class="container my-5 px-4">
            <section>
                <div class="container">
                    <div class="row no-gutters border shadow-sm rounded-9">
                        <!-- Bank Information Section -->
                        <div class="card border-0 col-lg-6 bg-light">
                            <!-- <h4 class="text-center mt-4">Bank Information</h4> -->
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-8 row">
                                        <div class="col-12 d-flex justify-content-center">
                                            <img class="fit-image" src="<?php echo ROOT_DIR; ?>images/transfer-3.svg" alt="payment confirmed">
                                        </div>
                                        <!-- Currency Dropdown -->
                                        <div class="col-12 d-flex flex-column text-center" style="margin: 6rem 0;">
                                            <label for="currencySelector" class="form-label" style="font-size: 1.2rem;">Select Currency:</label>
                                            <select id="currencySelector" class="form-control" onchange="updateCurrency()">
                                                <option value="pounds" selected>Pounds (£)</option>
                                                <option value="naira">Naira (₦)</option>
                                                <option value="dollars">Dollars ($)</option>
                                            </select>
                                        </div>
                                        <h1 id="paymentAmount" class="text-center mt-5 mb-5">£150</h1>
                                        <h3 class="text-center">One-Year Subscription Fee</h3>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <!-- <div class="row my-5 pt-4 text-center border-top">
                                    <div class="col-12 mb-4">
                                        <h6>Account Name</h6>
                                        <h3 class="font-weight-bold">London Real Estate Academy</h3>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <h6>Account Number</h6>
                                        <h3 class="font-weight-bold">1304554697</h3>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <h6>Bank</h6>
                                        <h3 class="font-weight-bold">Providus Bank</h3>
                                    </div>
                                </div> -->
                            </div>
                        </div>

                        <!-- How To Pay Section -->
                        <div class="card border-0 col-lg-6 py-4 animated fadeIn">
                            <div class="card-header text-center">
                                <h2 class="text-white"><i class="fas fa-graduation-cap"></i> Sign Up to Become a Member</h2>
                            </div>

                            <div class="mb-3" style="display: flex; justify-content: flex-end;">
                                <a href="<?php echo $link_root_path; ?>landing" style="color: #c10000; text-decoration: none;">
                                    <label for="benefits" class="form-label">Membership Benefits </label>
                                    <span data-toggle="tooltip" data-placement="top" title="Click to see benefits">
                                        <i class="fas fa-info-circle" data-bs-toggle="modal" data-bs-target="#benefitsModal"></i>
                                    </span>
                                </a>
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

                                <form action="" method="POST" id="paymentForm">
                                    <input type="hidden" id="public_key" value="<?php echo $paystack_public_key; ?>">
                                    <input type="hidden" id="st_public_key" value="<?php echo $stripe_public_key; ?>">
                                    <input type="hidden" id="amount" name="amount">
                                    <input type="hidden" id="currency" name="currency">
                                    <input type="hidden" id="reference" name="reference" required> <!-- Reference will be set by JS after payment -->
                                    <!-- First Name -->
                                    <div class="mb-3">
                                        <label for="firstName" class="form-label"><i class="fas fa-user"></i> First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" placeholder="Enter your first name" required>
                                        <div class="invalid-feedback">Please enter first name</div>
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
                                        <div class="invalid-feedback">Please enter last name</div>
                                    </div>
                                    <!-- Gender -->
                                    <div class="mb-3">
                                        <label for="gender" class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="" disabled <?php echo !isset($gender) ? 'selected' : ''; ?>>Select your gender</option>
                                            <option value="m" <?php echo isset($gender) && $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="f" <?php echo isset($gender) && $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a gender</div>
                                    </div>
                                    <!-- Phone Number -->
                                    <div class="mb-3">
                                        <label for="phoneNumber" class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                                        <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>" placeholder="Enter your phone number" required>
                                        <div class="invalid-feedback">Please enter phone number</div>
                                    </div>
                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="Enter your email" required>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
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
                                        <div class="invalid-feedback">Please select nationality</div>
                                    </div>

                                    <!-- How Did You Hear About Us -->
                                    <div class="mb-3">
                                        <label for="referral" class="form-label"><i class="fas fa-info-circle"></i> How Did You Hear About Us?</label>
                                        <input type="text" class="form-control" id="referral" name="referral" value="<?php echo isset($referral) ? htmlspecialchars($referral) : ''; ?>" placeholder="E.g., Social Media, Friend, Website" required>
                                        <div class="invalid-feedback">Please tell us how you heard about us</div>
                                    </div>

                                    <div style="display: none" id="stripePayment">
                                        <div class="mb-3">
                                            <label for="card-element">Credit or Debit Card</label>
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <label for="cardName">Name on card</label>
                                                    <input type="text" class="form-control" name="cardName" id="card-name" placeholder="Enter card name" required />
                                                </div>
                                                <div class="col-12 mb-4">
                                                    <div id="card-element">
                                                        <!-- Stripe Elements will insert the card input here -->
                                                    </div>
                                                    <div class="invalid-feedback" id="card-errors" role="alert"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="d-grid">
                                            <button type="submit" id="stripePaymentButton" class="btn btn-lg">
                                                <span id="paymentButtonText"><i class="fas fa-paper-plane"></i> Proceed To Payment</span>
                                                <span id="loadingSpinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                                            </button>
                                        </div>
                                        <div class="" id="payment-result"></div>
                                    </div>
                                    <!-- Paystack Payment Section -->
                                    <div style="display: none" id="paystackPayment">
                                        <div class="d-grid">
                                            <button type="button" id="paystackPaymentButton" class="btn btn-lg" onclick="payWithPaystack()">
                                                <span id="paymentPaystackButtonText"><i class="fas fa-paper-plane"></i> Proceed To Payment</span>
                                                <span id="loadingPaystackSpinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- /Main Content -->

    <?php require_once('components/footer.inc.php'); ?>
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?>
    <!-- Add Paystack inline script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        // Define the payment amounts in Naira, Pounds, and Dollars
        const nairaAmount = 300000; // Naira
        const poundAmount = 150; // Pounds
        const dollarAmount = 180; // Dollars (adjust the amount as needed)

        document.addEventListener('DOMContentLoaded', function() {
            // Set the default currency to Pounds when the page loads
            updateCurrency();
        });

        function updateCurrency() {
            const currencySelector = document.getElementById("currencySelector");
            const paymentAmount = document.getElementById("paymentAmount");
            const amountInput = document.getElementById("amount");
            const currencyInput = document.getElementById("currency");

            if (currencySelector.value === "naira") {
                paymentAmount.innerHTML = `₦${nairaAmount.toLocaleString()}`;
                amountInput.value = nairaAmount;
                currencyInput.value = "naira";
                $('#stripePayment').hide();
                $('#paystackPayment').show();
            } else if (currencySelector.value === "pounds") {
                paymentAmount.innerHTML = `£${poundAmount.toLocaleString()}`;
                amountInput.value = poundAmount;
                currencyInput.value = "pounds";
                $('#paystackPayment').hide();
                $('#stripePayment').show();
            } else if (currencySelector.value === "dollars") {
                paymentAmount.innerHTML = `$${dollarAmount.toLocaleString()}`;
                amountInput.value = dollarAmount;
                currencyInput.value = "dollars";
                $('#paystackPayment').hide();
                $('#stripePayment').show();
            }
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Function to validate form
        function validateForm() {
            var isValid = true;
            var fields = ['firstName', 'lastName', 'gender', 'phoneNumber', 'email', 'nationality', 'referral'];

            fields.forEach(function(fieldId) {
                var field = document.getElementById(fieldId);
                if (field.value.trim() === '') {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Check if email format is valid
            var email = document.getElementById('email').value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('email').classList.add('is-invalid');
                isValid = false;
            }

            return isValid;
        }

        // Paystack fee calculation function
        function calculatePaystackFee(amount) {
            let paystackFee;
            if (amount <= 2500) {
                paystackFee = 0.015 * amount;
            } else {
                paystackFee = (0.015 * amount) + 100;
                if (paystackFee > 2000) {
                    paystackFee = 2000; // Max fee is 2000 NGN
                }
            }
            return paystackFee;
        }

        // Function to initiate Paystack payment
        function payWithPaystack() {
            if (!validateForm()) {
                return; // Stop the payment process if validation fails
            }

            // Disable the button and show the loading spinner
            var paymentButton = document.getElementById('paystackPaymentButton');
            var paymentButtonText = document.getElementById('paymentPaystackButtonText');
            var loadingSpinner = document.getElementById('loadingPaystackSpinner');
            paymentButton.disabled = true;
            paymentButtonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block'; // Show spinner

            var email = document.getElementById('email').value;
            var baseAmount = 300000;

            // Calculate Paystack fee and total amount
            var paystackFee = calculatePaystackFee(baseAmount);
            var totalAmount = baseAmount + paystackFee;
            var totalAmountInKobo = totalAmount * 100; // Convert to Kobo for Paystack

            var handler = PaystackPop.setup({
                key: document.getElementById('public_key').value,
                email: email,
                amount: totalAmountInKobo,
                currency: "NGN",
                ref: 'LREA' + Math.floor((Math.random() * 1000000000) + 1), // Generate random reference
                callback: function(response) {
                    // Payment successful
                    document.getElementById('reference').value = response.reference;
                    document.getElementById('paymentForm').submit(); // Submit the form to backend
                },
                onClose: function() {
                    alert('Transaction was not completed.'); // Payment window closed
                    // Re-enable the button and hide the spinner
                    paymentButton.disabled = false;
                    paymentButtonText.style.display = 'inline-block';
                    loadingSpinner.style.display = 'none';
                }
            });

            handler.openIframe(); // Open Paystack payment iframe
        }

        var stripe = Stripe(document.getElementById('st_public_key').value); // Replace with your publishable key
        var elements = stripe.elements();

        const style = {
            base: {
                color: '#32325d',
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#a0a0a0'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        var card = elements.create('card', {
            style
        });
        card.mount('#card-element');

        var form = document.getElementById('paymentForm');
        var resultContainer = document.getElementById('payment-result');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            if (!validateForm()) {
                return; // Stop the payment process if validation fails
            }

            // Disable the button and show the loading spinner
            var paymentButton = document.getElementById('stripePaymentButton');
            var paymentButtonText = document.getElementById('paymentButtonText');
            var loadingSpinner = document.getElementById('loadingSpinner');
            paymentButton.disabled = true;
            paymentButtonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block'; // Show spinner

            var selectedAmount;
            var selectedCurrency;
            // Determine the selected currency and amount
            const currency = document.getElementById("currency").value;
            if (currency === "dollars") {
                selectedAmount = 18000; // Amount in cents for USD ($180)
                selectedCurrency = 'usd';
            } else if (currency === 'pounds') {
                selectedAmount = 15000; // Amount in pence for GBP (£150)
                selectedCurrency = 'gbp';
            }

            var email = document.getElementById('email').value;
            var name = document.getElementById('card-name').value;

            try {
                const result = await stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                });

                if (result.error) {
                    resultContainer.className = 'alert alert-danger mt-3'; // Set alert to danger
                    resultContainer.textContent = result.error.message;
                    paymentButton.disabled = false;
                    paymentButtonText.style.display = 'inline-block';
                    loadingSpinner.style.display = 'none';
                } else {
                    // Pass the PaymentMethod ID to your server to create a charge
                    const response = await fetch('create-payment-intent.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            payment_method_id: result.paymentMethod.id,
                            email: email,
                            name: name,
                            amount: selectedAmount,
                            currency: selectedCurrency
                        }),
                    });

                    const responseJson = await response.json();

                    if (responseJson.error) {
                        resultContainer.className = 'alert alert-danger mt-3'; // Set alert to danger
                        resultContainer.textContent = responseJson.error;
                        paymentButton.disabled = false;
                        paymentButtonText.style.display = 'inline-block';
                        loadingSpinner.style.display = 'none';
                    } else {
                        resultContainer.className = 'alert alert-success mt-3'; // Set alert to success
                        resultContainer.textContent = 'Payment successful!';
                        document.getElementById('reference').value = responseJson.reference; // Assuming you return reference from the server
                        form.submit(); // Submit the form to backend
                    }
                }
            } catch (error) {
                resultContainer.className = 'alert alert-danger mt-3'; // Set alert to danger
                resultContainer.textContent = 'There was an error processing your payment. Please try again.';
                paymentButton.disabled = false;
                paymentButtonText.style.display = 'inline-block';
                loadingSpinner.style.display = 'none';
                console.error('Payment error:', error);
            }
        });
    </script>

</body>

</html>