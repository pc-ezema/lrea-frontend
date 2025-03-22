<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');
require_once(ROOT_PATH_TO_PORTAL . 'PHPMailer/PHPMailerAutoload.php');

// Paystack public key
$paystack_public_key = "pk_test_dafbbf580555e2e2a10a8d59c6157b328192334d"; // Replace with your actual public key
$paystack_secret_key = "sk_test_53668873c7cbb5572b4156b133ab8ee8030e7f59";

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_begin_transaction($config) or die(mysqli_error($config)); // Start transaction

    $all_went_well = false;
    // $time_now = date('Y-m-d H:i:s');
    // $date_now = date('Y-m-d');

    // Verify the payment with Paystack
    if (isset($_POST['reference'])) {
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
                                    // Get all programs of this subscription that have a module
                                    $query_SubscriptionProgrammes = "SELECT sp.*
                                        FROM subscriptions_programmes sp
                                        JOIN modules m ON sp.prog_id = m.prog_id
                                        WHERE sp.sub_id = " . sql_int($sub_id);
                                    $SubscriptionProgrammes = mysqli_query($config, $query_SubscriptionProgrammes) or die(mysqli_error($config));
                                    $row_SubscriptionProgrammes = mysqli_fetch_assoc($SubscriptionProgrammes);
                                    $totalRows_SubscriptionProgrammes = mysqli_num_rows($SubscriptionProgrammes);

                                    if ($totalRows_SubscriptionProgrammes) {
                                        do {
                                            // Check if there's any existing prospect info
                                            mysqli_select_db($config, $database_config);
                                            $query_LastInfo = "SELECT * 
                                                                FROM prospects p 
                                                                WHERE p.email = '" . sql_string($email) . "' 
                                                                ORDER BY p.p_id DESC
                                                                LIMIT 1";
                                            $LastInfo = mysqli_query($config, $query_LastInfo) or die(mysqli_error($config));
                                            $row_LastInfo = mysqli_fetch_assoc($LastInfo);
                                            $totalRows_LastInfo = mysqli_num_rows($LastInfo);

                                            if ($totalRows_LastInfo) {
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
                                                            prospect_title, 
                                                            full_name, 
                                                            first_name, 
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
                                                            emp_company, 
                                                            emp_position, 
                                                            no_followup_emails, 
                                                            country_of_residence,
                                                            photo,
                                                            student_number
                                                        ) VALUES (
                                                            21,
                                                            '" . sql_string($row_LastInfo['prospect_title']) . "',
                                                            '" . sql_string(trim($row_LastInfo['full_name'])) . "',
                                                            '" . sql_string(trim($row_LastInfo['first_name'])) . "',
                                                            '" . sql_string(trim($row_LastInfo['last_name'])) . "',
                                                            '" . sql_string($row_LastInfo['email']) . "',
                                                            '" . sql_string($row_LastInfo['phone']) . "',
                                                            '" . sql_string($row_LastInfo['gender']) . "',
                                                            " . sql_int($row_LastInfo['nationality']) . ",
                                                            " . sql_int($row_SubscriptionProgrammes['prog_id']) . ",
                                                            " . $prog_date . ",
                                                            0,
                                                            '" . sql_string($time_now) . "',
                                                            0,
                                                            '" . $newStartDate . "',
                                                            '" . $newEndDate . "',
                                                            95,
                                                            1,
                                                            '" . sql_string($row_LastInfo['emp_company']) . "',
                                                            " . sql_int($row_LastInfo['emp_position']) . ", 
                                                            '1',
                                                            " . sql_int($row_LastInfo['country_of_residence']) . ",
                                                            '" . sql_string($row_LastInfo['photo']) . "',
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

                                        mysqli_select_db($config, $database_config);
                                        $updateSQL = "UPDATE users_students 
                                                    SET adsStatus = 1, 
                                                        modified_date = '" . sql_string(date("Y-m-d H:i:s")) . "'
                                                    WHERE email = '$email_safe' 
                                                    LIMIT 1";
                                        $updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));

                                        $all_went_well = true;

                                        // Construct the email message
                                        $subject = "Welcome to LREA!";

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
                                            '{{current_year}}' => date("Y"),
                                            '{{start_date}}' => $formattedCurrentDate  // Add the formatted start date
                                        );

                                        foreach ($placeholders as $placeholder => $value) {
                                            $template = str_replace($placeholder, $value, $template);
                                        }

                                        // Send the email using PHPMailer
                                        $mail = new PHPMailer();
                                        $mail->IsSMTP();
                                        $mail->SMTPAuth = true;
                                        // $mail->Host = 'mail.lrea.co.uk';
                                        // $mail->Port = 465;
                                        // $mail->SMTPSecure = 'SSL';
                                        // $mail->Username = 'noreply@lrea.co.uk';
                                        // $mail->Password = '!Pg0k%tPZhvS';

                                        $mail->Host = 'sandbox.smtp.mailtrap.io';
                                        $mail->Port = 2525;
                                        $mail->SMTPSecure = 'TLS';
                                        $mail->Username = '011a587122008d';
                                        $mail->Password = '65b15e1a2f0e29';

                                        $mail->SMTPOptions = array(
                                            'ssl' => array(
                                                'verify_peer' => false,
                                                'verify_peer_name' => false,
                                                'allow_self_signed' => true
                                            )
                                        );

                                        $mail->IsHTML(true);
                                        $mail->SetFrom('noreply@lrea.co.uk', 'LREA');
                                        $mail->AddReplyTo('info@lrea.co.uk', 'LREA');
                                        $mail->Subject = $subject;
                                        $mail->MsgHTML($template);
                                        $mail->AltBody = $template;  // Plain text version for non-HTML email clients

                                        $mail->AddAddress($email_safe);  // Send to the registered email

                                        // Attempt to send the email
                                        if (!$mail->Send()) {
                                            // Handle email send failure (optional)
                                            error_log("Failed to send registration email to " . $email_safe);
                                        }
                                    }
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
                }

                if ($all_went_well) {
                    mysqli_commit($config) or die(mysqli_error($config));

                    $_SESSION['userfeedback'] = "Your payment was successful! The courses have been assigned to your account. Kindly log in to your dashboard to start learning.";
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
    }
}

$page_title = "Landing Page";
require_once('components/doctype.inc.php');
?>

<style>
    .banner {
        background-image: url("https://lrea.co.uk/images/subscribe-banner.jpg");
        background-size: cover;
        background-position: center;
        color: white;
        border-radius: 15px;
        padding: 40px 20px;
        text-align: center;
        width: 100%;
        max-width: 1200px;
        margin: 20px auto;
        position: relative;
        overflow: hidden;
        /* Ensures pseudo-element doesn't overflow */
    }

    .banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        /* Dark overlay */
        z-index: 0;
    }

    .banner-content {
        position: relative;
        z-index: 1;
        /* Content stays above the dark overlay */
    }

    .banner h1 {
        font-size: 3rem;
        font-weight: 600;
        margin-bottom: 20px;
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        color: #f1c40f;
    }

    .banner p {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }

    .fee-info {
        background-color: rgba(0, 0, 0, 0.7);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        color: #ffffff;
    }

    .fee-info h2 {
        font-size: 2.5rem;
        color: #f1c40f;
        margin-bottom: 10px;
    }

    .fee-info p {
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .btn-payment {
        background-color: #ea383e;
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
        margin-bottom: 30px;
    }

    .btn-payment:hover {
        background-color: #000;
        color: #fff
    }

    .benefits-section {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        margin: 20px auto;
    }

    .benefits-section h2 {
        font-size: 2.5rem;
        font-weight: 600;
        color: #ea383e;
        margin-bottom: 20px;
        text-align: center;
    }

    .benefits-section p {
        font-size: 1.2rem;
        font-weight: 600;
        color: #000;
        margin-bottom: 20px;
        text-align: center;
    }

    .benefits-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .benefits-list li {
        margin-bottom: 15px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .benefits-list li i {
        color: #ea383e;
        font-size: 1.5rem;
        margin-right: 15px;
    }

    .benefits-list li .content {
        font-size: 1.2rem;
    }

    .benefits-list li:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    /* PARTICLES */
    .particles {
        position: absolute;
        display: flex;
        width: 100%;
        padding: 0 10px;
        z-index: 1;
        /* Ensure particles go behind other sections */
        /* top: 0; */
        bottom: 1000px;
        /* Position particles correctly */
    }

    .particles li {
        position: relative;
        bottom: 30px;
        width: 30px;
        height: 30px;
        background: #7edd90;
        box-shadow: 0 0 0 10px #7edd9044, 0 0 50px #7edd90, -100px 0 #475c9a99,
            100px 0 #475c9a99;
        margin: 0 4px;
        border-radius: 50%;
        animation: particles-animation calc(190s / var(--i)) ease infinite;
    }

    .particles li:nth-child(2n) {
        background: #475c9a;
        box-shadow: 0 0 0 10px #475c9a44, 0 0 50px #475c9a, -100px 0 #7edd9099,
            100px 0 #7edd9099;
    }

    .particles li:nth-child(3n) {
        background: #9a4772;
        box-shadow: 0 0 0 10px #9a477244, 0 0 50px #9a4772, -100px 0 #7edd9099,
            100px 0 #7edd9099;
    }

    @keyframes particles-animation {
        0% {
            transform: translateY(120vh) scale(0) rotate(180deg);
        }

        20% {
            transform: translateY(100vh) scale(1) rotate(0deg);
        }

        100% {
            transform: translateY(-120vh) scale(0.5) rotate(360deg);
        }
    }

    strong {
        font-family: "SoDoSans Bold", Arial, Helvetica, sans-serif;
    }
</style>

<?php include_once('components/end-of-head.inc.php'); ?>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>
    <?php include_once('components/beginning-of-body.inc.php'); ?>
    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>

    <div class="main-content">
        <!-- <ul class="particles">
            <li style="--i: 11"></li>
            <li style="--i: 12"></li>
            <li style="--i: 13"></li>
            <li style="--i: 14"></li>
            <li style="--i: 15"></li>
            <li style="--i: 16"></li>
            <li style="--i: 17"></li>
            <li style="--i: 18"></li>
            <li style="--i: 19"></li>
            <li style="--i: 20"></li>
            <li style="--i: 21"></li>
        </ul> -->
        <div class="container mt-5">
            <div class="banner">
                <div class="banner-content">
                    <h1>LREA Membership Benefits</h1>
                    <p>By becoming a member, you'll not only receive your certificate but also gain access to a world of benefits</p>
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
                    <div class="fee-info">
                        <h2>Limited Time Offer!</h2>
                        <p>
                            And the best part? You can become a member at a discounted fee of
                            <strong>N300,000</strong> only! 🎉
                            <br />
                            Offers end at 11:59 pm on December 31st. ⏰
                        </p>
                        <!-- Add id="paymentForm" to the form -->
                        <!-- <form id="paymentForm" action="" method="POST">
                            <input type="hidden" id="email" value="<?php echo !empty($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                            <input type="hidden" id="public_key" value="<?php echo $paystack_public_key; ?>">
                            <input type="hidden" id="reference" name="reference">

                            <button type="button" id="paymentButton" class="btn-payment" onclick="payWithPaystack()">
                                <span id="paymentButtonText"><i class="fas fa-paper-plane"></i> Proceed To Payment Of ₦300,000</span>
                                <span id="loadingSpinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                            </button>
                        </form> -->
                        <a href="<?php echo $link_root_path; ?>payment" class="btn-payment text-decoration-none">Proceed to Payment</a>

                    </div>
                </div>
            </div>
            <div class="benefits-section">
                <h2>LREA Membership Benefits</h2>
                <p>As a member, you will enjoy the following exclusive advantages:</p>
                <ul class="benefits-list">
                    <li>
                        <i class="fas fa-user-plus"></i>
                        <div class="content">
                            <h5>Personal Development Benefits:</h5>
                            <ul>
                                <li><strong>Exclusive Mentorship Programs:</strong> Gain personalized guidance from seasoned industry professionals who provide one-on-one mentorship to help you achieve your goals.</li>
                                <li><strong>Tailored Career Pathways:</strong> Receive expert advice and support on charting the course of your career with our range of educational offerings and industry connections.</li>
                                <li><strong>Personal Growth Resources:</strong> Access a library of resources focused on personal development, including books, articles, and online courses.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-briefcase"></i>
                        <div class="content">
                            <h5>Career Development Benefits:</h5>
                            <ul>
                                <li><strong>UK Professional Qualification and Certification Advantage:</strong> Boost your credentials and career advantage with our internationally recognized LREA certificates, fast-track your professional qualification, and open doors for opportunities in the UK and beyond.</li>
                                <li><strong>Professional Endorsement Letter:</strong> Elevate your career by leveraging official letters of recommendation to support your growth and credibility in your industry.</li>
                                <li><strong>Visa Application Support:</strong> Increase your chances of success with LREA endorsement letters for visa and contract applications.</li>
                                <li><strong>Career Counseling:</strong> One-on-one career counseling sessions to help you make informed career decisions.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-network-wired"></i>
                        <div class="content">
                            <h5>Networking Benefits:</h5>
                            <ul>
                                <li><strong>Global Networking Opportunities:</strong> Connect with a diverse membership base of professionals from various sectors and industries, fostering collaboration and partnership.</li>
                                <li><strong>VVIP Event Invitations:</strong> Attend high-profile events in Lagos, London, and beyond, where you can engage with industry leaders, innovators, and peers.</li>
                                <li><strong>Opportunities for Partnerships:</strong> Unlock collaborations with fellow professionals and access potential business opportunities within our global network.</li>
                                <li><strong>Access to Exclusive Club:</strong> Enjoy access to a prestigious club in Victoria Island for elite networking and collaboration opportunities with other LREA members.</li>
                                <li><strong>Annual London Trip:</strong> Attend the prestigious LREA conference, connect with global professionals, and expand your network while gaining insights from top industry leaders worldwide.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-hands-helping"></i>
                        <div class="content">
                            <h5>Support Benefits:</h5>
                            <ul>
                                <li><strong>Yearly Raffle Draws:</strong> Enter for a chance to win an all-expense-paid trip to the UK, exclusive for our members.</li>
                                <li><strong>Visa Application Assistance:</strong> Enhance your application for UK visas or work contracts with our official support.</li>
                                <li><strong>Personalized Support:</strong> Tailored support to help you achieve your professional goals.</li>
                                <li><strong>Member Assistance Programs:</strong> Access various support programs to assist you in your professional journey.</li>
                                <li><strong>Community Support:</strong> Join a supportive community of like-minded professionals.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-book-open"></i>
                        <div class="content">
                            <h5>Learning Benefits:</h5>
                            <ul>
                                <li><strong>Personalized Mentorship Programs:</strong> Benefit from one-on-one mentorship tailored to your professional needs.</li>
                                <li><strong>Access to Educational Resources:</strong> Comprehensive access to a wide range of educational materials, including online courses, webinars, and workshops.</li>
                                <li><strong>Invitations to Q&A Sessions with Industry Leaders:</strong> Gain insights and advice directly from experts in the field through exclusive interview sessions and panel discussions.</li>
                                <li><strong>Interdisciplinary Collaboration:</strong> Opportunities to collaborate with professionals from various sectors and disciplines.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-star"></i>
                        <div class="content">
                            <h5>Additional Benefits:</h5>
                            <ul>
                                <li><strong>Membership Badge:</strong> Proudly display your LREA affiliation with an official annual badge.</li>
                                <li><strong>Exclusive Member Discounts:</strong> Enjoy discounts on various professional development courses and events.</li>
                                <li><strong>Diverse Membership Base:</strong> Connect with professionals across various industries—LREA membership is open to all sectors.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-graduation-cap"></i>
                        <div class="content">
                            <h5>Training and Education:</h5>
                            <p style="text-align: left;">We offer internationally recognized diplomas and executive education courses tailored to your needs. Whether you’re starting out or looking to advance your career, our educational offerings are designed to empower you with the knowledge and skills needed to thrive.</p>
                            <ul>
                                <li><strong>Affordable Pathway Programs:</strong> Access high-quality, OFQUAL-regulated diplomas that can lead to degrees from top UK universities.</li>
                                <li><strong>Diverse Learning Opportunities:</strong> Explore flexible programs that are accessible from anywhere, catering to various sectors and industries.</li>
                                <li><strong>Continuous Learning:</strong> Stay updated with exclusive Q&A sessions, interview panels, and webinars featuring industry experts.</li>
                            </ul>
                            <a href="<?php echo ROOT_DIR.$location_slug; ?>category/short-courses" class="btn-payment text-decoration-none">Explore Courses</a> <!-- This link navigates to the Courses section -->
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-tools"></i>
                        <div class="content">
                            <h5>Resources:</h5>
                            <p style="text-align: left;">Our comprehensive resources provide the foundation for your personal and professional growth. As a member, you will have exclusive access to tools that can shape your career and open doors to new opportunities.</p>
                            <ul>
                                <li><strong>Industry Insights and Updates:</strong> Stay ahead with the latest trends and developments in your field through regular newsletters and market analysis.</li>
                                <li><strong>Professional Endorsement:</strong> Receive letters of recommendation for career advancement, visa applications, or contract bids.</li>
                                <li><strong>Exclusive Mailing List:</strong> Get updates from industry leaders that can keep you motivated and informed.</li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-handshake"></i>
                        <div class="content">
                            <strong>At LREA, we are committed to your success. We offer everything you need to enhance your career and personal growth. Joining LREA means becoming part of a global community dedicated to excellence, innovation, and collaboration. Start your journey with us today and unlock your full potential!</strong>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- /Main Content -->

    <?php require_once('components/footer.inc.php'); ?>
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?>

    <!-- Add Paystack inline script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>

    <script>
        // Function to initiate Paystack payment
        function payWithPaystack() {
            // Disable the button and show the loading spinner
            var paymentButton = document.getElementById('paymentButton');
            var paymentButtonText = document.getElementById('paymentButtonText');
            var loadingSpinner = document.getElementById('loadingSpinner');
            paymentButton.disabled = true;
            paymentButtonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block'; // Show spinner

            var email = document.getElementById('email').value;
            var amount = 300000 * 100;

            var handler = PaystackPop.setup({
                key: document.getElementById('public_key').value,
                email: email,
                amount: amount,
                currency: "NGN",
                ref: '' + Math.floor((Math.random() * 1000000000) + 1), // Generate random reference
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
    </script>
</body>

</html>